<?php

namespace Botble\CarRentals\Http\Controllers\API\Vendor;

use Botble\Api\Http\Controllers\BaseApiController;
use Botble\CarRentals\Enums\BookingStatusEnum;
use Botble\CarRentals\Models\Booking;
use Botble\CarRentals\Models\BookingCar;
use Botble\CarRentals\Models\Car;
use Botble\CarRentals\Models\CarReview;
use Botble\CarRentals\Models\Revenue;
use Botble\CarRentals\Services\HostAnalyticsService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends BaseApiController
{
    /**
     * Get vendor dashboard data
     *
     * @group Car Rentals - Vendor
     */
    public function index(Request $request, HostAnalyticsService $analyticsService)
    {
        $vendor = Auth::guard('sanctum')->user();
        $vendorId = $vendor->id;

        // --- NEW: Calculate standard 30-day window for Pro Analytics ---
        $startDate = Carbon::now()->subDays(30)->startOfDay();
        $endDate = Carbon::now()->endOfDay();

        $financials = $analyticsService->getFinancialMetrics($vendorId, $startDate, $endDate);
        $utilizationRate = $analyticsService->getFleetUtilization($vendorId, $startDate, $endDate);
        $conversionStats = $analyticsService->getConversionStats($vendorId);
        // ---------------------------------------------------------------

        // Get basic statistics
        $totalCars = Car::where('vendor_id', $vendorId)->count();
        $activeCars = Car::where('vendor_id', $vendorId)
            ->where('status', 'available')
            ->count();

        $totalBookings = Booking::where('vendor_id', $vendorId)->count();
        $pendingBookings = Booking::where('vendor_id', $vendorId)
            ->where('status', BookingStatusEnum::PENDING)
            ->count();
        $confirmedBookings = Booking::where('vendor_id', $vendorId)
            ->where('status', BookingStatusEnum::CONFIRMED)
            ->count();
        $completedBookings = Booking::where('vendor_id', $vendorId)
            ->where('status', BookingStatusEnum::COMPLETED)
            ->count();

        $totalReviews = CarReview::whereHas('car', function ($query) use ($vendorId): void {
            $query->where('vendor_id', $vendorId);
        })->count();

        $averageRating = CarReview::whereHas('car', function ($query) use ($vendorId): void {
            $query->where('vendor_id', $vendorId);
        })->avg('star') ?? 0;

        // Get recent bookings
        $recentBookings = Booking::where('vendor_id', $vendorId)
            ->with(['car.car', 'customer'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($booking) {
                return [
                    'id' => $booking->id,
                    'booking_number' => $booking->booking_number,
                    'customer_name' => $booking->customer_name,
                    'car_name' => $booking->car->car->name ?? 'N/A',
                    'amount' => $booking->amount,
                    'status' => $booking->status,
                    'created_at' => $booking->created_at,
                ];
            });

        // Get monthly revenue for current year
        $monthlyRevenue = Revenue::where('vendor_id', $vendorId)
            ->whereYear('created_at', Carbon::now()->year)
            ->select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(amount) as total')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->pluck('total', 'month')
            ->toArray();

        // Fill missing months with 0
        $revenueData = [];
        for ($i = 1; $i <= 12; $i++) {
            $revenueData[] = [
                'month' => Carbon::create()->month($i)->format('M'),
                'revenue' => $monthlyRevenue[$i] ?? 0,
            ];
        }

        return $this
            ->httpResponse()
            ->setData([
                'statistics' => [
                    'total_cars' => $totalCars,
                    'active_cars' => $activeCars,
                    'total_bookings' => $totalBookings,
                    'pending_bookings' => $pendingBookings,
                    'confirmed_bookings' => $confirmedBookings,
                    'completed_bookings' => $completedBookings,
                    'total_reviews' => $totalReviews,
                    'average_rating' => round($averageRating, 1),
                    
                    // --- NEW: Added Pro Metrics for Mobile App ---
                    'fleet_utilization_percent' => $utilizationRate,
                    'booking_conversion_percent' => $conversionStats['conversion_rate'],
                    'total_profile_views' => $conversionStats['total_views'],
                ],
                // --- NEW: Added Financial Breakdown ---
                'financials_last_30_days' => [
                    'gross_revenue' => $financials['gross_revenue'],
                    'net_payout' => $financials['net_payout'],
                    'platform_fees' => $financials['platform_fees'],
                ],
                'recent_bookings' => $recentBookings,
                'monthly_revenue' => $revenueData,
            ])
            ->toApiResponse();
    }

    /**
     * Get vendor revenue data
     *
     * @group Car Rentals - Vendor
     */
    public function getRevenue(Request $request)
    {
        $vendor = Auth::guard('sanctum')->user();

        $request->validate([
            'period' => ['nullable', 'in:week,month,year'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
        ]);

        $period = $request->input('period', 'month');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Set default date range based on period
        if (! $startDate || ! $endDate) {
            switch ($period) {
                case 'week':
                    $startDate = Carbon::now()->startOfWeek();
                    $endDate = Carbon::now()->endOfWeek();

                    break;
                case 'year':
                    $startDate = Carbon::now()->startOfYear();
                    $endDate = Carbon::now()->endOfYear();

                    break;
                default: // month
                    $startDate = Carbon::now()->startOfMonth();
                    $endDate = Carbon::now()->endOfMonth();

                    break;
            }
        }

        $query = Revenue::where('vendor_id', $vendor->id)
            ->whereBetween('created_at', [$startDate, $endDate]);

        $totalRevenue = $query->sum('amount');
        $totalBookings = $query->count();

        // Get revenue breakdown by period
        $revenueBreakdown = $query
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(amount) as total'),
                DB::raw('COUNT(*) as bookings')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return $this
            ->httpResponse()
            ->setData([
                'period' => $period,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'total_revenue' => $totalRevenue,
                'total_bookings' => $totalBookings,
                'average_booking_value' => $totalBookings > 0 ? $totalRevenue / $totalBookings : 0,
                'revenue_breakdown' => $revenueBreakdown,
            ])
            ->toApiResponse();
    }

    /**
     * Get vendor statistics
     *
     * @group Car Rentals - Vendor
     */
    public function getStatistics(Request $request)
    {
        $vendor = Auth::guard('sanctum')->user();

        $request->validate([
            'period' => ['nullable', 'in:week,month,year'],
        ]);

        $period = $request->input('period', 'month');

        // Set date range based on period
        switch ($period) {
            case 'week':
                $startDate = Carbon::now()->startOfWeek();
                $endDate = Carbon::now()->endOfWeek();

                break;
            case 'year':
                $startDate = Carbon::now()->startOfYear();
                $endDate = Carbon::now()->endOfYear();

                break;
            default: // month
                $startDate = Carbon::now()->startOfMonth();
                $endDate = Carbon::now()->endOfMonth();

                break;
        }

        // Booking statistics
        $bookingStats = Booking::where('vendor_id', $vendor->id)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status')
            ->toArray();

        // Car performance
        $carPerformance = Car::where('vendor_id', $vendor->id)
            ->withCount(['bookings' => function ($query) use ($startDate, $endDate): void {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            }])
            ->orderBy('bookings_count', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($car) {
                return [
                    'id' => $car->id,
                    'name' => $car->name,
                    'bookings_count' => $car->bookings_count,
                    'avg_rating' => $car->avg_review,
                ];
            });

        // Review statistics
        $reviewStats = CarReview::whereHas('car', function ($query) use ($vendor): void {
            $query->where('vendor_id', $vendor->id);
        })
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select('star', DB::raw('COUNT(*) as count'))
            ->groupBy('star')
            ->get()
            ->pluck('count', 'star')
            ->toArray();

        return $this
            ->httpResponse()
            ->setData([
                'period' => $period,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'booking_statistics' => $bookingStats,
                'car_performance' => $carPerformance,
                'review_statistics' => $reviewStats,
            ])
            ->toApiResponse();
    }

    /**
     * NEW: Get Fleet Calendar Events for Mobile App
     *
     * @group Car Rentals - Vendor
     */
    public function getFleetCalendarEvents(Request $request)
    {
        $vendorId = Auth::guard('sanctum')->id();
        
        $request->validate([
            'start' => ['required', 'date'],
            'end' => ['required', 'date'],
        ]);

        $start = $request->input('start');
        $end = $request->input('end');

        $bookings = BookingCar::query()
            ->with(['booking', 'car'])
            ->whereHas('car', function ($query) use ($vendorId) {
                $query->where('author_id', $vendorId);
            })
            ->whereBetween('rental_start_date', [$start, $end])
            ->get();

        $events = $bookings->map(function ($item) {
            $carName = $item->car->name ?? $item->car->license_plate ?? 'Car #' . $item->car_id;
            
            return [
                'booking_id' => $item->booking->id,
                'car_id' => $item->car_id,
                'car_name' => $carName,
                'customer_name' => $item->booking->customer_name,
                'start_date' => $item->rental_start_date->toIso8601String(),
                'end_date' => $item->rental_end_date->toIso8601String(),
                'status' => $item->booking->status,
                'amount' => (float) $item->booking->amount,
            ];
        });

        return $this
            ->httpResponse()
            ->setData($events)
            ->toApiResponse();
    }


    public function getFleetLocations(Request $request)
    {
        $vendorId = Auth::guard('sanctum')->id();

        // Get all cars for this vendor that have a registered tracker
        $cars = Car::query()
            ->where('author_id', $vendorId)
            ->where('author_type', \Botble\CarRentals\Models\Customer::class)
            ->whereNotNull('telematics_device_id')
            ->get();

        $locations = $cars->map(function ($car) {
            // Fetch the most recent ping for this specific car
            $lastLog = \Botble\CarRentals\Models\VehicleTelematicsLog::where('car_id', $car->id)
                ->latest()
                ->first();

            // Skip if the car has never sent a GPS ping
            if (!$lastLog || $lastLog->latitude === null) {
                return null;
            }

            return [
                'car_id' => $car->id,
                'name' => $car->name,
                'license_plate' => $car->license_plate,
                'image_url' => \RvMedia::getImageUrl($car->image, 'thumb', false, \RvMedia::getDefaultImage()),
                'coordinates' => [
                    'latitude' => (float) $lastLog->latitude,
                    'longitude' => (float) $lastLog->longitude,
                ],
                'telematics' => [
                    'speed_mph' => (float) $lastLog->speed_mph,
                    'odometer_miles' => (float) $lastLog->odometer_miles,
                    'fuel_percentage' => (float) $lastLog->fuel_percentage,
                    'event_type' => $lastLog->event_type,
                    'status_color' => $this->getMapMarkerColor($lastLog), 
                    'last_updated' => $lastLog->created_at->toIso8601String(),
                    'last_updated_human' => $lastLog->created_at->diffForHumans(),
                ]
            ];
        })->filter()->values(); // Remove nulls and re-index array

        return $this
            ->httpResponse()
            ->setData($locations)
            ->toApiResponse();
    }

    /**
     * Helper method to determine the marker color for the mobile app
     */
    protected function getMapMarkerColor($log): string
    {
        if (!$log) return 'gray';
        if ($log->event_type === 'geofence_exit') return 'red';
        if ($log->speed_mph > 80) return 'orange';
        return 'green';
    }

    
    public function getTelematicsLogs(Request $request)
    {
        $vendorId = Auth::guard('sanctum')->id();

        $query = \Botble\CarRentals\Models\VehicleTelematicsLog::query()
            ->with(['car' => function($q) {
                $q->select('id', 'name', 'license_plate');
            }])
            ->whereHas('car', function ($q) use ($vendorId) {
                $q->where('author_id', $vendorId)
                  ->where('author_type', \Botble\CarRentals\Models\Customer::class);
            });

        // Optional filter: By specific car
        if ($request->filled('car_id')) {
            $query->where('car_id', $request->input('car_id'));
        }

        // Optional filter: By event type (e.g., 'speeding', 'geofence_exit')
        if ($request->filled('event_type')) {
            $query->where('event_type', $request->input('event_type'));
        }

        $logs = $query->latest()->paginate($request->input('per_page', 20));

        // Format the collection inside the paginator for mobile consumption
        $logs->getCollection()->transform(function ($log) {
            return [
                'id' => $log->id,
                'car_id' => $log->car_id,
                'car_name' => $log->car->name ?? 'Unknown',
                'license_plate' => $log->car->license_plate ?? '',
                'event_type' => $log->event_type,
                'event_label' => ucfirst(str_replace('_', ' ', $log->event_type)),
                'speed_mph' => (float) $log->speed_mph,
                'odometer_miles' => (float) $log->odometer_miles,
                'coordinates' => [
                    'latitude' => (float) $log->latitude,
                    'longitude' => (float) $log->longitude,
                ],
                'created_at' => $log->created_at->toIso8601String(),
                'formatted_time' => $log->created_at->format('M d, Y h:i A'),
                // Tell the app if this should be highlighted in red/orange
                'is_alert' => in_array($log->event_type, ['speeding', 'geofence_exit', 'hard_braking'])
            ];
        });

        return $this
            ->httpResponse()
            ->setData($logs)
            ->toApiResponse();
    }
}
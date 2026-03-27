<?php

namespace Botble\CarRentals\Http\Controllers\API\Vendor;

use Botble\Api\Http\Controllers\BaseApiController;
use Botble\CarRentals\Enums\BookingStatusEnum;
use Botble\CarRentals\Models\Booking;
use Botble\CarRentals\Models\Car;
use Botble\CarRentals\Models\CarReview;
use Botble\CarRentals\Models\Revenue;
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
    public function index()
    {
        $vendor = Auth::guard('sanctum')->user();

        // Get basic statistics
        $totalCars = Car::where('vendor_id', $vendor->id)->count();
        $activeCars = Car::where('vendor_id', $vendor->id)
            ->where('status', 'available')
            ->count();

        $totalBookings = Booking::where('vendor_id', $vendor->id)->count();
        $pendingBookings = Booking::where('vendor_id', $vendor->id)
            ->where('status', BookingStatusEnum::PENDING)
            ->count();
        $confirmedBookings = Booking::where('vendor_id', $vendor->id)
            ->where('status', BookingStatusEnum::CONFIRMED)
            ->count();
        $completedBookings = Booking::where('vendor_id', $vendor->id)
            ->where('status', BookingStatusEnum::COMPLETED)
            ->count();

        $totalReviews = CarReview::whereHas('car', function ($query) use ($vendor): void {
            $query->where('vendor_id', $vendor->id);
        })->count();

        $averageRating = CarReview::whereHas('car', function ($query) use ($vendor): void {
            $query->where('vendor_id', $vendor->id);
        })->avg('star') ?? 0;

        // Get recent bookings
        $recentBookings = Booking::where('vendor_id', $vendor->id)
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
        $monthlyRevenue = Revenue::where('vendor_id', $vendor->id)
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
}

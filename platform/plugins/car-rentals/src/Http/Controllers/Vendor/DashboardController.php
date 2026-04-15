<?php

namespace Botble\CarRentals\Http\Controllers\Vendor;

use Botble\Base\Facades\Assets;
use Botble\Base\Http\Controllers\BaseController;
use Botble\CarRentals\Enums\BookingStatusEnum;
use Botble\CarRentals\Enums\RevenueTypeEnum;
use Botble\CarRentals\Facades\CarRentalsHelper;
use Botble\CarRentals\Models\Booking;
use Botble\CarRentals\Models\BookingCar;
use Botble\CarRentals\Models\Car;
use Botble\CarRentals\Models\CarReview;
use Botble\CarRentals\Models\Customer;
use Botble\CarRentals\Models\Message;
use Botble\CarRentals\Models\Revenue;
use Botble\CarRentals\Models\Withdrawal;
use Botble\Media\Chunks\Exceptions\UploadMissingFileException;
use Botble\Media\Chunks\Handler\DropZoneUploadHandler;
use Botble\Media\Chunks\Receiver\FileReceiver;
use Botble\Media\Facades\RvMedia;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;

class DashboardController extends BaseController
{
    public function index(Request $request, \Botble\CarRentals\Services\HostAnalyticsService $analyticsService)
    {
        $this->pageTitle(__('Dashboard'));

        $vendorId = auth('customer')->id();

        $totalCars = Car::query()->where('author_type', Customer::class)->where('author_id', $vendorId)->count();
        $totalBookings = Booking::query()->where('vendor_id', $vendorId)->count();
        $totalMessages = Message::query()->where('vendor_id', $vendorId)->count();

        [$startDate, $endDate] = CarRentalsHelper::getDateRangeInReport($request);
        $predefinedRange = $request->input('date_range', trans('plugins/car-rentals::reports.ranges.last_30_days'));

        // --- NEW: Advanced Analytics ---
        $financials = $analyticsService->getFinancialMetrics($vendorId, $startDate, $endDate);
        $utilizationRate = $analyticsService->getFleetUtilization($vendorId, $startDate, $endDate);
        
        // --- NEW: Fetch Conversion Stats ---
        $conversionStats = $analyticsService->getConversionStats($vendorId);

        // Recent bookings
        $bookings = Booking::query()
            ->where('vendor_id', $vendorId)
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->latest()
            ->with(['car', 'car.car'])
            ->limit(10)
            ->get();

        // Top performing cars
        $topCars = Car::query()
            ->where('author_type', Customer::class)
            ->where('author_id', $vendorId)
            ->select('cr_cars.*')
            ->selectRaw('(
                SELECT COUNT(*)
                FROM cr_bookings
                INNER JOIN cr_booking_cars ON cr_booking_cars.booking_id = cr_bookings.id
                WHERE cr_cars.id = cr_booking_cars.car_id
                AND cr_bookings.created_at >= ?
                AND cr_bookings.created_at <= ?
            ) as bookings_count', [$startDate->toDateTimeString(), $endDate->toDateTimeString()])
            ->selectRaw('(
                SELECT COALESCE(SUM(cr_bookings.amount), 0)
                FROM cr_bookings
                INNER JOIN cr_booking_cars ON cr_booking_cars.booking_id = cr_bookings.id
                WHERE cr_cars.id = cr_booking_cars.car_id
                AND cr_bookings.created_at >= ?
                AND cr_bookings.created_at <= ?
            ) as revenue', [$startDate->toDateTimeString(), $endDate->toDateTimeString()])
            ->latest('bookings_count')
            ->limit(5)
            ->get();

        // Recent reviews
        $recentReviews = CarReview::query()
            ->whereHas('car', function ($query) use ($vendorId): void {
                $query->where('author_type', Customer::class)
                    ->where('author_id', $vendorId);
            })
            ->with(['car', 'customer'])
            ->latest()
            ->limit(5)
            ->get();

        // Maintenance alerts - simulated data
        $maintenanceAlerts = collect();
        $carsWithBookings = Car::query()
            ->where('author_type', Customer::class)
            ->where('author_id', $vendorId)
            ->select('cr_cars.*')
            ->selectRaw('(
                SELECT COUNT(*)
                FROM cr_bookings
                INNER JOIN cr_booking_cars ON cr_booking_cars.booking_id = cr_bookings.id
                WHERE cr_cars.id = cr_booking_cars.car_id
            ) as bookings_count')
            ->get();

        $maxBookings = $carsWithBookings->max('bookings_count') ?: 0;
        $highThreshold = max(2, (int) ($maxBookings * 0.7)); 
        $mediumThreshold = max(1, (int) ($maxBookings * 0.5)); 
        $lowThreshold = max(1, (int) ($maxBookings * 0.3)); 

        foreach ($carsWithBookings as $car) {
            if ($car->bookings_count > 0) {
                if ($car->bookings_count >= $highThreshold) {
                    $maintenanceAlerts->push((object) ['car' => $car, 'priority' => 'high', 'message' => __('This car has been booked frequently and may need maintenance.'), 'last_maintenance' => null]);
                } elseif ($car->bookings_count >= $mediumThreshold) {
                    $maintenanceAlerts->push((object) ['car' => $car, 'priority' => 'medium', 'message' => __('Consider scheduling maintenance for this car soon.'), 'last_maintenance' => null]);
                } elseif ($car->bookings_count >= $lowThreshold) {
                    $maintenanceAlerts->push((object) ['car' => $car, 'priority' => 'low', 'message' => __('This car may need a routine check-up.'), 'last_maintenance' => null]);
                }
            }
        }

        $data = [
            // Using our new precise analytics engine
            'revenue' => [
                'gross' => $financials['gross_revenue'],
                'net' => $financials['net_payout'],
                'fees' => $financials['platform_fees'],
            ],
            'utilizationRate' => $utilizationRate,
            
            // --- NEW: Add the conversion variables to the array ---
            'conversion_rate' => $conversionStats['conversion_rate'],
            'total_views' => $conversionStats['total_views'],
            
            'bookings' => $bookings,
            'predefinedRange' => $predefinedRange,
            'startDate' => $startDate->toDateString(),
            'endDate' => $endDate->toDateString(),
            'topCars' => $topCars,
            'recentReviews' => $recentReviews,
            'maintenanceAlerts' => $maintenanceAlerts,
        ];

        // ==========================================
        // PREPARE DYNAMIC DATA FOR APEXCHARTS
        // ==========================================
        $chartDates = [];
        $chartRevenue = [];
        $chartBookings = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = \Carbon\Carbon::today()->subDays($i);
            $chartDates[] = $date->format('D'); 

            $dailyBookings = Booking::query()
                ->where('vendor_id', $vendorId)
                ->whereDate('created_at', $date)
                ->count();
                
            // Fetch daily net revenue
            $dailyGross = Booking::query()
                ->where('vendor_id', $vendorId)
                ->whereNotIn('status', ['cancelled', 'failed'])
                ->whereDate('created_at', $date)
                ->get();
                
            $dailyNet = 0;
            foreach($dailyGross as $b) {
                $dailyNet += ((float) $b->amount) * (((float) ($b->host_revenue_share_percentage ?? 100)) / 100);
            }

            $chartBookings[] = $dailyBookings;
            $chartRevenue[] = (float) $dailyNet;
        }
        
        $chartData = [
            'dates' => $chartDates,
            'revenue' => $chartRevenue,
            'bookings' => $chartBookings,
        ];

        $topCarsChart = [
            'labels' => $topCars->map(fn($car) => $car->name ?? $car->license_plate ?? 'Car #' . $car->id)->toArray(),
            'revenues' => $topCars->pluck('revenue')->map(fn($rev) => (float) $rev)->toArray(),
        ];

        if (empty($topCarsChart['labels'])) {
             $topCarsChart = ['labels' => ['No Data'], 'revenues' => [1]];
        }

        return CarRentalsHelper::view('vendor-dashboard.index', compact('totalCars', 'totalBookings', 'totalMessages', 'data', 'chartData', 'topCarsChart'));
    }

    public function postUpload(Request $request)
    {
        $customer = auth('customer')->user();

        $uploadFolder = $customer->upload_folder;

        if (! RvMedia::isChunkUploadEnabled()) {
            $validator = Validator::make($request->all(), [
                'file.0' => ['required', 'image', 'mimes:jpg,jpeg,png'],
            ]);

            if ($validator->fails()) {
                return $this
                    ->httpResponse()
                    ->setError()
                    ->setMessage($validator->getMessageBag()->first());
            }

            $result = RvMedia::handleUpload(Arr::first($request->file('file')), 0, $uploadFolder);

            if ($result['error']) {
                return $this
                    ->httpResponse()
                    ->setError()
                    ->setMessage($result['message']);
            }

            return $this
                ->httpResponse()
                ->setData($result['data']);
        }

        try {
            // Create the file receiver
            $receiver = new FileReceiver('file', $request, DropZoneUploadHandler::class);
            // Check if the upload is success, throw exception or return response you need
            if ($receiver->isUploaded() === false) {
                throw new UploadMissingFileException();
            }
            // Receive the file
            $save = $receiver->receive();
            // Check if the upload has finished (in chunk mode it will send smaller files)
            if ($save->isFinished()) {
                $result = RvMedia::handleUpload($save->getFile(), 0, $uploadFolder);

                if (! $result['error']) {
                    return $this
                        ->httpResponse()
                        ->setData($result['data']);
                }

                return $this
                    ->httpResponse()
                    ->setError()
                    ->setMessage($result['message']);
            }
            // We are in chunk mode, lets send the current progress
            $handler = $save->handler();

            return response()->json([
                'done' => $handler->getPercentageDone(),
                'status' => true,
            ]);
        } catch (Exception $exception) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage($exception->getMessage());
        }
    }

    public function postUploadFromEditor(Request $request)
    {
        $customer = auth('customer')->user();

        $uploadFolder = $customer->upload_folder;

        return RvMedia::uploadFromEditor($request, 0, $uploadFolder);
    }

    public function carAvailabilityCalendar()
    {
        $this->pageTitle(__('Car Availability Calendar'));

        Assets::addScriptsDirectly([
            'vendor/core/plugins/car-rentals/libraries/full-calendar/index.global.min.js',
            'vendor/core/plugins/car-rentals/js/car-availability-calendar.js',
        ]);

        Assets::usingVueJS();

        $vendorId = auth('customer')->id();

        $cars = Car::query()
            ->where('author_type', Customer::class)
            ->where('author_id', $vendorId)
            ->active()
            ->with(['make'])
            ->orderBy('name')
            ->get();

        return CarRentalsHelper::view('vendor-dashboard.car-availability-calendar', compact('cars'));
    }

    public function getCarAvailabilityEvents(Request $request)
    {
        $request->validate([
            'start' => ['required', 'date'],
            'end' => ['required', 'date'],
            'car_id' => ['nullable', 'integer'],
        ]);

        $vendorId = auth('customer')->id();
        $startDate = $request->date('start');
        $endDate = $request->date('end');
        $carId = $request->input('car_id');

        $query = BookingCar::query()
            ->with(['booking', 'car'])
            ->whereHas('car', function ($query) use ($vendorId): void {
                $query->where('author_type', Customer::class)
                    ->where('author_id', $vendorId);
            })
            ->whereHas('booking', function ($query): void {
                $query->whereNotIn('status', [BookingStatusEnum::CANCELLED]);
            })
            ->where(function ($query) use ($startDate, $endDate): void {
                $query->whereBetween('rental_start_date', [$startDate, $endDate])
                    ->orWhereBetween('rental_end_date', [$startDate, $endDate])
                    ->orWhere(function ($query) use ($startDate, $endDate): void {
                        $query->where('rental_start_date', '<=', $startDate)
                            ->where('rental_end_date', '>=', $endDate);
                    });
            });

        if ($carId) {
            $query->where('car_id', $carId);
        }

        $bookings = $query->get();

        $events = $bookings->map(function ($bookingCar) {
            $booking = $bookingCar->booking;
            $car = $bookingCar->car;

            return [
                'id' => $bookingCar->id,
                'title' => $car->name . ' - ' . $booking->customer_name,
                'start' => $bookingCar->rental_start_date->format('Y-m-d'),
                'end' => $bookingCar->rental_end_date->addDay()->format('Y-m-d'),
                'backgroundColor' => $this->getBookingColor($booking->status),
                'borderColor' => $this->getBookingColor($booking->status),
                'extendedProps' => [
                    'booking_id' => $booking->id,
                    'car_id' => $car->id,
                    'car_name' => $car->name,
                    'customer_name' => $booking->customer_name,
                    'customer_email' => $booking->customer_email,
                    'customer_phone' => $booking->customer_phone,
                    'status' => $booking->status->label(),
                    'amount' => format_price($booking->amount),
                    'booking_number' => $booking->booking_number,
                    'rental_start_date' => $bookingCar->rental_start_date->format('M d, Y'),
                    'rental_end_date' => $bookingCar->rental_end_date->format('M d, Y'),
                    'pickup_address' => $bookingCar->pickupAddressText,
                    'return_address' => $bookingCar->returnAddressText,
                    'detail_url' => route('car-rentals.vendor.bookings.show', $booking->id),
                ],
            ];
        });

        return response()->json($events);
    }

    public function getBookingDetails(Request $request)
    {
        $request->validate([
            'booking_id' => ['required', 'integer', 'exists:cr_bookings,id'],
        ]);

        $vendorId = auth('customer')->id();

        $booking = Booking::query()
            ->with(['car.car', 'customer', 'payment', 'invoice', 'services'])
            ->where('vendor_id', $vendorId)
            ->findOrFail($request->input('booking_id'));

        $html = view('plugins/car-rentals::bookings.information', [
            'booking' => $booking,
            'displayBookingStatus' => true,
            'printBookingRoute' => 'car-rentals.vendor.bookings.print',
            'route' => 'car-rentals.vendor.invoices.generate',
        ])->render();

        return response()->json([
            'success' => true,
            'data' => $html,
            'edit_url' => route('car-rentals.vendor.bookings.show', $booking->id),
        ]);
    }

    private function getBookingColor(BookingStatusEnum $status): string
    {
        return match ($status) {
            BookingStatusEnum::PENDING => '#ffc107',
            BookingStatusEnum::PROCESSING => '#17a2b8',
            BookingStatusEnum::COMPLETED => '#28a745',
            BookingStatusEnum::CANCELLED => '#dc3545',
            default => '#6c757d',
        };
    }

    public function telematicsLogs(Request $request)
        {
            $this->pageTitle(__('Telematics Logs'));
            $vendorId = auth('customer')->id();

            // Get cars with trackers for the filter dropdown
            $cars = Car::query()
                ->where('author_id', $vendorId)
                ->where('author_type', Customer::class)
                ->whereNotNull('telematics_device_id')
                ->pluck('name', 'id');

            $query = \Botble\CarRentals\Models\VehicleTelematicsLog::query()
                ->with('car')
                ->whereHas('car', function ($q) use ($vendorId) {
                    $q->where('author_id', $vendorId);
                });

            // Apply filters if selected
            if ($request->filled('car_id')) {
                $query->where('car_id', $request->input('car_id'));
            }

            if ($request->filled('event_type')) {
                $query->where('event_type', $request->input('event_type'));
            }

            $logs = $query->latest()->paginate(20);

            return CarRentalsHelper::view('vendor-dashboard.telematics-logs', compact('logs', 'cars'));
        }

    public function fleetCalendar()
    {
        $this->pageTitle(__('Fleet Schedule'));

        Assets::addScriptsDirectly([
            'vendor/core/plugins/car-rentals/libraries/full-calendar/index.global.min.js',
        ]);

        $vendorId = auth('customer')->id();
        
        // Fetch all cars for this vendor to populate the "Resources" list
        $cars = Car::query()
            ->where('author_id', $vendorId)
            ->where('author_type', Customer::class)
            ->select(['id', 'name', 'license_plate'])
            ->get()
            ->map(function ($car) {
                return [
                    'id' => $car->id,
                    'title' => $car->name . ' (' . ($car->license_plate ?: 'No Plate') . ')',
                ];
            });

        return CarRentalsHelper::view('vendor-dashboard.fleet-calendar', compact('cars'));
    }

    public function getFleetCalendarEvents(Request $request)
    {
        $vendorId = auth('customer')->id();
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
            // Get the car name, fallback to license plate if missing
            $carName = $item->car->name ?? $item->car->license_plate ?? 'Car #' . $item->car_id;
            
            return [
                'id' => $item->booking->id,
                // Include both Car Name and Customer Name in the event bubble
                'title' => $carName . ' (' . $item->booking->customer_name . ')',
                'start' => $item->rental_start_date->toIso8601String(),
                'end' => $item->rental_end_date->toIso8601String(),
                'color' => $this->getBookingColor($item->booking->status),
                'url' => route('car-rentals.vendor.bookings.show', $item->booking->id),
            ];
        });

        return response()->json($events);
    }

    public function getFleetLocations()
    {
        $vendorId = auth('customer')->id();

        // Get all cars for this vendor that have a registered device
        $cars = Car::query()
            ->where('author_id', $vendorId)
            ->whereNotNull('telematics_device_id')
            ->get();

        $locations = $cars->map(function ($car) {
            // Fetch the very last log entry for this car
            $lastLog = \Botble\CarRentals\Models\VehicleTelematicsLog::where('car_id', $car->id)
                ->latest()
                ->first();

            return [
                'id' => $car->id,
                'name' => $car->name,
                'plate' => $car->license_plate,
                'lat' => $lastLog ? (float) $lastLog->latitude : null,
                'lng' => $lastLog ? (float) $lastLog->longitude : null,
                'speed' => $lastLog ? $lastLog->speed_mph : 0,
                'event' => $lastLog ? $lastLog->event_type : 'offline',
                'last_ping' => $lastLog ? $lastLog->created_at->diffForHumans() : 'Never',
                'status_color' => $this->getMapMarkerColor($lastLog),
            ];
        })->filter(fn($loc) => $loc['lat'] !== null); // Only show cars with data

        return response()->json($locations);
    }

    protected function getMapMarkerColor($log): string
    {
        if (!$log) return 'gray';
        if ($log->event_type === 'geofence_exit') return 'red';
        if ($log->speed_mph > 80) return 'orange';
        return 'green';
    }

    public function liveTrackingView()
    {
        $this->pageTitle(__('Live Fleet Tracking'));

        return CarRentalsHelper::view('vendor-dashboard.live-map');
    }
}

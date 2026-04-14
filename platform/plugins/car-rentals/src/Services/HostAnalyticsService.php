<?php

namespace Botble\CarRentals\Services;

use Botble\CarRentals\Models\Booking;
use Botble\CarRentals\Models\Car;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class HostAnalyticsService
{
    /**
     * Get the core financial metrics (Gross, Net, Fees)
     */
    public function getFinancialMetrics(int $vendorId, Carbon $startDate, Carbon $endDate): array
    {
        $bookings = Booking::query()
            ->where('vendor_id', $vendorId)
            ->whereNotIn('status', ['cancelled', 'failed'])
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->get();

        $grossRevenue = 0;
        $netPayout = 0;
        $platformFees = 0;

        foreach ($bookings as $booking) {
            $gross = (float) $booking->amount;
            $grossRevenue += $gross;

            // Calculate Net based on the Host's Protection Plan Share
            $sharePercentage = (float) ($booking->host_revenue_share_percentage ?? 100);
            $net = $gross * ($sharePercentage / 100);
            
            $netPayout += $net;
            $platformFees += ($gross - $net);
        }

        return [
            'gross_revenue' => $grossRevenue,
            'net_payout' => $netPayout,
            'platform_fees' => $platformFees,
        ];
    }

    /**
     * Calculate Fleet Utilization Percentage
     * (Total Days Booked / Total Days Available) * 100
     */
    public function getFleetUtilization(int $vendorId, Carbon $startDate, Carbon $endDate): float
    {
        $totalCars = Car::query()->where('author_id', $vendorId)->active()->count();
        
        if ($totalCars === 0) {
            return 0;
        }

        $daysInPeriod = $startDate->diffInDays($endDate) + 1;
        $totalAvailableDays = $totalCars * $daysInPeriod;

        // Calculate total days booked within this period
        $bookedDays = DB::table('cr_booking_cars')
            ->join('cr_bookings', 'cr_booking_cars.booking_id', '=', 'cr_bookings.id')
            ->join('cr_cars', 'cr_booking_cars.car_id', '=', 'cr_cars.id')
            ->where('cr_cars.author_id', $vendorId)
            ->whereNotIn('cr_bookings.status', ['cancelled', 'failed'])
            ->where('cr_booking_cars.rental_start_date', '<=', $endDate)
            ->where('cr_booking_cars.rental_end_date', '>=', $startDate)
            ->selectRaw('SUM(DATEDIFF(
                LEAST(cr_booking_cars.rental_end_date, ?), 
                GREATEST(cr_booking_cars.rental_start_date, ?)
            ) + 1) as total_days', [$endDate, $startDate])
            ->value('total_days');

        if (!$bookedDays) {
            return 0;
        }

        return min(100, round(($bookedDays / $totalAvailableDays) * 100, 1));
    }

    public function getConversionStats(int $vendorId): array
    {
        $stats = DB::table('cr_cars')
            ->where('author_id', $vendorId)
            ->selectRaw('SUM(views_count) as total_views')
            ->first();

        $totalBookings = Booking::where('vendor_id', $vendorId)
            ->whereNotIn('status', ['cancelled', 'failed'])
            ->count();

        $views = $stats->total_views ?? 0;
        $conversionRate = $views > 0 ? min(100, round(($totalBookings / $views) * 100, 1)) : 0;

        return [
            'total_views' => $views,
            'conversion_rate' => $conversionRate
        ];
    }
}
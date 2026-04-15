<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Botble\CarRentals\Jobs\GenerateDemandPricingRecommendationsJob;
use Botble\CarRentals\Jobs\AutoApplyDemandPricingRecommendationsJob;

/**
 * =========================================================================
 * PRODUCTION SCHEDULER CONFIGURATION
 * =========================================================================
 *
 * IMPORTANT: This file defines all scheduled jobs and commands.
 * 
 * For these schedules to run in production, you need:
 * 
 * 1. Queue Worker Running:
 *    php artisan queue:listen  (or queue:work for daemon mode)
 * 
 * 2. Unix Cron Job (add to crontab):
 *    * * * * * cd /path/to/laravel && php artisan schedule:run >> /dev/null 2>&1
 * 
 * 3. Verify Scheduler:
 *    php artisan schedule:list      (see all scheduled tasks)
 *    php artisan schedule:test      (test scheduler)
 * 
 * =========================================================================
 * DAILY JOBS & COMMANDS (Auto-Run)
 * =========================================================================
 */

// 03:30 AM - Send trip reminders to customers
Schedule::command('car-rentals:send-trip-reminders')->dailyAt('03:30');

// 03:30 AM - Send return alerts to customers
Schedule::command('car-rentals:send-return-alerts')->dailyAt('03:30');

/**
 * =========================================================================
 * DEMAND-AWARE AUTO-PRICING JOBS
 * =========================================================================
 *
 * These jobs implement the complete demand-pricing workflow:
 * 1. Generate recommendations based on demand signals (04:00)
 * 2. Auto-apply high-confidence recommendations (hourly)
 * 3. Cleanup expired recommendations (05:00)
 *
 * Signals used: views, bookings, occupancy, weekends
 * Confidence threshold: 0.70 (70%)
 * Daily price change cap: Configurable per car
 * 
 * Dashboard: /admin/car-rentals/auto-pricing/metrics
 * Manual trigger: php artisan car-rentals:auto-apply-recommendations --dry-run
 */

// 04:00 AM - Generate demand pricing recommendations for next 30 days (JOB - Async)
// Process: Analyzes views, bookings, occupancy rates past 30 days
// Output: Creates "pending" demand_pricing_recommendations
// Queue: Processes ~50 cars per batch asynchronously
Schedule::job(new GenerateDemandPricingRecommendationsJob())->dailyAt('04:00');

// 05:00 AM - Cleanup expired pending recommendations (COMMAND - Sync)
// Process: Deletes "pending" recommendations older than 24 hours
// Preserves: "applied" and "dismissed" recommendations (audit trail)
// Risk: None - only removes stale pending recommendations
Schedule::command('car-rentals:cleanup-expired-recommendations')->dailyAt('05:00');

// Every hour at :30 - Auto-apply high-confidence recommendations (JOB - Async)
// Timing: Runs at 1:30, 2:30, 3:30, ... 23:30 (24x per day)
// Criteria: confidence_score >= 0.70 + global/per-car pause checks
// Action: Creates/updates car_dates with recommended_value, marks as applied_by=0
// Queue: Asynchronous - doesn't block HTTP requests
// Safety: Global pause (admin) + per-car pause (vendor) kill switches
Schedule::job(new AutoApplyDemandPricingRecommendationsJob())->hourly()->at('30');

/**
 * =========================================================================
 * MISC SCHEDULED COMMANDS
 * =========================================================================
 */

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

/**
 * =========================================================================
 * MANUAL COMMAND TRIGGERS (For Production Testing/Admin)
 * =========================================================================
 *
 * Generate Recommendations:
 *   php artisan car-rentals:generate-demand-pricing-recommendations
 *   php artisan car-rentals:generate-demand-pricing-recommendations --days=7
 *   php artisan car-rentals:generate-demand-pricing-recommendations --sync
 * 
 * Auto-Apply Recommendations:
 *   php artisan car-rentals:auto-apply-recommendations
 *   php artisan car-rentals:auto-apply-recommendations --dry-run
 * 
 * Cleanup Expired:
 *   php artisan car-rentals:cleanup-expired-recommendations
 * 
 * Monitor Queue:
 *   php artisan queue:failed (view failed jobs)
 *   php artisan queue:retry all (retry failed jobs)
 *   php artisan queue:flush (clear all jobs)
 *
 * =========================================================================
 */
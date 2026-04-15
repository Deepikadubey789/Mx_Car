<?php

namespace Botble\CarRentals\Notifications;

use Botble\CarRentals\Models\Customer;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;

class VendorHighConfidenceRecommendationNotification extends Notification
{
    use Queueable;

    public function __construct(
        protected Collection $recommendations,
        protected string $notificationType = 'normal', // 'normal' or 'expiring'
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $count = $this->recommendations->count();
        $totalValue = $this->recommendations->sum('estimated_revenue_impact');

        $mailMessage = (new MailMessage)
            ->subject($this->notificationType === 'expiring' 
                ? "Action Required: {$count} Pricing Recommendation(s) Expiring Soon"
                : "New: {$count} High-Confidence Pricing Recommendation(s)")
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line($this->notificationType === 'expiring'
                ? "You have {$count} pricing recommendation(s) expiring within 24 hours."
                : "Great news! We've generated {$count} high-confidence pricing recommendation(s) for your fleet.");

        if ($this->notificationType !== 'expiring') {
            $mailMessage->line(sprintf(
                "These recommendations could generate an estimated <strong>$%.2f</strong> in additional revenue.",
                $totalValue
            ));
        }

        // Add top recommendations preview
        foreach ($this->recommendations->take(3) as $rec) {
            $mailMessage->line(sprintf(
                "🚗 <strong>%s</strong> - Suggested: $%.2f (Confidence: %d%%)",
                $rec->car->name ?? 'Car ' . $rec->car_id,
                $rec->recommended_value,
                intval($rec->confidence_score * 100)
            ));
        }

        if ($count > 3) {
            $mailMessage->line(sprintf("... and %d more recommendation(s)", $count - 3));
        }

        $mailMessage
            ->action('View All Recommendations', route('car-rentals.vendor.demand-pricing.recommendations.index'))
            ->line('Act quickly — recommendations expire in 24 hours!');

        return $mailMessage;
    }

    public function toDatabase(object $notifiable): array
    {
        $count = $this->recommendations->count();

        return [
            'title' => $this->notificationType === 'expiring'
                ? "{$count} Pricing Recommendation(s) Expiring"
                : "{$count} New Pricing Recommendation(s)",
            'message' => $this->notificationType === 'expiring'
                ? "You have {$count} recommendation(s) expiring soon. Review at your vendor dashboard."
                : "New demand-based pricing recommendations available for your cars.",
            'type' => 'demand-pricing',
            'action_url' => route('car-rentals.vendor.demand-pricing.recommendations.index'),
            'count' => $count,
            'total_value' => $this->recommendations->sum('estimated_revenue_impact'),
        ];
    }
}

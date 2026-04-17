<?php

namespace Botble\CarRentals\Observers;

use Botble\CarRentals\Models\BookingClaim;
use Botble\CarRentals\Models\WhatsAppConfig;
use Botble\CarRentals\Services\WhatsApp\WhatsAppSentMessageService;
use Illuminate\Support\Facades\Log;

class BookingClaimObserver
{
    protected WhatsAppSentMessageService $whatsAppSentMessageService;

    public function __construct(WhatsAppSentMessageService $whatsAppSentMessageService)
    {
        $this->whatsAppSentMessageService = $whatsAppSentMessageService;
    }

    /**
     * Handle the BookingClaim "created" event.
     */
    public function created(BookingClaim $claim): void
    {
        if (!WhatsAppConfig::where('enabled', true)->exists()) {
            return;
        }

        try {
            $customer = $claim->booking?->customer;
            if (!$customer || !$customer->whatsapp) {
                return;
            }

            $this->sendDisputeCreated($claim, $customer);
        } catch (\Exception $e) {
            Log::error('BookingClaimObserver created error', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Handle the BookingClaim "updated" event.
     */
    public function updated(BookingClaim $claim): void
    {
        if (!WhatsAppConfig::where('enabled', true)->exists()) {
            return;
        }

        try {
            $customer = $claim->booking?->customer;
            if (!$customer || !$customer->whatsapp) {
                return;
            }

            // Check if status changed to resolved
            $original = $claim->getOriginal();
            $currentStatus = $claim->status;
            $originalStatus = $original['status'] ?? null;

            if ($originalStatus !== 'resolved' && $currentStatus === 'resolved') {
                $this->sendDisputeResolved($claim, $customer);
            }
        } catch (\Exception $e) {
            Log::error('BookingClaimObserver updated error', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Send dispute created message
     */
    protected function sendDisputeCreated(BookingClaim $claim, $customer): void
    {
        try {
            $data = [
                'booking_reference' => $claim->booking->booking_number,
                'claim_id' => $claim->id,
                'claim_category' => ucfirst($claim->category ?? 'general'),
                'claimed_amount' => '$' . number_format($claim->claimed_amount ?? 0, 2),
            ];

            $this->whatsAppSentMessageService->sendFromTemplate(
                $customer,
                'dispute_created',
                $data,
                'dispute_created',
                $claim->booking,
                $claim
            );

            Log::info('Dispute created WhatsApp sent', ['claim_id' => $claim->id]);
        } catch (\Exception $e) {
            Log::error('Failed to send dispute created message', [
                'claim_id' => $claim->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Send dispute resolved message
     */
    protected function sendDisputeResolved(BookingClaim $claim, $customer): void
    {
        try {
            $outcomeMappings = [
                'manual_only' => 'Manual review',
                'capture_deposit' => 'Deposit captured',
                'release_deposit' => 'Deposit released',
                'partial_refund' => 'Partial refund issued',
            ];

            $data = [
                'booking_reference' => $claim->booking->booking_number,
                'claim_id' => $claim->id,
                'claim_outcome' => $outcomeMappings[$claim->outcome_action] ?? 'Claim processed',
                'approved_amount' => '$' . number_format($claim->approved_amount ?? 0, 2),
                'resolution_note' => $claim->resolution_note ?? 'Thank you for your patience.',
            ];

            $this->whatsAppSentMessageService->sendFromTemplate(
                $customer,
                'dispute_resolved',
                $data,
                'dispute_resolved',
                $claim->booking,
                $claim
            );

            Log::info('Dispute resolved WhatsApp sent', ['claim_id' => $claim->id]);
        } catch (\Exception $e) {
            Log::error('Failed to send dispute resolved message', [
                'claim_id' => $claim->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}

<?php

namespace Botble\CarRentals\Services;

use Botble\CarRentals\Models\Booking;
use Botble\CarRentals\Models\BookingClaim;
use Botble\CarRentals\Models\BookingSupportAction;
use Botble\CarRentals\Models\CustomerKycVerification;
use Botble\CarRentals\Models\TripMessage;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class TripTimelineBuilder
{
    /**
     * @return list<array{
     *     occurred_at: string,
     *     category: string,
     *     title: string,
     *     summary: string,
     *     metadata: array<string, mixed>,
     *     actor: ?array{type: ?string, id: ?int, name: ?string},
     *     source: string
     * }>
     */
    public function build(Booking $booking): array
    {
        $booking->loadMissing([
            'tripMessages.sender',
            'supportActions.admin',
            'claims.assignee',
            'kycVerification.documents',
            'payment',
            'invoice',
            'customer',
            'vendor',
            'car',
        ]);

        $rows = collect();

        foreach ($booking->tripMessages as $message) {
            $rows->push($this->tripMessageRow($message));
        }

        foreach ($booking->supportActions as $action) {
            $rows->push($this->supportActionRow($action));
        }

        foreach ($booking->claims as $claim) {
            $rows = $rows->merge($this->claimRows($claim));
        }

        foreach ($this->bookingMilestoneRows($booking) as $row) {
            $rows->push($row);
        }

        $rows = $rows->merge($this->kycRows($booking));
        $rows = $rows->merge($this->paymentRows($booking));
        $rows = $rows->merge($this->invoiceRows($booking));

        return $this->finalizeTimelineRows($rows);
    }

    /**
     * @param  Collection<int, array<string, mixed>>  $rows
     * @return list<array<string, mixed>>
     */
    public function finalizeTimelineRows(Collection $rows): array
    {
        return $rows
            ->filter(fn (array $row) => $row['occurred_at'] !== '')
            ->sort(function (array $a, array $b): int {
                return strcmp($a['occurred_at'], $b['occurred_at']) ?: strcmp($a['source'], $b['source']);
            })
            ->values()
            ->all();
    }

    protected function tripMessageRow(TripMessage $message): array
    {
        $sender = $message->sender;
        $actor = $this->actorFromModel($sender);

        return [
            'occurred_at' => $this->iso($message->created_at),
            'category' => 'chat',
            'title' => trans('plugins/car-rentals::disputes.timeline_chat', ['type' => $message->type]),
            'summary' => Str::limit(strip_tags((string) $message->message), 500) ?: trans('plugins/car-rentals::disputes.timeline_chat_empty'),
            'metadata' => [
                'message_type' => $message->type,
                'message_id' => $message->getKey(),
            ],
            'actor' => $actor,
            'source' => 'trip_message:'.$message->getKey(),
        ];
    }

    protected function supportActionRow(BookingSupportAction $action): array
    {
        $admin = $action->admin;

        return [
            'occurred_at' => $this->iso($action->created_at),
            'category' => 'support_action',
            'title' => trans('plugins/car-rentals::disputes.timeline_support_action', ['action' => $action->action]),
            'summary' => $action->note ?: trans('plugins/car-rentals::disputes.timeline_support_action_summary', [
                'admin' => $admin?->name ?: trans('plugins/car-rentals::disputes.system'),
            ]),
            'metadata' => array_merge($action->metadata ?? [], [
                'action' => $action->action,
                'admin_name' => $admin?->name,
            ]),
            'actor' => $this->actorFromModel($admin),
            'source' => 'support_action:'.$action->getKey(),
        ];
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    protected function claimRows(BookingClaim $claim): Collection
    {
        $rows = collect();

        $rows->push([
            'occurred_at' => $this->iso($claim->created_at),
            'category' => 'support_action',
            'title' => trans('plugins/car-rentals::disputes.timeline_claim_created'),
            'summary' => trans('plugins/car-rentals::disputes.timeline_claim_summary', [
                'category' => $claim->category ?: 'general',
                'status' => str((string) $claim->status)->replace('_', ' ')->title(),
            ]),
            'metadata' => [
                'claim_id' => $claim->id,
                'category' => $claim->category,
                'status' => $claim->status,
                'claimed_amount' => $claim->claimed_amount,
                'approved_amount' => $claim->approved_amount,
                'priority' => $claim->priority,
                'evidence_completeness' => $claim->evidence_completeness,
                'evidence_provenance' => $claim->evidence_provenance,
            ],
            'actor' => $this->actorFromModel($claim->assignee),
            'source' => 'claim:'.$claim->id.':created',
        ]);

        if ($claim->updated_at && $claim->updated_at->ne($claim->created_at)) {
            $rows->push([
                'occurred_at' => $this->iso($claim->updated_at),
                'category' => 'support_action',
                'title' => trans('plugins/car-rentals::disputes.timeline_claim_updated'),
                'summary' => trans('plugins/car-rentals::disputes.timeline_claim_summary', [
                    'category' => $claim->category ?: 'general',
                    'status' => str((string) $claim->status)->replace('_', ' ')->title(),
                ]),
                'metadata' => [
                    'claim_id' => $claim->id,
                    'category' => $claim->category,
                    'status' => $claim->status,
                    'claimed_amount' => $claim->claimed_amount,
                    'approved_amount' => $claim->approved_amount,
                    'resolution_note' => $claim->resolution_note,
                    'outcome_action' => $claim->outcome_action,
                    'settlement_status' => $claim->settlement_status,
                    'settlement_reference' => $claim->settlement_reference,
                ],
                'actor' => $this->actorFromModel($claim->assignee),
                'source' => 'claim:'.$claim->id.':updated',
            ]);
        }

        if ($claim->resolved_at instanceof Carbon) {
            $rows->push([
                'occurred_at' => $this->iso($claim->resolved_at),
                'category' => 'support_action',
                'title' => trans('plugins/car-rentals::disputes.timeline_claim_updated'),
                'summary' => trans('plugins/car-rentals::disputes.timeline_claim_resolved_summary', [
                    'status' => str((string) $claim->status)->replace('_', ' ')->title(),
                    'approved_amount' => (string) ($claim->approved_amount ?? 0),
                ]),
                'metadata' => [
                    'claim_id' => $claim->id,
                    'status' => $claim->status,
                    'approved_amount' => $claim->approved_amount,
                    'resolved_at' => $this->iso($claim->resolved_at),
                    'outcome_action' => $claim->outcome_action,
                    'settlement_status' => $claim->settlement_status,
                    'settlement_reference' => $claim->settlement_reference,
                ],
                'actor' => $this->actorFromModel($claim->assignee),
                'source' => 'claim:'.$claim->id.':resolved',
            ]);
        }

        if ($claim->settlement_completed_at instanceof Carbon) {
            $rows->push([
                'occurred_at' => $this->iso($claim->settlement_completed_at),
                'category' => 'payment',
                'title' => trans('plugins/car-rentals::disputes.timeline_claim_settlement'),
                'summary' => trans('plugins/car-rentals::disputes.timeline_claim_settlement_summary', [
                    'action' => (string) ($claim->outcome_action ?: 'manual_only'),
                    'status' => (string) ($claim->settlement_status ?: 'pending'),
                ]),
                'metadata' => [
                    'claim_id' => $claim->id,
                    'settlement_status' => $claim->settlement_status,
                    'settlement_reference' => $claim->settlement_reference,
                    'settlement_metadata' => $claim->settlement_metadata,
                ],
                'actor' => $this->actorFromModel($claim->assignee),
                'source' => 'claim:'.$claim->id.':settlement',
            ]);
        }

        return $rows;
    }

    /**
     * @return list<array<string, mixed>>
     */
    protected function bookingMilestoneRows(Booking $booking): array
    {
        $map = [
            'created_at' => ['booking_lifecycle', 'timeline_booking_created'],
            'pickup_photos_uploaded_at' => ['media', 'timeline_pickup_photos'],
            'after_photos_uploaded_at' => ['media', 'timeline_return_photos'],
            'deposit_authorized_at' => ['payment', 'timeline_deposit_authorized'],
            'deposit_settled_at' => ['payment', 'timeline_deposit_settled'],
            'damage_settled_at' => ['damage', 'timeline_damage_settled'],
            'completed_at' => ['booking_lifecycle', 'timeline_completed'],
            'cancelled_at' => ['booking_lifecycle', 'timeline_cancelled'],
            'modified_at' => ['booking_lifecycle', 'timeline_modified'],
            'key_instructions_sent_at' => ['booking_lifecycle', 'timeline_key_instructions'],
            'price_lock_expires_at' => ['booking_lifecycle', 'timeline_price_lock_expires'],
        ];

        $rows = [];

        foreach ($map as $field => [$category, $titleKey]) {
            $value = $booking->{$field};
            if (! $value instanceof Carbon) {
                continue;
            }

            $rows[] = [
                'occurred_at' => $this->iso($value),
                'category' => $category,
                'title' => trans('plugins/car-rentals::disputes.'.$titleKey),
                'summary' => trans('plugins/car-rentals::disputes.timeline_booking_field', ['field' => $field]),
                'metadata' => [
                    'booking_field' => $field,
                    'evidence_provenance' => [
                        'source' => 'booking',
                        'field' => $field,
                        'recorded_at' => $this->iso($value),
                        'confidence' => 'system_timestamp',
                    ],
                ],
                'actor' => null,
                'source' => 'booking_field:'.$field,
            ];
        }

        return $rows;
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    protected function kycRows(Booking $booking): Collection
    {
        $verification = $booking->kycVerification;
        if (! $verification instanceof CustomerKycVerification || ! $verification->getKey()) {
            return collect();
        }

        $at = $verification->reviewed_at ?: $verification->updated_at ?: $verification->created_at;

        $summaryParts = array_filter([
            $verification->status,
            $verification->rejection_reason,
        ]);

        $row = [
            'occurred_at' => $this->iso($at),
            'category' => 'verification',
            'title' => trans('plugins/car-rentals::disputes.timeline_kyc'),
            'summary' => implode(' · ', $summaryParts) ?: trans('plugins/car-rentals::disputes.timeline_kyc_pending'),
            'metadata' => [
                'status' => $verification->status,
                'reviewed_at' => $this->iso($verification->reviewed_at),
                'license_valid' => $verification->license_valid,
                'license_expiry_date' => $verification->license_expiry_date?->toDateString(),
                'document_count' => $verification->documents?->count() ?? 0,
            ],
            'actor' => null,
            'source' => 'kyc_verification:'.$verification->getKey(),
        ];

        return collect([$row]);
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    protected function paymentRows(Booking $booking): Collection
    {
        $payment = $booking->payment;
        if (! $payment || ! $payment->getKey()) {
            return collect();
        }

        $rows = collect();

        $rows->push([
            'occurred_at' => $this->iso($payment->created_at),
            'category' => 'payment',
            'title' => trans('plugins/car-rentals::disputes.timeline_payment_recorded'),
            'summary' => trans('plugins/car-rentals::disputes.timeline_payment_summary', [
                'amount' => (string) ($payment->amount ?? 0),
                'status' => (string) ($payment->status?->label() ?: $payment->status?->value ?: $payment->status ?: trans('plugins/car-rentals::disputes.unknown')),
            ]),
            'metadata' => [
                'payment_id' => $payment->getKey(),
                'status' => $payment->status?->value ?? $payment->status,
                'amount' => $payment->amount,
            ],
            'actor' => null,
            'source' => 'payment:'.$payment->getKey(),
        ]);

        foreach (['authorized_at', 'captured_at', 'released_at'] as $field) {
            $dt = $payment->{$field};
            if ($dt instanceof Carbon) {
                $rows->push([
                    'occurred_at' => $this->iso($dt),
                    'category' => 'payment',
                    'title' => trans('plugins/car-rentals::disputes.timeline_payment_event', ['event' => $field]),
                    'summary' => trans('plugins/car-rentals::disputes.timeline_payment_event_summary', [
                        'event' => $field,
                    ]),
                    'metadata' => ['payment_id' => $payment->getKey(), 'event' => $field],
                    'actor' => null,
                    'source' => 'payment:'.$payment->getKey().':'.$field,
                ]);
            }
        }

        return $rows;
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    protected function invoiceRows(Booking $booking): Collection
    {
        $invoice = $booking->invoice;
        if (! $invoice || ! $invoice->getKey()) {
            return collect();
        }

        $rows = collect([
            [
                'occurred_at' => $this->iso($invoice->created_at),
                'category' => 'payment',
                'title' => trans('plugins/car-rentals::disputes.timeline_invoice_created'),
                'summary' => trans('plugins/car-rentals::disputes.timeline_invoice_summary', [
                    'code' => (string) ($invoice->code ?: '#'.$invoice->id),
                    'amount' => (string) ($invoice->amount ?? 0),
                ]),
                'metadata' => [
                    'invoice_id' => $invoice->getKey(),
                    'code' => $invoice->code,
                    'amount' => $invoice->amount,
                ],
                'actor' => null,
                'source' => 'invoice:'.$invoice->getKey(),
            ],
        ]);

        if ($invoice->paid_at instanceof Carbon) {
            $rows->push([
                'occurred_at' => $this->iso($invoice->paid_at),
                'category' => 'payment',
                'title' => trans('plugins/car-rentals::disputes.timeline_invoice_paid'),
                'summary' => trans('plugins/car-rentals::disputes.timeline_invoice_paid_summary', [
                    'code' => (string) ($invoice->code ?: '#'.$invoice->id),
                ]),
                'metadata' => ['invoice_id' => $invoice->getKey()],
                'actor' => null,
                'source' => 'invoice:'.$invoice->getKey().':paid_at',
            ]);
        }

        return $rows;
    }

    protected function iso(?Carbon $value): string
    {
        if (! $value instanceof Carbon) {
            return '';
        }

        return $value->copy()->utc()->toIso8601String();
    }

    /**
     * @return array{type: ?string, id: ?int, name: ?string}|null
     */
    protected function actorFromModel(?object $model): ?array
    {
        if (! $model) {
            return null;
        }

        $name = null;
        if (isset($model->name)) {
            $name = (string) $model->name;
        } elseif (isset($model->first_name) || isset($model->last_name)) {
            $name = trim(((string) ($model->first_name ?? '')).' '.((string) ($model->last_name ?? '')));
        } elseif (isset($model->email)) {
            $name = (string) $model->email;
        }

        return [
            'type' => $model::class,
            'id' => $model->getKey() ? (int) $model->getKey() : null,
            'name' => $name,
        ];
    }
}

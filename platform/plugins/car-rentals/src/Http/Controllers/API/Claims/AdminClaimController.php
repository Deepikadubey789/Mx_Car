<?php

namespace Botble\CarRentals\Http\Controllers\API\Claims;

use Botble\ACL\Models\User;
use Botble\Api\Http\Controllers\BaseApiController;
use Botble\CarRentals\Http\Controllers\BookingClaimController;
use Botble\CarRentals\Http\Requests\API\AdminStoreBookingClaimRequest;
use Botble\CarRentals\Http\Requests\API\AdminUpdateBookingClaimRequest;
use Botble\CarRentals\Http\Resources\AdminClaimQueueResource;
use Botble\CarRentals\Http\Resources\ClaimDetailResource;
use Botble\CarRentals\Http\Resources\ClaimTimelineResource;
use Botble\CarRentals\Models\Booking;
use Botble\CarRentals\Models\BookingClaim;
use Botble\CarRentals\Notifications\ClaimAssignmentNotification;
use Botble\CarRentals\Notifications\ClaimSlaBreachNotification;
use Botble\CarRentals\Services\ClaimNotificationDispatcher;
use Botble\CarRentals\Services\ClaimResolutionSettlementService;
use Botble\CarRentals\Services\SupportActionRecorder;
use Botble\CarRentals\Services\TripTimelineBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class AdminClaimController extends BaseApiController
{
    public function queue(Request $request)
    {
        $this->authorizePermission('car-rentals.bookings.claims.index');

        $filters = $this->filters($request);

        $claims = BookingClaim::query()
            ->with([
                'booking:id,booking_number,customer_name,vendor_id',
                'assignee:id,first_name,last_name,email',
            ])
            ->when($filters['only_open'], function ($query): void {
                $query->whereIn('status', ['open', 'under_review', 'awaiting_docs', 'ready_for_decision']);
            })
            ->when(
                ! $filters['only_open'] && $filters['status'] !== '',
                fn ($query) => $query->where('status', $filters['status'])
            )
            ->when($filters['assignee_id'], fn ($query) => $query->where('assignee_id', (int) $filters['assignee_id']))
            ->when($filters['category'] !== '', fn ($query) => $query->where('category', 'like', '%' . $filters['category'] . '%'))
            ->when($filters['priority'] !== '', fn ($query) => $query->where('priority', $filters['priority']))
            ->when($filters['escalated'] !== '', function ($query) use ($filters): void {
                if ($filters['escalated'] === 'yes') {
                    $query->whereNotNull('escalated_at');
                } elseif ($filters['escalated'] === 'no') {
                    $query->whereNull('escalated_at');
                }
            })
            ->when($filters['sla_breached'], function ($query): void {
                $query->whereNotNull('resolution_due_at')
                    ->where('resolution_due_at', '<', now())
                    ->whereNotIn('status', ['resolved', 'rejected', 'closed_no_action']);
            })
            ->when($filters['booking_number'] !== '', function ($query) use ($filters): void {
                $query->whereHas('booking', function ($bookingQuery) use ($filters): void {
                    $bookingQuery->where('booking_number', 'like', '%' . $filters['booking_number'] . '%');
                });
            })
            ->orderByDesc('updated_at')
            ->paginate(min($request->integer('per_page', 20), 100));

        return $this
            ->httpResponse()
            ->setData([
                'items' => AdminClaimQueueResource::collection($claims),
                'filters' => $filters,
            ])
            ->toApiResponse();
    }

    public function metrics()
    {
        $this->authorizePermission('car-rentals.bookings.claims.index');

        $metrics = BookingClaim::query()
            ->selectRaw('COUNT(*) as total_claims')
            ->selectRaw("SUM(CASE WHEN status IN ('open','under_review','awaiting_docs','ready_for_decision') THEN 1 ELSE 0 END) as open_claims")
            ->selectRaw("SUM(CASE WHEN resolution_due_at IS NOT NULL AND resolution_due_at < NOW() AND status NOT IN ('resolved','rejected','closed_no_action') THEN 1 ELSE 0 END) as sla_breached_claims")
            ->selectRaw("SUM(CASE WHEN escalated_at IS NOT NULL THEN 1 ELSE 0 END) as escalated_claims")
            ->first();

        return $this
            ->httpResponse()
            ->setData((array) $metrics)
            ->toApiResponse();
    }

    public function index(int $booking)
    {
        $this->authorizePermission('car-rentals.bookings.claims.index');
        $booking = $this->resolveBooking($booking);

        return $this
            ->httpResponse()
            ->setData(ClaimDetailResource::collection(
                $booking->claims()->with(['booking', 'assignee'])->latest('id')->get()
            ))
            ->toApiResponse();
    }

    public function show(int $booking, int $claim)
    {
        $this->authorizePermission('car-rentals.bookings.claims.index');
        $claim = $this->resolveClaim($this->resolveBooking($booking), $claim);

        return $this
            ->httpResponse()
            ->setData(new ClaimDetailResource($claim->loadMissing(['booking', 'assignee'])))
            ->toApiResponse();
    }

    public function store(int $booking, AdminStoreBookingClaimRequest $request)
    {
        $this->authorizePermission('car-rentals.bookings.claims.assign');
        $booking = $this->resolveBooking($booking);
        $data = $request->validated();

        if (($data['outcome_action'] ?? 'manual_only') !== 'manual_only') {
            $this->authorizePermission('car-rentals.bookings.claims.financial');
        }

        $claim = BookingClaim::query()->create([
            'booking_id' => $booking->id,
            'category' => $data['category'],
            'claimed_amount' => $data['claimed_amount'] ?? null,
            'reason' => $data['reason'],
            'assignee_id' => $data['assignee_id'] ?? null,
            'status' => $data['status'] ?? 'open',
            'priority' => $data['priority'] ?? 'normal',
            'liability_decision' => $data['liability_decision'] ?? 'undetermined',
            'policy_basis' => $data['policy_basis'] ?? null,
            'evidence_completeness' => $data['evidence_completeness'] ?? 'partial',
            'requires_additional_docs' => (bool) ($data['requires_additional_docs'] ?? false),
            'checklist_notes' => $data['checklist_notes'] ?? null,
            'first_response_due_at' => $data['first_response_due_at'] ?? now()->addHours(12),
            'resolution_due_at' => $data['resolution_due_at'] ?? now()->addHours(48),
            'outcome_action' => $data['outcome_action'] ?? 'manual_only',
            'evidence_provenance' => $data['evidence_provenance'] ?? null,
        ]);

        app(SupportActionRecorder::class)->record($booking, 'claim_created', null, [
            'claim_id' => $claim->id,
            'status' => $claim->status,
            'category' => $claim->category,
            'claimed_amount' => $claim->claimed_amount,
            'assignee_id' => $claim->assignee_id,
            'priority' => $claim->priority,
            'resolution_due_at' => optional($claim->resolution_due_at)->toIso8601String(),
        ]);

        $this->notifyAssignmentIfChanged($booking, $claim, null, $claim->assignee_id);
        $this->notifySlaBreachIfNeeded($booking, $claim);

        return $this
            ->httpResponse()
            ->setMessage(trans('plugins/car-rentals::disputes.claim_created'))
            ->setData(new ClaimDetailResource($claim->loadMissing(['booking', 'assignee'])))
            ->toApiResponse();
    }

    public function update(int $booking, int $claim, AdminUpdateBookingClaimRequest $request)
    {
        $this->authorizePermission('car-rentals.bookings.claims.resolve');
        $booking = $this->resolveBooking($booking);
        $claim = $this->resolveClaim($booking, $claim);
        $data = $request->validated();

        $before = [
            'status' => $claim->status,
            'assignee_id' => $claim->assignee_id,
            'approved_amount' => $claim->approved_amount,
            'resolution_note' => $claim->resolution_note,
            'reason' => $claim->reason,
            'category' => $claim->category,
            'priority' => $claim->priority,
            'resolution_due_at' => optional($claim->resolution_due_at)->toIso8601String(),
            'escalated_at' => optional($claim->escalated_at)->toIso8601String(),
            'settlement_status' => $claim->settlement_status,
            'requires_additional_docs' => (bool) $claim->requires_additional_docs,
            'outcome_action' => $claim->outcome_action,
        ];

        $this->assertValidTransition((string) $claim->status, (string) $data['status']);
        $previousAssigneeId = $claim->assignee_id;

        $claim->status = (string) $data['status'];
        $claim->assignee_id = $data['assignee_id'] ?? null;
        $claim->approved_amount = $data['approved_amount'] ?? null;
        $claim->resolution_note = $data['resolution_note'] ?? $claim->resolution_note;
        $claim->reason = $data['reason'] ?? $claim->reason;
        $claim->category = $data['category'] ?? $claim->category;
        $claim->priority = $data['priority'] ?? $claim->priority;
        $claim->liability_decision = $data['liability_decision'] ?? $claim->liability_decision;
        $claim->policy_basis = $data['policy_basis'] ?? $claim->policy_basis;
        $claim->evidence_completeness = $data['evidence_completeness'] ?? $claim->evidence_completeness;
        $claim->requires_additional_docs = array_key_exists('requires_additional_docs', $data)
            ? (bool) $data['requires_additional_docs']
            : (bool) $claim->requires_additional_docs;
        $claim->checklist_notes = $data['checklist_notes'] ?? $claim->checklist_notes;
        $claim->resolution_due_at = $data['resolution_due_at'] ?? $claim->resolution_due_at;
        $claim->first_response_due_at = $data['first_response_due_at'] ?? $claim->first_response_due_at;
        $claim->outcome_action = $data['outcome_action'] ?? $claim->outcome_action;
        $claim->evidence_provenance = $data['evidence_provenance'] ?? $claim->evidence_provenance;

        if (($claim->outcome_action ?? 'manual_only') !== 'manual_only') {
            $this->authorizePermission('car-rentals.bookings.claims.financial');
        }

        if (array_key_exists('escalated', $data)) {
            $claim->escalated_at = (bool) $data['escalated'] ? now() : null;
        }

        if (array_key_exists('escalation_note', $data)) {
            $claim->escalation_note = $data['escalation_note'] ?? null;
        }

        if (in_array($claim->status, ['resolved', 'rejected', 'closed_no_action'], true)) {
            $claim->resolved_at = now();
        } elseif ($claim->resolved_at && in_array($claim->status, ['open', 'under_review', 'awaiting_docs', 'ready_for_decision'], true)) {
            $claim->resolved_at = null;
        }

        DB::transaction(function () use ($booking, $claim): void {
            if (in_array($claim->status, ['resolved', 'rejected', 'closed_no_action'], true)) {
                $claim->settlement_attempted_at = now();
                $settlementResult = app(ClaimResolutionSettlementService::class)->settle($booking, $claim);
                $claim->settlement_status = $settlementResult['status'];
                $claim->settlement_reference = $settlementResult['reference'];
                $claim->settlement_error = null;
                $claim->settlement_metadata = $settlementResult['metadata'];
                $claim->settlement_completed_at = now();
            } else {
                $claim->settlement_status = 'pending';
                $claim->settlement_reference = null;
                $claim->settlement_error = null;
                $claim->settlement_metadata = null;
                $claim->settlement_attempted_at = null;
                $claim->settlement_completed_at = null;
            }

            $claim->save();
        });

        app(SupportActionRecorder::class)->record($booking, 'claim_updated', null, [
            'claim_id' => $claim->id,
            'before' => $before,
            'after' => [
                'status' => $claim->status,
                'assignee_id' => $claim->assignee_id,
                'approved_amount' => $claim->approved_amount,
                'resolution_note' => $claim->resolution_note,
                'reason' => $claim->reason,
                'category' => $claim->category,
                'priority' => $claim->priority,
                'resolved_at' => optional($claim->resolved_at)->toIso8601String(),
                'resolution_due_at' => optional($claim->resolution_due_at)->toIso8601String(),
                'escalated_at' => optional($claim->escalated_at)->toIso8601String(),
                'outcome_action' => $claim->outcome_action,
                'settlement_status' => $claim->settlement_status,
                'settlement_reference' => $claim->settlement_reference,
            ],
        ]);

        $this->notifyAssignmentIfChanged($booking, $claim, $previousAssigneeId, $claim->assignee_id);
        $this->notifySlaBreachIfNeeded($booking, $claim);
        $this->notifyPublicClaimChanges($booking, $claim, $before);

        return $this
            ->httpResponse()
            ->setMessage(trans('plugins/car-rentals::disputes.claim_updated'))
            ->setData(new ClaimDetailResource($claim->loadMissing(['booking', 'assignee'])))
            ->toApiResponse();
    }

    public function timeline(int $booking)
    {
        $this->authorizePermission('car-rentals.bookings.claims.index');
        $booking = $this->resolveBooking($booking);

        return $this
            ->httpResponse()
            ->setData(ClaimTimelineResource::collection(
                app(TripTimelineBuilder::class)->build($booking)
            ))
            ->toApiResponse();
    }

    protected function filters(Request $request): array
    {
        return [
            'status' => (string) $request->input('status', ''),
            'assignee_id' => $request->input('assignee_id'),
            'category' => trim((string) $request->input('category', '')),
            'booking_number' => trim((string) $request->input('booking_number', '')),
            'priority' => (string) $request->input('priority', ''),
            'escalated' => (string) $request->input('escalated', ''),
            'sla_breached' => $request->boolean('sla_breached', false),
            'only_open' => $request->boolean('only_open', true),
        ];
    }

    protected function resolveBooking(int $bookingId): Booking
    {
        $booking = Booking::query()->whereKey($bookingId)->first();

        if (! $booking) {
            abort(404, 'Booking not found');
        }

        return $booking;
    }

    protected function resolveClaim(Booking $booking, int $claimId): BookingClaim
    {
        $claim = BookingClaim::query()
            ->whereKey($claimId)
            ->where('booking_id', $booking->id)
            ->first();

        if (! $claim) {
            abort(404, 'Claim not found');
        }

        return $claim;
    }

    protected function authorizePermission(string $permission): void
    {
        $user = Auth::guard('sanctum')->user();

        if (! $user || ! method_exists($user, 'hasPermission') || ! $user->hasPermission($permission)) {
            abort(403);
        }
    }

    protected function assertValidTransition(string $from, string $to): void
    {
        if ($from === $to) {
            return;
        }

        if ($from === 'open' && in_array($to, ['ready_for_decision', 'resolved', 'rejected', 'closed_no_action'], true)) {
            abort(422, trans('plugins/car-rentals::disputes.claim_transition_invalid'));
        }
    }

    protected function notifyAssignmentIfChanged(Booking $booking, BookingClaim $claim, ?int $beforeAssigneeId, ?int $afterAssigneeId): void
    {
        if (! $afterAssigneeId || (int) $beforeAssigneeId === (int) $afterAssigneeId) {
            return;
        }

        $assignee = User::query()->find($afterAssigneeId);

        if ($assignee) {
            Notification::send($assignee, new ClaimAssignmentNotification($claim, $booking));
        }
    }

    protected function notifySlaBreachIfNeeded(Booking $booking, BookingClaim $claim): void
    {
        if (
            ! $claim->resolution_due_at
            || $claim->resolution_due_at->isFuture()
            || in_array($claim->status, ['resolved', 'rejected', 'closed_no_action'], true)
            || ! $claim->assignee_id
        ) {
            return;
        }

        $shouldNotify = ! $claim->last_notified_at || $claim->last_notified_at->lt(now()->subHours(12));

        if (! $shouldNotify) {
            return;
        }

        $assignee = User::query()->find($claim->assignee_id);

        if (! $assignee) {
            return;
        }

        Notification::send($assignee, new ClaimSlaBreachNotification($claim, $booking));

        $claim->forceFill([
            'last_notified_at' => now(),
        ])->save();
    }

    protected function notifyPublicClaimChanges(Booking $booking, BookingClaim $claim, array $before): void
    {
        $dispatcher = app(ClaimNotificationDispatcher::class);
        $docsNowRequired = (bool) $claim->requires_additional_docs;
        $docsBeforeRequired = (bool) ($before['requires_additional_docs'] ?? false);

        if (! $docsBeforeRequired && $docsNowRequired) {
            $dispatcher->notifyDocsRequested($booking, $claim);
        }

        $statusChanged = ($before['status'] ?? null) !== $claim->status;
        $isClosed = in_array($claim->status, ['resolved', 'rejected', 'closed_no_action'], true);

        if ($statusChanged && $isClosed) {
            $dispatcher->notifyClosed($booking, $claim);
        } elseif ($statusChanged) {
            $dispatcher->notifyStatusUpdated($booking, $claim);
        }

        $financialOutcomeCompleted = ($before['settlement_status'] ?? null) !== $claim->settlement_status
            && in_array($claim->settlement_status, ['completed', 'manual'], true)
            && ($claim->outcome_action ?? 'manual_only') !== 'manual_only';

        if ($financialOutcomeCompleted) {
            $dispatcher->notifyFinancialOutcome($booking, $claim);
        }
    }
}

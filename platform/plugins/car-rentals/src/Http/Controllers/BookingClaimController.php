<?php

namespace Botble\CarRentals\Http\Controllers;

use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\ACL\Models\User;
use Botble\CarRentals\Models\Booking;
use Botble\CarRentals\Models\BookingClaim;
use Botble\CarRentals\Notifications\ClaimAssignmentNotification;
use Botble\CarRentals\Notifications\ClaimSlaBreachNotification;
use Botble\CarRentals\Services\ClaimResolutionSettlementService;
use Botble\CarRentals\Services\SupportActionRecorder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\Rule;

class BookingClaimController extends BaseController
{
    public const PRIORITIES = [
        'low',
        'normal',
        'high',
        'critical',
    ];

    public const EVIDENCE_COMPLETENESS = [
        'none',
        'partial',
        'complete',
    ];

    public const LIABILITY_DECISIONS = [
        'undetermined',
        'host_liable',
        'guest_liable',
        'shared',
    ];

    public function index(Request $request)
    {
        $this->pageTitle(trans('plugins/car-rentals::disputes.claims_queue_title'));

        $filters = [
            'status' => (string) $request->input('status', ''),
            'assignee_id' => $request->input('assignee_id'),
            'category' => trim((string) $request->input('category', '')),
            'booking_number' => trim((string) $request->input('booking_number', '')),
            'priority' => (string) $request->input('priority', ''),
            'escalated' => (string) $request->input('escalated', ''),
            'sla_breached' => $request->boolean('sla_breached', false),
            'only_open' => $request->boolean('only_open', true),
        ];

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
            ->paginate(20)
            ->withQueryString();

        $metrics = BookingClaim::query()
            ->selectRaw('COUNT(*) as total_claims')
            ->selectRaw("SUM(CASE WHEN status IN ('open','under_review','awaiting_docs','ready_for_decision') THEN 1 ELSE 0 END) as open_claims")
            ->selectRaw("SUM(CASE WHEN resolution_due_at IS NOT NULL AND resolution_due_at < NOW() AND status NOT IN ('resolved','rejected','closed_no_action') THEN 1 ELSE 0 END) as sla_breached_claims")
            ->selectRaw("SUM(CASE WHEN escalated_at IS NOT NULL THEN 1 ELSE 0 END) as escalated_claims")
            ->first();

        return view('plugins/car-rentals::bookings.claims-queue', [
            'claims' => $claims,
            'filters' => $filters,
            'statuses' => BookingClaim::STATUSES,
            'priorities' => self::PRIORITIES,
            'metrics' => $metrics,
            'assignees' => User::query()
                ->select(['id', 'first_name', 'last_name', 'email'])
                ->orderBy('first_name')
                ->orderBy('last_name')
                ->limit(200)
                ->get(),
        ]);
    }

    public function store(Booking $booking, Request $request, BaseHttpResponse $response)
    {
        $data = $request->validate([
            'category' => ['required', 'string', 'max:60'],
            'claimed_amount' => ['nullable', 'numeric', 'min:0'],
            'reason' => ['required', 'string', 'max:5000'],
            'assignee_id' => ['nullable', 'integer', 'exists:users,id'],
            'status' => ['nullable', Rule::in(BookingClaim::STATUSES)],
            'priority' => ['nullable', Rule::in(self::PRIORITIES)],
            'liability_decision' => ['nullable', Rule::in(self::LIABILITY_DECISIONS)],
            'policy_basis' => ['nullable', 'string', 'max:5000'],
            'evidence_completeness' => ['nullable', Rule::in(self::EVIDENCE_COMPLETENESS)],
            'requires_additional_docs' => ['nullable', 'boolean'],
            'checklist_notes' => ['nullable', 'string', 'max:5000'],
            'resolution_due_at' => ['nullable', 'date'],
            'first_response_due_at' => ['nullable', 'date'],
            'outcome_action' => ['nullable', Rule::in(BookingClaim::OUTCOME_ACTIONS)],
            'evidence_provenance' => ['nullable', 'array'],
        ]);

        if (($data['outcome_action'] ?? 'manual_only') !== 'manual_only' && ! auth()->user()?->hasPermission('car-rentals.bookings.claims.financial')) {
            abort(403);
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

        if ($request->expectsJson()) {
            return $response
                ->setMessage(trans('plugins/car-rentals::disputes.claim_created'))
                ->setData([
                    'claim_id' => $claim->id,
                ]);
        }

        return $response
            ->setPreviousUrl(route('car-rentals.bookings.edit', $booking->id))
            ->setNextUrl(route('car-rentals.bookings.edit', $booking->id).'#trip-timeline-casefile')
            ->setMessage(trans('plugins/car-rentals::disputes.claim_created'));
    }

    public function update(Booking $booking, BookingClaim $claim, Request $request, BaseHttpResponse $response)
    {
        if ((int) $claim->booking_id !== (int) $booking->id) {
            abort(404);
        }

        $data = $request->validate([
            'status' => ['required', Rule::in(BookingClaim::STATUSES)],
            'assignee_id' => ['nullable', 'integer', 'exists:users,id'],
            'approved_amount' => ['nullable', 'numeric', 'min:0'],
            'resolution_note' => ['nullable', 'string', 'max:5000'],
            'reason' => ['nullable', 'string', 'max:5000'],
            'category' => ['nullable', 'string', 'max:60'],
            'priority' => ['nullable', Rule::in(self::PRIORITIES)],
            'liability_decision' => ['nullable', Rule::in(self::LIABILITY_DECISIONS)],
            'policy_basis' => ['nullable', 'string', 'max:5000'],
            'evidence_completeness' => ['nullable', Rule::in(self::EVIDENCE_COMPLETENESS)],
            'requires_additional_docs' => ['nullable', 'boolean'],
            'checklist_notes' => ['nullable', 'string', 'max:5000'],
            'resolution_due_at' => ['nullable', 'date'],
            'first_response_due_at' => ['nullable', 'date'],
            'outcome_action' => ['nullable', Rule::in(BookingClaim::OUTCOME_ACTIONS)],
            'evidence_provenance' => ['nullable', 'array'],
            'escalated' => ['nullable', 'boolean'],
            'escalation_note' => ['nullable', 'string', 'max:5000'],
        ]);

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

        if (($claim->outcome_action ?? 'manual_only') !== 'manual_only' && ! auth()->user()?->hasPermission('car-rentals.bookings.claims.financial')) {
            abort(403);
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

        if ($request->expectsJson()) {
            return $response
                ->setMessage(trans('plugins/car-rentals::disputes.claim_updated'))
                ->setData([
                    'claim_id' => $claim->id,
                ]);
        }

        return $response
            ->setPreviousUrl(route('car-rentals.bookings.edit', $booking->id))
            ->setNextUrl(route('car-rentals.bookings.edit', $booking->id).'#trip-timeline-casefile')
            ->setMessage(trans('plugins/car-rentals::disputes.claim_updated'));
    }

    public function metrics(): JsonResponse
    {
        $metrics = BookingClaim::query()
            ->selectRaw('COUNT(*) as total_claims')
            ->selectRaw("SUM(CASE WHEN status IN ('open','under_review','awaiting_docs','ready_for_decision') THEN 1 ELSE 0 END) as open_claims")
            ->selectRaw("SUM(CASE WHEN resolution_due_at IS NOT NULL AND resolution_due_at < NOW() AND status NOT IN ('resolved','rejected','closed_no_action') THEN 1 ELSE 0 END) as sla_breached_claims")
            ->selectRaw("SUM(CASE WHEN escalated_at IS NOT NULL THEN 1 ELSE 0 END) as escalated_claims")
            ->first();

        return response()->json((array) $metrics);
    }

    protected function assertValidTransition(string $from, string $to): void
    {
        if ($from === $to) {
            return;
        }

        // Keep only the critical guardrail: brand-new claims in `open` cannot
        // jump directly to final decision states without review context.
        if (
            $from === 'open'
            && in_array($to, ['ready_for_decision', 'resolved', 'rejected', 'closed_no_action'], true)
        ) {
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
}

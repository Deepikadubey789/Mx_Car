@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
    <style>
        .claims-queue-table .quick-update-cell {
            min-width: 260px;
        }

        .claims-queue-filters .claims-filter-actions {
            display: flex;
            align-items: flex-end;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .claims-queue-filters .form-check-label {
            white-space: nowrap;
        }

        .claims-queue-filters .claims-filter-submit {
            min-width: 110px;
        }

        @media (max-width: 1199.98px) {
            .claims-queue-table .hide-md {
                display: none;
            }
        }

        @media (max-width: 767.98px) {
            .claims-queue-table .hide-sm {
                display: none;
            }

            .claims-queue-table .quick-update-cell {
                min-width: 220px;
            }

            .claims-queue-filters .claims-filter-actions {
                align-items: center;
                gap: 0.5rem;
            }

            .claims-queue-filters .claims-filter-submit {
                width: 100%;
            }
        }
    </style>

    <div class="card">
        <div class="card-header">
            <div class="w-100">
                <h4 class="card-title mb-1">{{ trans('plugins/car-rentals::disputes.claims_queue_title') }}</h4>
                <p class="text-muted mb-0">{{ trans('plugins/car-rentals::disputes.claims_queue_help') }}</p>
                <div class="row g-2 mt-2">
                    <div class="col-6 col-md-3">
                        <div class="border rounded p-2 bg-body-tertiary">
                            <div class="text-muted small">{{ trans('plugins/car-rentals::disputes.metric_total_claims') }}</div>
                            <div class="fw-semibold">{{ (int) ($metrics->total_claims ?? 0) }}</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="border rounded p-2 bg-body-tertiary">
                            <div class="text-muted small">{{ trans('plugins/car-rentals::disputes.metric_open_claims') }}</div>
                            <div class="fw-semibold">{{ (int) ($metrics->open_claims ?? 0) }}</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="border rounded p-2 bg-body-tertiary">
                            <div class="text-muted small">{{ trans('plugins/car-rentals::disputes.metric_sla_breached') }}</div>
                            <div class="fw-semibold text-warning">{{ (int) ($metrics->sla_breached_claims ?? 0) }}</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="border rounded p-2 bg-body-tertiary">
                            <div class="text-muted small">{{ trans('plugins/car-rentals::disputes.metric_escalated') }}</div>
                            <div class="fw-semibold text-danger">{{ (int) ($metrics->escalated_claims ?? 0) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body border-bottom">
            <form method="GET" action="{{ route('car-rentals.bookings.claims.index') }}" class="row g-2 claims-queue-filters">
                <div class="col-md-2">
                    <label class="form-label">{{ trans('plugins/car-rentals::disputes.claim_status') }}</label>
                    <select name="status" class="form-select form-select-sm">
                        <option value="">{{ trans('plugins/car-rentals::disputes.select_option') }}</option>
                        @foreach($statuses as $status)
                            <option value="{{ $status }}" @selected(($filters['status'] ?? '') === $status)>{{ ucfirst(str_replace('_', ' ', $status)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">{{ trans('plugins/car-rentals::disputes.claim_assignee') }}</label>
                    <select name="assignee_id" class="form-select form-select-sm">
                        <option value="">{{ trans('plugins/car-rentals::disputes.claim_unassigned') }}</option>
                        @foreach($assignees as $assignee)
                            <option value="{{ $assignee->id }}" @selected((string) ($filters['assignee_id'] ?? '') === (string) $assignee->id)>
                                {{ trim(($assignee->first_name ?? '') . ' ' . ($assignee->last_name ?? '')) ?: $assignee->email }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">{{ trans('plugins/car-rentals::disputes.claim_category') }}</label>
                    <input type="text" class="form-control form-control-sm" name="category" value="{{ $filters['category'] ?? '' }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">{{ trans('plugins/car-rentals::disputes.booking_number') }}</label>
                    <input type="text" class="form-control form-control-sm" name="booking_number" value="{{ $filters['booking_number'] ?? '' }}">
                </div>
                <div class="col-md-1">
                    <label class="form-label">{{ trans('plugins/car-rentals::disputes.claim_priority') }}</label>
                    <select name="priority" class="form-select form-select-sm">
                        <option value="">{{ trans('plugins/car-rentals::disputes.select_option') }}</option>
                        @foreach($priorities as $priority)
                            <option value="{{ $priority }}" @selected(($filters['priority'] ?? '') === $priority)>
                                {{ trans('plugins/car-rentals::disputes.priority_' . $priority) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">{{ trans('plugins/car-rentals::disputes.claim_escalated') }}</label>
                    <select name="escalated" class="form-select form-select-sm">
                        <option value="">{{ trans('plugins/car-rentals::disputes.select_option') }}</option>
                        <option value="yes" @selected(($filters['escalated'] ?? '') === 'yes')>{{ trans('plugins/car-rentals::disputes.claim_escalated_yes') }}</option>
                        <option value="no" @selected(($filters['escalated'] ?? '') === 'no')>{{ trans('plugins/car-rentals::disputes.claim_escalated_no') }}</option>
                    </select>
                </div>
                <div class="col-md-auto">
                    <div class="claims-filter-actions">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="only_open" name="only_open" value="1" @checked($filters['only_open'] ?? false)>
                            <label class="form-check-label" for="only_open">{{ trans('plugins/car-rentals::disputes.claims_only_open') }}</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="sla_breached" name="sla_breached" value="1" @checked($filters['sla_breached'] ?? false)>
                            <label class="form-check-label" for="sla_breached">{{ trans('plugins/car-rentals::disputes.claim_sla_breached_only') }}</label>
                        </div>
                        <button class="btn btn-sm btn-primary claims-filter-submit" type="submit">{{ trans('plugins/car-rentals::disputes.search') }}</button>
                    </div>
                </div>
            </form>
        </div>
        <div class="table-responsive">
            <table class="table table-hover table-sm mb-0 claims-queue-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>{{ trans('plugins/car-rentals::disputes.booking_number') }}</th>
                        <th>{{ trans('plugins/car-rentals::disputes.claim_category') }}</th>
                        <th>{{ trans('plugins/car-rentals::disputes.claim_status') }}</th>
                        <th>{{ trans('plugins/car-rentals::disputes.claim_assignee') }}</th>
                        <th>{{ trans('plugins/car-rentals::disputes.claim_priority') }}</th>
                        <th class="hide-sm">{{ trans('plugins/car-rentals::disputes.claim_resolution_due') }}</th>
                        <th class="hide-sm">{{ trans('plugins/car-rentals::disputes.claim_claimed_amount') }}</th>
                        <th class="hide-md">{{ trans('plugins/car-rentals::disputes.claim_approved_amount') }}</th>
                        <th class="hide-md">{{ trans('plugins/car-rentals::disputes.updated_at') }}</th>
                        <th>{{ trans('plugins/car-rentals::disputes.claim_actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($claims as $claim)
                        @php
                            $isStale = optional($claim->updated_at)->lt(now()->subHours(48));
                            $isSlaBreached = $claim->resolution_due_at && $claim->resolution_due_at->isPast() && ! in_array($claim->status, ['resolved', 'rejected', 'closed_no_action'], true);
                            $isUnassigned = ! $claim->assignee_id;
                            $hasUnresolvedAmount = (float) ($claim->claimed_amount ?? 0) > (float) ($claim->approved_amount ?? 0) && ! in_array($claim->status, ['resolved', 'rejected'], true);
                            $assigneeName = trim((optional($claim->assignee)->first_name ?? '') . ' ' . (optional($claim->assignee)->last_name ?? ''));
                        @endphp
                        <tr>
                            <td>#{{ $claim->id }}</td>
                            <td>
                                <a href="{{ route('car-rentals.bookings.edit', $claim->booking_id) }}#trip-timeline-casefile" target="_blank">
                                    {{ optional($claim->booking)->booking_number ?: ('#' . $claim->booking_id) }}
                                </a>
                            </td>
                            <td>{{ $claim->category ?: '-' }}</td>
                            <td>
                                <span class="badge bg-secondary text-white">{{ ucfirst(str_replace('_', ' ', $claim->status)) }}</span>
                                @if($isStale)
                                    <span class="badge bg-warning text-dark">{{ trans('plugins/car-rentals::disputes.claim_stale') }}</span>
                                @endif
                            </td>
                            <td>
                                {{ $assigneeName ?: trans('plugins/car-rentals::disputes.claim_unassigned') }}
                                @if($isUnassigned)
                                    <span class="badge bg-danger-subtle text-danger">{{ trans('plugins/car-rentals::disputes.claim_needs_assignee') }}</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-info-subtle text-info">{{ trans('plugins/car-rentals::disputes.priority_' . ($claim->priority ?: 'normal')) }}</span>
                                @if($claim->escalated_at)
                                    <span class="badge bg-danger-subtle text-danger">{{ trans('plugins/car-rentals::disputes.claim_escalated') }}</span>
                                @endif
                            </td>
                            <td class="hide-sm">
                                {{ optional($claim->resolution_due_at)->toDateTimeString() ?: '-' }}
                                @if($isSlaBreached)
                                    <span class="badge bg-warning text-dark">{{ trans('plugins/car-rentals::disputes.metric_sla_breached') }}</span>
                                @endif
                            </td>
                            <td class="hide-sm">{{ $claim->claimed_amount !== null ? format_price((float) $claim->claimed_amount) : '-' }}</td>
                            <td class="hide-md">
                                {{ $claim->approved_amount !== null ? format_price((float) $claim->approved_amount) : '-' }}
                                @if($hasUnresolvedAmount)
                                    <span class="badge bg-warning-subtle text-warning">{{ trans('plugins/car-rentals::disputes.claim_unresolved_amount') }}</span>
                                @endif
                            </td>
                            <td class="hide-md">{{ optional($claim->updated_at)->toDateTimeString() }}</td>
                            <td class="quick-update-cell">
                                <form class="row g-1 claim-quick-update-form"
                                      method="POST"
                                      action="{{ route('car-rentals.bookings.claims.update', [$claim->booking_id, $claim->id]) }}">
                                    @csrf
                                    @method('PUT')
                                    <div class="col-4">
                                        <select class="form-select form-select-sm" name="status" required>
                                            @foreach($statuses as $status)
                                                <option value="{{ $status }}" @selected($claim->status === $status)>{{ ucfirst(str_replace('_', ' ', $status)) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-4">
                                        <select class="form-select form-select-sm" name="assignee_id">
                                            <option value="">{{ trans('plugins/car-rentals::disputes.select_option') }}</option>
                                            @foreach($assignees as $assignee)
                                                <option value="{{ $assignee->id }}" @selected((int) $claim->assignee_id === (int) $assignee->id)>
                                                    {{ trim(($assignee->first_name ?? '') . ' ' . ($assignee->last_name ?? '')) ?: $assignee->email }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-3">
                                        <input type="number" step="0.01" min="0" class="form-control form-control-sm" name="approved_amount" value="{{ $claim->approved_amount }}">
                                    </div>
                                    <div class="col-3">
                                        <select class="form-select form-select-sm" name="priority">
                                            @foreach($priorities as $priority)
                                                <option value="{{ $priority }}" @selected(($claim->priority ?: 'normal') === $priority)>
                                                    {{ trans('plugins/car-rentals::disputes.priority_' . $priority) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-1">
                                        <button class="btn btn-sm btn-primary w-100" type="submit">✓</button>
                                    </div>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="text-center text-muted py-4">{{ trans('plugins/car-rentals::disputes.claims_none') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($claims->hasPages())
            <div class="card-footer">
                {!! $claims->links() !!}
            </div>
        @endif
    </div>

    <script>
        (() => {
            document.querySelectorAll('.claim-quick-update-form').forEach((form) => {
                form.addEventListener('submit', async (event) => {
                    event.preventDefault();

                    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                    const formData = new FormData(form);
                    const payload = {
                        status: formData.get('status'),
                        assignee_id: formData.get('assignee_id') || null,
                        approved_amount: formData.get('approved_amount') || null,
                        priority: formData.get('priority') || null,
                    };

                    const response = await fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': token || '',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: JSON.stringify({
                            _method: 'PUT',
                            ...payload,
                        }),
                    });

                    const result = await response.json().catch(() => ({}));

                    if (!response.ok) {
                        alert(result?.message || 'Failed to update claim.');
                        return;
                    }

                    window.location.reload();
                });
            });
        })();
    </script>
@endsection

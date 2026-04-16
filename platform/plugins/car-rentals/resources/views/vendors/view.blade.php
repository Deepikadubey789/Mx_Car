@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
    <div class="row">
        <div class="col-md-3">
            <x-core::card>
                <x-core::card.header>
                    <x-core::card.title>
                        {{ trans('plugins/car-rentals::car-rentals.vendor.information') }}
                    </x-core::card.title>
                </x-core::card.header>

                <x-core::card.body>
                    <div class="text-center mb-3">
                        <img src="{{ $vendor->avatar_url }}" alt="{{ $vendor->name }}" class="rounded-circle" width="100" height="100">
                    </div>

                    <dl class="row">
                        <dt class="col-5">{{ trans('plugins/car-rentals::car-rentals.customer.name') }}</dt>
                        <dd class="col-7">{{ $vendor->name }}</dd>

                        <dt class="col-5">{{ trans('plugins/car-rentals::car-rentals.customer.email') }}</dt>
                        <dd class="col-7">{{ $vendor->email }}</dd>

                        <dt class="col-5">{{ trans('plugins/car-rentals::car-rentals.customer.phone') }}</dt>
                        <dd class="col-7">{{ $vendor->phone ?: '—' }}</dd>

                        <dt class="col-5">{{ trans('plugins/car-rentals::car-rentals.customer.status') }}</dt>
                        <dd class="col-7">{!! BaseHelper::clean($vendor->status->toHtml()) !!}</dd>

                        <dt class="col-5">{{ trans('plugins/car-rentals::car-rentals.vendor.total_cars') }}</dt>
                        <dd class="col-7">
                            <span class="badge bg-blue text-blue-fg">{{ $vendor->cars()->count() }}</span>
                        </dd>

                        <dt class="col-5">{{ trans('plugins/car-rentals::car-rentals.vendor.total_bookings') }}</dt>
                        <dd class="col-7">
                            <span class="badge bg-green text-green-fg">{{ $vendor->vendorBookings()->count() }}</span>
                        </dd>
                    </dl>
                </x-core::card.body>
            </x-core::card>

            {{-- Verification Section --}}
            <div class="card mt-3">
                @if($vendor->is_verified)
                    <div class="card-status-top bg-success"></div>
                @else
                    <div class="card-status-top bg-warning"></div>
                @endif

                <div class="card-header">
                    <h3 class="card-title">
                        <x-core::icon name="ti ti-shield-check" />
                        {{ trans('plugins/car-rentals::car-rentals.vendor.verification_section') }}
                    </h3>
                </div>

                <div class="card-body">
                    @if($vendor->is_verified)
                        <div class="alert alert-success" role="alert">
                            <div class="d-flex">
                                <div>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                        <path d="M5 12l5 5l10 -10"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="alert-title">{{ trans('plugins/car-rentals::car-rentals.vendor.verified') }}</h4>
                                    <div class="text-secondary">{{ trans('plugins/car-rentals::car-rentals.vendor.vendor_verified_successfully') }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-12">
                                <div class="datagrid">
                                    @if($vendor->verifiedBy)
                                        <div class="datagrid-item">
                                            <div class="datagrid-title">{{ trans('plugins/car-rentals::car-rentals.customer.verified_by') }}</div>
                                            <div class="datagrid-content">
                                                <strong>{{ $vendor->verifiedBy->name }}</strong>
                                            </div>
                                        </div>
                                    @endif

                                    @if($vendor->verified_at)
                                        <div class="datagrid-item">
                                            <div class="datagrid-title">{{ trans('plugins/car-rentals::car-rentals.customer.verified_at') }}</div>
                                            <div class="datagrid-content">
                                                {{ $vendor->verified_at->format('M d, Y H:i') }}
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            @if($vendor->verification_note)
                                <div class="col-12">
                                    <div class="card bg-blue-lt">
                                        <div class="card-body">
                                            <h4 class="card-title">
                                                <x-core::icon name="ti ti-notes" />
                                                {{ trans('plugins/car-rentals::car-rentals.customer.verification_note') }}
                                            </h4>
                                            <p class="text-secondary mb-0">{{ $vendor->verification_note }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="mt-3">
                            <button type="button" class="btn btn-outline-warning w-100" data-bs-toggle="modal" data-bs-target="#unverify-vendor-modal">
                                <x-core::icon name="ti ti-shield-x" />
                                {{ trans('plugins/car-rentals::car-rentals.vendor.unverify_vendor') }}
                            </button>
                        </div>
                    @else
                        <div class="alert alert-warning" role="alert">
                            <div class="d-flex">
                                <div>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                        <circle cx="12" cy="12" r="9"></circle>
                                        <line x1="12" y1="8" x2="12" y2="12"></line>
                                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="alert-title">{{ trans('plugins/car-rentals::car-rentals.vendor.not_verified') }}</h4>
                                    <div class="text-secondary">{{ trans('plugins/car-rentals::car-rentals.vendor.vendor_not_verified_yet') }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="text-center py-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg text-muted mb-3" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <path d="M12 3a12 12 0 0 0 8.5 3a12 12 0 0 1 -8.5 15a12 12 0 0 1 -8.5 -15a12 12 0 0 0 8.5 -3"></path>
                                <circle cx="12" cy="11" r="1"></circle>
                                <line x1="12" y1="12" x2="12" y2="14.5"></line>
                            </svg>
                            <h3>{{ trans('plugins/car-rentals::car-rentals.vendor.verification_pending') }}</h3>
                            <p class="text-muted">{{ trans('plugins/car-rentals::car-rentals.vendor.click_verify_to_approve') }}</p>

                            <button type="button" class="btn btn-success btn-lg" data-bs-toggle="modal" data-bs-target="#verify-vendor-modal">
                                <x-core::icon name="ti ti-shield-check" />
                                {{ trans('plugins/car-rentals::car-rentals.vendor.verify_vendor') }}
                            </button>
                        </div>
                    @endif
                </div>
            </div>
            {{-- Quality Score Widget --}}
            <div class="card mt-3">
                @php
                    $badgeColors = ['all_star' => 'success', 'top_host' => 'primary', 'rising_star' => 'warning'];
                    $badgeLabels = ['all_star' => '⭐ All-Star Host', 'top_host' => '🏆 Top Host', 'rising_star' => '🌟 Rising Star'];
                    $effectiveBadge = $qualityScore?->badge_override ? $qualityScore?->override_badge : $qualityScore?->badge_tier;
                    $badgeColor = $badgeColors[$effectiveBadge] ?? 'secondary';
                    $badgeLabel = $badgeLabels[$effectiveBadge] ?? 'No Badge';
                @endphp

                <div class="card-header">
                    <h3 class="card-title">
                        <x-core::icon name="ti ti-award" />
                        Host Quality Score
                    </h3>
                    @if($effectiveBadge)
                        <div class="card-options">
                            <span class="badge bg-{{ $badgeColor }} text-{{ $badgeColor }}-fg">{{ $badgeLabel }}</span>
                        </div>
                    @endif
                </div>

                <div class="card-body">
                    @if($qualityScore)
                        {{-- Total Score --}}
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="fw-bold">Total Score</span>
                                <span class="fw-bold">{{ $qualityScore->total_score }}/100</span>
                            </div>
                            <div class="progress" style="height: 10px;">
                                <div class="progress-bar bg-{{ $badgeColor }}"
                                     style="width: {{ $qualityScore->total_score }}%"></div>
                            </div>
                        </div>

                        {{-- Individual Metrics --}}
                        <div class="datagrid">
                            <div class="datagrid-item">
                                <div class="datagrid-title">⭐ Rating Score</div>
                                <div class="datagrid-content">
                                    {{ $qualityScore->rating_score }}/100
                                    <small class="text-muted">(avg {{ $qualityScore->avg_rating }}/5)</small>
                                </div>
                            </div>
                            <div class="datagrid-item">
                                <div class="datagrid-title">✅ Completion Rate</div>
                                <div class="datagrid-content">{{ $qualityScore->completion_rate }}%</div>
                            </div>
                            <div class="datagrid-item">
                                <div class="datagrid-title">❌ Cancellation Score</div>
                                <div class="datagrid-content">{{ $qualityScore->cancellation_score }}/100</div>
                            </div>
                            <div class="datagrid-item">
                                <div class="datagrid-title">⚡ Response Score</div>
                                <div class="datagrid-content">
                                    {{ $qualityScore->response_score }}/100
                                    <small class="text-muted">({{ $qualityScore->avg_response_hours }}h avg)</small>
                                </div>
                            </div>
                            <div class="datagrid-item">
                                <div class="datagrid-title">📦 Total Bookings</div>
                                <div class="datagrid-content">{{ $qualityScore->total_bookings }}</div>
                            </div>
                            <div class="datagrid-item">
                                <div class="datagrid-title">🕒 Last Calculated</div>
                                <div class="datagrid-content">
                                    {{ $qualityScore->last_calculated_at?->diffForHumans() ?? '—' }}
                                </div>
                            </div>
                        </div>

                        {{-- Admin Badge Override --}}
                        <div class="mt-3">
                            <form method="POST"
                                  action="{{ route('car-rentals.vendors.override-badge', $vendor->id) }}">
                                @csrf
                                <div class="mb-2">
                                    <label class="form-label fw-bold">Admin Badge Override</label>
                                    <select name="override_badge" class="form-select form-select-sm">
                                        <option value="">-- System Badge Use Karo --</option>
                                        <option value="all_star"    {{ $qualityScore->override_badge == 'all_star'    ? 'selected' : '' }}>⭐ All-Star Host</option>
                                        <option value="top_host"    {{ $qualityScore->override_badge == 'top_host'    ? 'selected' : '' }}>🏆 Top Host</option>
                                        <option value="rising_star" {{ $qualityScore->override_badge == 'rising_star' ? 'selected' : '' }}>🌟 Rising Star</option>
                                    </select>
                                </div>
                                <div class="mb-2 form-check">
                                    <input type="checkbox" class="form-check-input" name="badge_override"
                                           id="badge_override" value="1"
                                           {{ $qualityScore->badge_override ? 'checked' : '' }}>
                                    <label class="form-check-label" for="badge_override">
                                        Override enable karo
                                    </label>
                                </div>
                                <button type="submit" class="btn btn-sm btn-outline-primary w-100">
                                    <x-core::icon name="ti ti-device-floppy" /> Badge Save Karo
                                </button>
                            </form>
                        </div>
                    @else
                        <div class="text-center py-3 text-muted">
                            <x-core::icon name="ti ti-chart-bar" />
                            <p class="mt-2">Score abhi calculate nahi hua.<br>
                                <small>Run karo: <code>php artisan vendor:recalculate-scores --vendor_id={{ $vendor->id }}</code></small>
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-9">
            <x-core::card>
                <x-core::card.header>
                    <x-core::card.title>
                        {{ trans('plugins/car-rentals::car-rentals.vendor.recent_activity') }}
                    </x-core::card.title>
                </x-core::card.header>

                <x-core::card.body>
                    <div class="row">
                        <div class="col-md-6">
                            <h4>{{ trans('plugins/car-rentals::car-rentals.vendor.recent_cars') }}</h4>
                            @if($vendor->cars()->count() > 0)
                                <div class="list-group">
                                    @foreach($vendor->cars()->latest()->limit(5)->get() as $car)
                                        <a href="{{ route('car-rentals.cars.edit', $car->id) }}" class="list-group-item list-group-item-action">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h5 class="mb-1">{{ $car->name }}</h5>
                                                    <small>{{ $car->created_at->diffForHumans() }}</small>
                                                </div>
                                                {!! BaseHelper::clean($car->status->toHtml()) !!}
                                            </div>
                                        </a>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-muted">{{ trans('plugins/car-rentals::car-rentals.vendor.no_cars_yet') }}</p>
                            @endif
                        </div>

                        <div class="col-md-6">
                            <h4>{{ trans('plugins/car-rentals::car-rentals.vendor.recent_bookings') }}</h4>
                            @if($vendor->vendorBookings()->count() > 0)
                                <div class="list-group">
                                    @foreach($vendor->vendorBookings()->latest()->limit(5)->get() as $booking)
                                        <a href="{{ route('car-rentals.bookings.edit', $booking->id) }}" class="list-group-item list-group-item-action">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h5 class="mb-1">{{ $booking->booking_number }}</h5>
                                                    <small>{{ $booking->created_at->diffForHumans() }}</small>
                                                </div>
                                                {!! BaseHelper::clean($booking->status->toHtml()) !!}
                                            </div>
                                        </a>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-muted">{{ trans('plugins/car-rentals::car-rentals.vendor.no_bookings_yet') }}</p>
                            @endif
                        </div>
                    </div>
                </x-core::card.body>
            </x-core::card>
        </div>
    </div>
@endsection

@push('footer')
    @if(!$vendor->is_verified)
        <x-core::modal
            id="verify-vendor-modal"
            :title="trans('plugins/car-rentals::car-rentals.vendor.verify_vendor_confirmation')"
            button-id="confirm-verify-button"
            :button-label="trans('plugins/car-rentals::car-rentals.vendor.verify_vendor')"
            button-class="btn-success"
            size="md"
        >
            <x-core::form :url="route('car-rentals.vendors.verify', $vendor->id)">
                <div class="alert alert-info" role="alert">
                    <div class="d-flex">
                        <div>
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <circle cx="12" cy="12" r="9"></circle>
                                <line x1="12" y1="8" x2="12.01" y2="8"></line>
                                <polyline points="11 12 12 12 12 16 13 16"></polyline>
                            </svg>
                        </div>
                        <div>
                            <h4 class="alert-title">{{ trans('plugins/car-rentals::car-rentals.vendor.verify_vendor_confirmation') }}</h4>
                            <div class="text-secondary">{{ trans('plugins/car-rentals::car-rentals.vendor.verify_vendor_confirmation_desc', ['name' => $vendor->name]) }}</div>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">
                        <x-core::icon name="ti ti-notes" />
                        {{ trans('plugins/car-rentals::car-rentals.customer.verification_note') }}
                    </label>
                    <textarea
                        class="form-control"
                        name="verification_note"
                        rows="3"
                        placeholder="{{ trans('plugins/car-rentals::car-rentals.customer.verification_note_placeholder') }}"
                    ></textarea>
                    <small class="form-hint">{{ trans('plugins/car-rentals::car-rentals.customer.verification_note_helper') }}</small>
                </div>
            </x-core::form>
        </x-core::modal>
    @else
        <x-core::modal
            id="unverify-vendor-modal"
            :title="trans('plugins/car-rentals::car-rentals.vendor.unverify_vendor_confirmation')"
            button-id="confirm-unverify-button"
            :button-label="trans('plugins/car-rentals::car-rentals.vendor.unverify_vendor')"
            button-class="btn-warning"
            size="md"
        >
            <x-core::form :url="route('car-rentals.vendors.unverify', $vendor->id)">
                <div class="alert alert-warning" role="alert">
                    <div class="d-flex">
                        <div>
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <path d="M10.24 3.957l-8.422 14.06a1.989 1.989 0 0 0 1.7 2.983h16.845a1.989 1.989 0 0 0 1.7 -2.983l-8.423 -14.06a1.989 1.989 0 0 0 -3.4 0z"></path>
                                <path d="M12 9v4"></path>
                                <path d="M12 17h.01"></path>
                            </svg>
                        </div>
                        <div>
                            <h4 class="alert-title">{{ trans('plugins/car-rentals::car-rentals.vendor.unverify_vendor_confirmation') }}</h4>
                            <div class="text-secondary">{{ trans('plugins/car-rentals::car-rentals.vendor.unverify_vendor_confirmation_desc', ['name' => $vendor->name]) }}</div>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">
                        <x-core::icon name="ti ti-notes" />
                        {{ trans('plugins/car-rentals::car-rentals.customer.verification_note') }}
                    </label>
                    <textarea
                        class="form-control"
                        name="verification_note"
                        rows="3"
                        placeholder="{{ trans('plugins/car-rentals::car-rentals.customer.verification_note_placeholder') }}"
                    ></textarea>
                    <small class="form-hint">{{ trans('plugins/car-rentals::car-rentals.customer.verification_note_helper') }}</small>
                </div>
            </x-core::form>
        </x-core::modal>
    @endif
@endpush

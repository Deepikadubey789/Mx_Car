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

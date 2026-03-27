@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
    @if(!$customer->confirmed_at)
    <x-core::alert type="warning" class="mb-3">
        <div>
            <strong>{{ trans('plugins/car-rentals::car-rentals.customer.email_not_verified') }}</strong>
            <div class="text-muted small">{{ trans('plugins/car-rentals::car-rentals.customer.verify_email_warning_message') }}</div>
            <div class="mt-3">
                <x-core::button
                    type="button"
                    color="warning"
                    icon="ti ti-mail-check"
                    data-bs-toggle="modal"
                    data-bs-target="#verify-email-modal"
                >
                    {{ trans('plugins/car-rentals::car-rentals.customer.verify_email_button') }}
                </x-core::button>
                <x-core::button
                    type="button"
                    color="info"
                    icon="ti ti-mail"
                    data-bs-toggle="modal"
                    data-bs-target="#resend-confirmation-modal"
                    class="ms-2"
                >
                    {{ trans('plugins/car-rentals::car-rentals.customer.resend_confirmation_button') }}
                </x-core::button>
            </div>
        </div>
    </x-core::alert>
    @endif

    <div class="row">
        <div class="col-md-3">
            <x-core::card>
                <x-core::card.header>
                    <x-core::card.title>
                        {{ trans('plugins/car-rentals::car-rentals.customer.information') }}
                    </x-core::card.title>
                </x-core::card.header>

                <x-core::card.body>
                    <div class="text-center mb-3">
                        <img src="{{ $customer->avatar_url }}" alt="{{ $customer->name }}" class="rounded-circle" width="100" height="100">
                    </div>

                    <dl class="row">
                        <dt class="col-5">{{ trans('plugins/car-rentals::car-rentals.customer.name') }}</dt>
                        <dd class="col-7">{{ $customer->name }}</dd>

                        <dt class="col-5">{{ trans('plugins/car-rentals::car-rentals.customer.email') }}</dt>
                        <dd class="col-7">
                            {{ $customer->email }}
                            @if($customer->confirmed_at)
                                <span class="badge bg-green text-green-fg ms-2">{{ trans('plugins/car-rentals::car-rentals.customer.email_verified') }}</span>
                            @else
                                <span class="badge bg-yellow text-yellow-fg ms-2">{{ trans('plugins/car-rentals::car-rentals.customer.email_not_verified') }}</span>
                            @endif
                        </dd>

                        <dt class="col-5">{{ trans('plugins/car-rentals::car-rentals.customer.phone') }}</dt>
                        <dd class="col-7">{{ $customer->phone ?: '—' }}</dd>

                        <dt class="col-5">{{ trans('plugins/car-rentals::car-rentals.customer.status') }}</dt>
                        <dd class="col-7">{!! BaseHelper::clean($customer->status->toHtml()) !!}</dd>

                        <dt class="col-5">{{ trans('plugins/car-rentals::car-rentals.car.enums.vendor_status') }}</dt>
                        <dd class="col-7">
                            @if($customer->is_vendor)
                                <span class="badge bg-green text-green-fg">{{ trans('plugins/car-rentals::car-rentals.car.enums.is_vendor') }}</span>
                            @else
                                <span class="badge bg-cyan text-cyan-fg">{{ trans('plugins/car-rentals::car-rentals.car.enums.not_vendor') }}</span>
                            @endif
                        </dd>
                    </dl>
                </x-core::card.body>
            </x-core::card>

            @if(!$customer->is_vendor)
            <x-core::card class="mt-3">
                <x-core::card.header>
                    <x-core::card.title>
                        {{ trans('plugins/car-rentals::car-rentals.customer.upgrade_to_vendor_title') }}
                    </x-core::card.title>
                </x-core::card.header>

                <x-core::card.body>
                    <p>{{ trans('plugins/car-rentals::car-rentals.customer.upgrade_to_vendor_description') }}</p>
                    <x-core::button
                        type="button"
                        color="primary"
                        icon="ti ti-arrow-up-circle"
                        data-bs-toggle="modal"
                        data-bs-target="#upgrade-to-vendor-modal"
                    >
                        {{ trans('plugins/car-rentals::car-rentals.customer.upgrade_to_vendor_button') }}
                    </x-core::button>
                </x-core::card.body>
            </x-core::card>
            @endif
        </div>

        <div class="col-md-9">
            <x-core::card>
                <x-core::card.header>
                    <x-core::card.title>
                        {{ trans('plugins/car-rentals::car-rentals.customer.recent_activity') }}
                    </x-core::card.title>
                </x-core::card.header>
                
                <x-core::card.body>
                    <p>{{ trans('plugins/car-rentals::car-rentals.customer.recent_activity_description') }}</p>
                </x-core::card.body>
            </x-core::card>
        </div>
    </div>

    @if(!$customer->confirmed_at)
    <x-core::modal.action
        id="verify-email-modal"
        type="success"
        :title="trans('plugins/car-rentals::car-rentals.customer.verify_email_confirmation')"
        :description="trans('plugins/car-rentals::car-rentals.customer.verify_email_confirmation_desc', ['name' => $customer->name, 'email' => $customer->email])"
        :submit-button-label="trans('plugins/car-rentals::car-rentals.customer.verify_email_button')"
        :submit-button-attrs="['id' => 'confirm-verify-email', 'data-url' => route('car-rentals.customers.verify-email', $customer)]"
        size="md"
    />

    <x-core::modal.action
        id="resend-confirmation-modal"
        type="info"
        :title="trans('plugins/car-rentals::car-rentals.customer.resend_confirmation_title')"
        :description="trans('plugins/car-rentals::car-rentals.customer.resend_confirmation_desc', ['name' => $customer->name, 'email' => $customer->email])"
        :submit-button-label="trans('plugins/car-rentals::car-rentals.customer.resend_confirmation_button')"
        :submit-button-attrs="['id' => 'confirm-resend-confirmation', 'data-url' => route('car-rentals.customers.resend-confirmation', $customer)]"
        size="md"
    />
    @endif

    @if(!$customer->is_vendor)
    <x-core::modal.action
        id="upgrade-to-vendor-modal"
        type="primary"
        :title="trans('plugins/car-rentals::car-rentals.customer.upgrade_to_vendor_confirmation')"
        :description="trans('plugins/car-rentals::car-rentals.customer.upgrade_to_vendor_confirmation_desc', ['name' => $customer->name])"
        :submit-button-label="trans('plugins/car-rentals::car-rentals.customer.upgrade_to_vendor_button')"
        :submit-button-attrs="['id' => 'confirm-upgrade-to-vendor', 'data-url' => route('car-rentals.customers.upgrade-to-vendor', $customer)]"
        size="md"
    />
    @endif
@endsection
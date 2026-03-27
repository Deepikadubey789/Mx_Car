@if($car->author && $car->author->id && !get_car_rentals_setting('hide_owner_info', false))
<div class="group-collapse-expand">
    <button class="btn btn-collapse" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOwnerInfo" aria-expanded="true" aria-controls="collapseOwnerInfo">
        <strong class="heading-6">{{ __('Owner Information') }}</strong>
        <svg width="12" height="7" viewBox="0 0 12 7" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M1 1L6 6L11 1" stroke="" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
        </svg>
    </button>

    <div class="collapse show" id="collapseOwnerInfo">
        <div class="card card-body owner-info-card">
            <!-- Owner Profile Header -->
            <div class="owner-profile-header mb-4">
                <div class="d-flex align-items-center">
                    <div class="owner-avatar-wrapper me-4">
                        <div class="owner-avatar-container">
                            <img src="{{ $car->author->avatar_url }}" alt="{{ $car->author->name }}" class="owner-avatar">
                            <div class="owner-badge">
                                <img src="{{ Theme::asset()->url('images/icons/owner.svg') }}" alt="{{ __('Owner') }}" width="16" height="16">
                            </div>
                        </div>
                    </div>
                    <div class="owner-details">
                        <h4 class="owner-name mb-1">
                            {{ $car->author->name }}
                            @if($car->author instanceof \Botble\CarRentals\Models\Customer)
                                {!! $car->author->badge !!}
                            @endif
                        </h4>
                        <p class="owner-title text-muted mb-2">{{ __('Car Owner') }}</p>
                        <div class="owner-since d-flex align-items-center">
                            <img src="{{ Theme::asset()->url('images/icons/calendar.svg') }}" alt="{{ __('Member Since') }}" width="16" height="16" class="me-2">
                            <span class="text-sm text-muted">{{ __('Member since :date', ['date' => Theme::formatDate($car->author->created_at, 'M Y')]) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Information -->
            @php
                $showPhone = $car->author->phone && !get_car_rentals_setting('hide_owner_phone', false);
                $showEmail = $car->author->email && !get_car_rentals_setting('hide_owner_email', false);
                $showWhatsApp = $car->author->whatsapp && !get_car_rentals_setting('hide_owner_whatsapp', false);
            @endphp
            @if($showPhone || $showEmail)
            <div class="owner-contact-info">
                <h6 class="contact-title mb-3">{{ __('Contact Information') }}</h6>
                <div class="row g-3">
                    @if($showPhone)
                    <div class="col-md-6">
                        <div class="contact-item">
                            <div class="contact-icon">
                                <img src="{{ Theme::asset()->url('images/icons/phone-black.svg') }}" alt="{{ __('Phone') }}" width="20" height="20">
                            </div>
                            <div class="contact-details">
                                <span class="contact-label">{{ __('Phone') }}</span>
                                <a href="tel:{{ $car->author->phone }}" class="contact-value">{{ $car->author->phone }}</a>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($showEmail)
                    <div class="col-md-6">
                        <div class="contact-item">
                            <div class="contact-icon">
                                {!! BaseHelper::renderIcon('ti ti-mail', attributes: ['style' => 'width: 20px; height: 20px']) !!}
                            </div>
                            <div class="contact-details">
                                <span class="contact-label">{{ __('Email') }}</span>
                                {!! Html::mailto($car->author->email, attributes: ['class' => 'contact-value']) !!}
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            @if($showWhatsApp)
            <div class="whatsapp-contact-section mt-4 pt-4 text-center">
                <div class="mb-3">
                    <p class="mb-2">{{ __('Or get instant response via WhatsApp') }}</p>
                </div>
                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $car->author->whatsapp) }}" target="_blank" class="contact-whatsapp-btn justify-content-center">
                    <x-core::icon name="ti ti-brand-whatsapp" />
                    <span>{{ __('Chat on WhatsApp') }}</span>
                </a>
            </div>
            @endif
        </div>
    </div>
</div>
@endif

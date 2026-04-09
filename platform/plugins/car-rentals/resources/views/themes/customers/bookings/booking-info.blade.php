@php
    $route ??= 'invoices.generate';
    $buttonClass ??= 'btn-primary';
    $displayBookingStatus ??= true;
@endphp

@if ($booking)
    <div class="customer-booking-info">
    <div class="row">
        <div class="col-lg-4">
            <strong>{{ __('Booking Information') }}:</strong> {{ $booking->booking_number }}
        </div>

        <div class="col-lg-4">
            <strong>{{ __('Time') }}:</strong> {{ $booking->created_at }}
        </div>
    </div>

    <div class="row">
        <div class="col-lg-4">
            <strong>{{ __('Full Name') }}:</strong> {{ $booking->customer_name }}
        </div>

        <div class="col-lg-4">
            <strong>{{ __('Email') }}:</strong> <a href="mailto:{{ $booking->customer_email }}">{{ $booking->customer_email }}</a>
        </div>

        @if($customerPhone = $booking->customer_phone)
            <div class="col-lg-4">
                <strong>{{ __('Phone') }}:</strong> <a href="mailto:{{ $customerPhone }}">{{ $customerPhone }}</a>
            </div>
        @endif

        <div class="col-lg-4">
            <strong>{{ __('Vendor Name') }}:</strong>
            {{ $booking->vendor->name ?: __('N/A') }}
        </div>

        <div class="col-lg-4">
            <strong>{{ __('Vendor Email') }}:</strong>
            @if ($booking->vendor->email)
                <a href="mailto:{{ $booking->vendor->email }}">{{ $booking->vendor->email }}</a>
            @else
                {{ __('N/A') }}
            @endif
        </div>
    </div>

    <div class="row">
        <div class="col-lg-4">
            <strong>{{ __('Car') }}:</strong>
            @if ($booking->car->car->exists && ($car = $booking->car->car))
                <a href="{{ $car->url }}" target="_blank">{{ $car->name }}</a>
            @else
                {{ $booking->car->car_name }}
            @endif
        </div>

        <div class="col-lg-4">
            <strong>{{ __('Rental Start Date') }}:</strong>
            {{ $booking->car->rental_start_date_formatted }}
        </div>

        <div class="col-lg-4">
            <strong>{{ __('Rental End Date') }}:</strong>
            {{ $booking->car->rental_end_date_formatted }}
        </div>
    </div>

    <div class="mb-4 mt-4">
        <h6>{{ __('Car') }}</h6>
        <x-core::table>
            <x-core::table.header>
                <x-core::table.header.cell>
                    {{ __('Name') }}
                </x-core::table.header.cell>
                <x-core::table.header.cell class="text-center">
                    {{ __('Rental Start Date') }}
                </x-core::table.header.cell>
                <x-core::table.header.cell class="text-center">
                    {{ __('Rental End Date') }}
                </x-core::table.header.cell>
                <x-core::table.header.cell class="text-center">
                    {{ __('Price') }}
                </x-core::table.header.cell>
                <x-core::table.header.cell class="text-center">
                    {{ __('Tax') }}
                </x-core::table.header.cell>
            </x-core::table.header>
            <x-core::table.body>
                <x-core::table.body.row>
                    <x-core::table.body.cell
                        class="text-start"
                    >{{ $booking->car->car_name }}</x-core::table.body.cell>
                    <x-core::table.body.cell
                        class="text-center"
                        style="vertical-align: middle !important;"
                    >{{ $booking->car->rental_start_date_formatted }}</x-core::table.body.cell>
                    <x-core::table.body.cell
                        class="text-center"
                        style="vertical-align: middle !important;"
                    >{{ $booking->car->rental_end_date_formatted }}</x-core::table.body.cell>
                    <x-core::table.body.cell
                        class="text-center"
                        style="vertical-align: middle !important;"
                    ><strong>{{ format_price($booking->car->price) }}</strong></x-core::table.body.cell>
                    <x-core::table.body.cell
                        class="text-center"
                        style="vertical-align: middle !important;"
                    ><strong>{{ format_price($booking->tax_amount, $booking->currency_id) }}</strong></x-core::table.body.cell>
                </x-core::table.body.row>
            </x-core::table.body>
        </x-core::table>
    </div>

    @if ($booking->services->isNotEmpty())
        <h6>{{ __('Services') }}</h6>
        <x-core::table>
            <x-core::table.header>
                <x-core::table.header.cell>
                    {{ __('Name') }}
                </x-core::table.header.cell>
                <x-core::table.header.cell class="text-center">
                    {{ __('Price') }}
                </x-core::table.header.cell>
                <x-core::table.header.cell class="text-center">
                    {{ __('Total') }}
                </x-core::table.header.cell>
            </x-core::table.header>
            <x-core::table.body>
                @foreach ($booking->services->unique() as $service)
                    <x-core::table.body.row>
                        <x-core::table.body.cell style="vertical-align: middle !important;">
                            {{ $service->name }}
                        </x-core::table.body.cell>
                        <x-core::table.body.cell class="text-center">
                            {{ format_price($service->price, $booking->currency_id) }}
                        </x-core::table.body.cell>
                        <x-core::table.body.cell class="text-center">
                            {{ format_price($service->price, $booking->currency_id) }}
                        </x-core::table.body.cell>
                    </x-core::table.body.row>
                @endforeach
            </x-core::table.body>
        </x-core::table>
        <br>
    @endif

    {{-- NEW: Insurances Table --}}
    @if ($booking->insurances->isNotEmpty())
        <h6>{{ __('Insurances') }}</h6>
        <x-core::table>
            <x-core::table.header>
                <x-core::table.header.cell>
                    {{ __('Coverage Plan') }}
                </x-core::table.header.cell>
                <x-core::table.header.cell class="text-center">
                    {{ __('Price') }}
                </x-core::table.header.cell>
            </x-core::table.header>
            <x-core::table.body>
                @foreach ($booking->insurances->unique() as $insurance)
                    <x-core::table.body.row>
                        <x-core::table.body.cell style="vertical-align: middle !important;">
                            <i class="ti ti-shield-check text-success me-2"></i> {{ $insurance->name }}
                        </x-core::table.body.cell>
                        <x-core::table.body.cell class="text-center">
                            {{ format_price($insurance->price, $booking->currency_id) }}
                        </x-core::table.body.cell>
                    </x-core::table.body.row>
                @endforeach
            </x-core::table.body>
        </x-core::table>
        <br>
    @endif

    <div class="row">
        <div class="col-lg-4">
            <strong>{{ __('Sub Total') }}:</strong>
            {{ format_price($booking->sub_total, $booking->currency_id) }}
        </div>

        <div class="col-lg-4">
            <strong>{{ __('Discount Amount') }}:</strong>
            {{ format_price($booking->coupon_amount, $booking->currency_id) }}
        </div>

        <div class="col-lg-4">
            <strong>{{ __('Tax Amount') }}:</strong>
            {{ format_price($booking->tax_amount, $booking->currency_id) }}
        </div>

        @if ($booking->fee_amount > 0)
            <div class="col-lg-4">
                <strong>{{ $booking->fee_name ?: __('Service Fee') }}:</strong>
                {{ format_price($booking->fee_amount, $booking->currency_id) }}
            </div>
        @endif

        @if ($booking->deposit_amount > 0)
            <div class="col-lg-4">
                <strong>
                    {{ __('Refundable Deposit') }}
                    @if ($booking->deposit_type === 'fixed')
                        ({{ __('Fixed') }})
                    @else
                        ({{ (float) ($booking->deposit_rate ?? 0) }}%)
                    @endif:
                </strong>
                {{ format_price($booking->deposit_amount, $booking->currency_id) }}
            </div>

            @if ($booking->deposit_risk_level)
                <div class="col-lg-4">
                    <strong>{{ __('Deposit risk tier') }}:</strong>
                    <span class="text-capitalize">{{ $booking->deposit_risk_level }}</span>
                </div>
            @endif

            @if ((float) $booking->deposit_risk_multiplier > 1)
                <div class="col-lg-4">
                    <strong>{{ __('Deposit multiplier') }}:</strong>
                    x{{ number_format((float) $booking->deposit_risk_multiplier, 2) }}
                </div>
            @endif

            @if ($booking->deposit_hold_status)
                @php
                    $statusMap = [
                        'pending_authorization' => 'warning',
                        'authorized' => 'warning',
                        'release_pending_provider_expiry' => 'info',
                        'released' => 'success',
                        'captured' => 'success',
                        'captured_directly' => 'success',
                    ];
                    $statusLabel = [
                        'pending_authorization' => 'Pending Authorization',
                        'authorized' => 'Authorized Hold',
                        'release_pending_provider_expiry' => 'Release Pending',
                        'released' => 'Released',
                        'captured' => 'Captured',
                        'captured_directly' => 'Captured (No Hold)',
                    ];
                    $badgeColor = $statusMap[$booking->deposit_hold_status] ?? 'secondary';
                    $label = $statusLabel[$booking->deposit_hold_status] ?? ucwords(str_replace('_', ' ', $booking->deposit_hold_status));
                @endphp
                <div class="col-lg-4">
                    <strong>{{ __('Deposit hold status') }}:</strong>
                    <span class="badge customer-booking-info__status-badge bg-{{ $badgeColor }}">{{ $label }}</span>
                </div>
            @endif

            @if ((float) $booking->deposit_captured_amount > 0)
                <div class="col-lg-4">
                    <strong>{{ __('Deposit captured') }}:</strong>
                    <span style="color: #5cb85c; font-weight: 600;">
                        <i class="fa fa-check-circle"></i> {{ format_price($booking->deposit_captured_amount, $booking->currency_id) }}
                    </span>
                </div>
            @endif

            @if ((float) $booking->deposit_released_amount > 0)
                <div class="col-lg-4">
                    <strong>{{ __('Deposit released') }}:</strong>
                    <span style="color: #5bc0de; font-weight: 600;">
                        <i class="fa fa-undo"></i> {{ format_price($booking->deposit_released_amount, $booking->currency_id) }}
                    </span>
                </div>
            @endif

            @if (is_array($booking->deposit_risk_reasons) && count($booking->deposit_risk_reasons))
                <div class="col-lg-12">
                    <small class="text-muted">
                        {{ __('Deposit hold is adjusted by profile and vehicle risk factors: :reasons', ['reasons' => implode(', ', $booking->deposit_risk_reasons)]) }}
                    </small>
                </div>
            @endif

            <div class="col-lg-12">
                <small class="text-muted">{{ __('Security deposit is processed as an authorization hold and settled after inspection.') }}</small>
            </div>
        @endif

        <div class="col-lg-4">
            <strong>{{ __('Total') }}:</strong>
            {{ format_price($booking->amount, $booking->currency_id) }}
        </div>

        @if (is_plugin_active('payment') && $booking->payment->id)
            @auth
                <div class="col-lg-4">
                    <strong>{{ __('Payment ID') }}:</strong>
                    <a href="{{ route('payment.show', $booking->payment->id) }}" target="_blank">
                        {{ $booking->payment->charge_id }}
                        <x-core::icon name="ti ti-external-link" />
                    </a>
                </div>
            @endauth

            <div class="col-lg-4">
                <strong>{{ __('Payment method') }}:</strong>  {{ $booking->payment->payment_channel->label() }}
            </div>

            <div class="col-lg-4 customer-booking-info__status-field">
                <strong>{{ __('Payment status') }}:</strong>
                <span class="customer-booking-info__status-wrap">{!! $booking->payment->status->toHtml() !!}</span>
            </div>

            @if ($booking->payment->payment_channel == \Botble\Payment\Enums\PaymentMethodEnum::BANK_TRANSFER
                && $booking->payment->status == \Botble\Payment\Enums\PaymentStatusEnum::PENDING
            )
                <div class="col-lg-4">
                    <strong>{{ __('Payment info') }}:</strong>
                    {!! BaseHelper::clean(get_payment_setting('description', $booking->payment->payment_channel)) !!}
                </div>
            @endif
        @endif

        @if ($displayBookingStatus)
            <div class="col-lg-4 customer-booking-info__status-field">
                <strong>{{ __('Booking status') }}:</strong>
                <span class="customer-booking-info__status-wrap">{!! BaseHelper::clean($booking->status->toHtml()) !!}</span>
            </div>
        @endif
    </div>

    <div class="d-flex flex-wrap gap-2 mt-4">
        @if ((auth()->check() || $booking->customer_id) && ($invoiceId = $booking->invoice->id) && $route)
            <x-core::button
                tag="a"
                :href="route($route, ['invoice' => $invoiceId, 'type' => 'print'])"
                target="_blank"
                icon="ti ti-printer"
                :class="$buttonClass ?? ''"
            >
                {{ __('View Invoice') }}
            </x-core::button>
            <x-core::button
                tag="a"
                :href="route($route, ['invoice' => $invoiceId, 'type' => 'download'])"
                target="_blank"
                icon="ti ti-download"
                :class="$buttonClass ?? ''"
            >
                {{ __('Download Invoice') }}
            </x-core::button>
        @endif

        {{-- NEW: Rate Car Button (Customer Only) --}}
        @if ($booking->status == \Botble\CarRentals\Enums\BookingStatusEnum::COMPLETED && ! \Botble\CarRentals\Models\CarReview::where('booking_id', $booking->id)->exists())
            <x-core::button type="button" data-bs-toggle="modal" data-bs-target="#rateCarModal" icon="ti ti-star" color="warning" :class="$buttonClass ?? ''">
                {{ __('Rate Car') }}
            </x-core::button>
        @endif
    </div>

   {{-- NEW: Rate Car Modal (Customer Only) --}}
    @if ($booking->status == \Botble\CarRentals\Enums\BookingStatusEnum::COMPLETED && ! \Botble\CarRentals\Models\CarReview::where('booking_id', $booking->id)->exists())
        <div class="modal fade" id="rateCarModal" tabindex="-1" aria-labelledby="rateCarModalLabel" aria-hidden="true">
            {{-- Added modal-dialog-centered and fallback styles for perfect centering --}}
            <div class="modal-dialog modal-dialog-centered" style="max-width: 500px; margin: 3rem auto;">
                <div class="modal-content border-0 shadow-lg rounded-4" style="background: #fff;">
                    <div class="modal-header border-bottom-0 pb-0">
                        <h5 class="modal-title fs-5 fw-bold" id="rateCarModalLabel">
                            <i class="ti ti-star-filled text-warning me-2"></i>{{ __('Rate') }} {{ $booking->car->car_name }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ Route::has('public.ajax.car-reviews') ? route('public.ajax.car-reviews') : '#' }}" method="POST" class="customer-review-form">
                        @csrf
                        <input type="hidden" name="car_id" value="{{ $booking->car->car_id }}">
                        <input type="hidden" name="booking_id" value="{{ $booking->id }}">
                        <input type="hidden" name="customer_id" value="{{ auth('customer')->id() }}">
                        
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label fw-bold">{{ __('Rating') }}</label>
                                <select name="star" class="form-select rounded-3" required>
                                    <option value="5">5 {{ __('Stars') }} - {{ __('Excellent') }}</option>
                                    <option value="4">4 {{ __('Stars') }} - {{ __('Good') }}</option>
                                    <option value="3">3 {{ __('Stars') }} - {{ __('Average') }}</option>
                                    <option value="2">2 {{ __('Stars') }} - {{ __('Poor') }}</option>
                                    <option value="1">1 {{ __('Star') }} - {{ __('Terrible') }}</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">{{ __('Review') }}</label>
                                <textarea name="content" class="form-control rounded-3" rows="4" required placeholder="{{ __('Share your experience with this car...') }}"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer border-top-0 pt-0">
                            <button type="button" class="btn btn-light rounded-3" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                            <button type="submit" class="btn btn-primary rounded-3 px-4">{{ __('Submit Review') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // FIX: Move the modal to the absolute body level to escape CSS stacking traps!
                const modalEl = document.getElementById('rateCarModal');
                if (modalEl) {
                    document.body.appendChild(modalEl);
                }

                const form = document.querySelector('.customer-review-form');
                if (!form) return;

                form.addEventListener('submit', function (e) {
                    e.preventDefault();
                    
                    if (form.getAttribute('action') === '#') {
                        alert('Review route is missing!');
                        return;
                    }

                    const btn = form.querySelector('button[type="submit"]');
                    const originalText = btn.innerHTML;
                    btn.disabled = true;
                    btn.innerHTML = '{{ __('Submitting...') }}';
                    
                    const formData = new FormData(form);

                    fetch(form.getAttribute('action'), {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(res => {
                        if (res.error) {
                            alert(res.message);
                            btn.disabled = false;
                            btn.innerHTML = originalText;
                        } else {
                            alert(res.message);
                            location.reload();
                        }
                    })
                    .catch(error => {
                        alert('{{ __('An error occurred. Please try again.') }}');
                        btn.disabled = false;
                        btn.innerHTML = originalText;
                    });
                });
            });
        </script>
    @endif

    <div class="mt-5">
        @include('plugins/car-rentals::partials.trip-messaging', [
            'booking' => $booking,
            'fetchUrl' => route('customer.bookings.messages.index', $booking->id),
            'storeUrl' => route('customer.bookings.messages.store', $booking->id),
            'escalateUrl' => route('customer.bookings.messages.escalate', $booking->id)
        ])
    </div>

    @if($booking->status->getValue() === 'completed')

    <div class="mt-4 mb-2">
        <h6>{{ __('After Photos (Return)') }}</h6>
    </div>

    <div class="btn-list d-flex gap-2 mt-2 mb-3">
        <x-core::button
            type="button"
            icon="ti ti-camera"
            :class="$buttonClass ?? ''"
            onclick="document.getElementById('afterPhotosModal').style.setProperty('display','flex','important');window.scrollTo(0,0);"
        >
            {{ __('Upload Return Photos') }}
        </x-core::button>
    </div>

    @if($booking->after_photos && count($booking->after_photos) > 0)
        <div style="display:flex; flex-wrap:wrap; gap:10px; margin-bottom:12px;">
            @foreach($booking->after_photos as $photo)
                <div style="width:100px; height:100px;">
                    <img
                        src="{{ RvMedia::getImageUrl($photo, 'thumb') }}"
                        style="width:100px;height:100px;object-fit:cover;border-radius:10px;border:1px solid #e2e8f0;"
                    >
                </div>
            @endforeach
        </div>
        @if($booking->after_photos_uploaded_at)
            <small class="text-muted mt-1 d-block">
                {{ __('Uploaded') }}: {{ $booking->after_photos_uploaded_at->format('M d, Y h:i A') }}
            </small>
        @endif
    @else
        <p class="text-muted">{{ __('No return photos uploaded yet.') }}</p>
    @endif

    <div id="afterPhotosModal"
         style="display:none; position:fixed; top:0; left:0; width:100%; height:100%;
                background: rgba(0,0,0,0.65); z-index:9999; align-items:center; justify-content:center;">
        <div style="background:#fff; border-radius:8px; width:420px; max-width:95%;
                    box-shadow:0 8px 30px rgba(0,0,0,0.18); overflow:hidden;">

            <div style="padding:16px 20px; border-bottom:1px solid #e5e7eb; display:flex; align-items:center; justify-content:space-between;">
                <span style="font-size:15px; font-weight:600; color:#111827;">{{ __('Upload Return Photos') }}</span>
                <button type="button"
                    onclick="document.getElementById('afterPhotosModal').style.setProperty('display','none','important')"
                    style="background:none; border:none; cursor:pointer; color:#6b7280; font-size:18px; line-height:1; padding:0;">
                    &times;
                </button>
            </div>

            <form id="afterPhotosForm" enctype="multipart/form-data">
                @csrf
                <div style="padding:20px;">
                    <div style="display:flex; align-items:center; gap:10px; margin-bottom:6px;">
                        <input type="file" name="after_photos[]" multiple accept="image/*"
                               id="afterPhotoInput"
                               style="border:1px solid #d1d5db; border-radius:4px; padding:5px 8px; font-size:13px; color:#374151; flex:1;"
                               onchange="document.getElementById('afterPhotoCount').textContent = this.files.length + ' photo(s) selected'">
                    </div>
                    <p style="font-size:12px; color:#9ca3af; margin:0;">{{ __('Select multiple photos') }}</p>
                    <p id="afterPhotoCount" style="font-size:12px; color:#6b7280; margin-top:4px; margin-bottom:0;"></p>
                </div>

                <div style="padding:12px 20px; border-top:1px solid #f3f4f6; display:flex; justify-content:flex-end; gap:8px;">
                    <button type="button"
                        onclick="document.getElementById('afterPhotosModal').style.setProperty('display','none','important')"
                        style="padding:8px 18px; border:1px solid #d1d5db; border-radius:6px; background:#fff;
                               font-size:13px; font-weight:500; cursor:pointer; color:#374151;">
                        {{ __('Cancel') }}
                    </button>
                    <button type="submit" id="afterUploadBtn"
                        style="padding:8px 18px; border:none; border-radius:6px; background:#c0392b;
                               color:#fff; font-size:13px; font-weight:600; cursor:pointer;">
                        {{ __('Upload Photos') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    @endif

    </div>

@endif

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('afterPhotosForm');
    if (!form) return;

    form.addEventListener('submit', function (e) {
        e.preventDefault();

        const fileInput = document.getElementById('afterPhotoInput');
        if (!fileInput || fileInput.files.length === 0) {
            alert('Please select photos first.');
            return;
        }

        const formData = new FormData();
        Array.from(fileInput.files).forEach(file => {
            formData.append('after_photos[]', file);
        });
        formData.append('_token', document.querySelector('meta[name="csrf-token"]')?.content);

        const btn = form.querySelector('button[type="submit"]');
        btn.disabled = true;
        btn.innerHTML = '<i class="ti ti-loader me-1"></i>Uploading...';

        fetch('/bookings/{{ $booking->id }}/upload-after-photos', {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                document.getElementById('afterPhotosModal').style.setProperty('display', 'none', 'important');
                location.reload();
            } else {
                alert('Error: ' + (data.error || 'Upload failed'));
                btn.disabled = false;
                btn.innerHTML = 'Upload Photos';
            }
        })
        .catch(() => {
            alert('Upload failed. Please try again.');
            btn.disabled = false;
            btn.innerHTML = 'Upload Photos';
        });
    });
});
</script>

@extends('core/base::layouts.master')

@section('content')
<div class="container-xl">
    <div class="page-wrapper">
        <div class="page-header d-print-none">
            <div class="row align-items-center">
                <div class="col">
                    <h2 class="page-title">Send WhatsApp Message</h2>
                </div>
            </div>
        </div>
    </div>
    <div class="page-wrapper">
        <div class="container-xl">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            @if (session('success'))
                                <div class="alert alert-success">
                                    {{ session('success') }}
                                </div>
                            @endif

                            @if (session('error'))
                                <div class="alert alert-danger">
                                    {{ session('error') }}
                                </div>
                            @endif

                            <div class="alert alert-info">
                                Free-form text messages only work within WhatsApp's 24-hour customer service window.
                                If outside 24 hours, send an approved template message.
                            </div>

                            <form action="{{ route('car-rentals.whatsapp.send.post') }}" method="POST">
                                @csrf

                                <div class="form-group mb-3">
                                    <label class="form-label">Send Mode *</label>
                                    <select name="send_mode" id="send_mode" class="form-control @error('send_mode') is-invalid @enderror" required>
                                        <option value="text" {{ old('send_mode') === 'text' ? 'selected' : '' }}>Text (24h window only)</option>
                                        <option value="template" {{ old('send_mode', 'template') === 'template' ? 'selected' : '' }}>Meta Template (outside 24h)</option>
                                    </select>
                                    @error('send_mode')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label class="form-label">Customer *</label>
                                    <select name="customer_id" id="customer_id" class="form-control @error('customer_id') is-invalid @enderror" required>
                                        <option value="">-- Select Customer --</option>
                                        @foreach ($customers as $customer)
                                            <option value="{{ $customer['id'] }}" {{ old('customer_id') == $customer['id'] ? 'selected' : '' }}>
                                                {{ $customer['label'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('customer_id')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-row">
                                    <div class="form-group mb-3 col-md-6">
                                        <label class="form-label">Associated Booking</label>
                                        <select name="booking_id" id="booking_id" class="form-control">
                                            <option value="">-- None --</option>
                                        </select>
                                    </div>

                                    <div class="form-group mb-3 col-md-6">
                                        <label class="form-label">Associated Claim</label>
                                        <select name="claim_id" id="claim_id" class="form-control">
                                            <option value="">-- None --</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group mb-3" id="text_message_group">
                                    <label class="form-label">Message *</label>
                                    <textarea name="message" id="message" class="form-control @error('message') is-invalid @enderror" rows="6" placeholder="Enter your WhatsApp message...">{{ old('message') }}</textarea>
                                    <small class="form-text text-muted">Maximum 1000 characters. Emojis are supported.</small>
                                    @error('message')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div id="template_message_group" style="display: none;">
                                    <div class="form-group mb-3">
                                        <label class="form-label">Meta Template Name *</label>
                                        <input
                                            type="text"
                                            name="template_name"
                                            id="template_name"
                                            class="form-control @error('template_name') is-invalid @enderror"
                                            value="{{ old('template_name', 'hello_world') }}"
                                            placeholder="hello_world"
                                            list="local-template-suggestions"
                                        >
                                        <datalist id="local-template-suggestions">
                                            @foreach ($templates as $template)
                                                <option value="{{ $template->name }}"></option>
                                            @endforeach
                                        </datalist>
                                        <small class="form-text text-muted">Must match the exact template name approved in Meta WhatsApp Manager (example: hello_world).</small>
                                        @error('template_name')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="form-group mb-3">
                                        <label class="form-label">Template Language Code</label>
                                        <input type="text" name="template_language" id="template_language" class="form-control @error('template_language') is-invalid @enderror" value="{{ old('template_language', 'en_US') }}" placeholder="en_US">
                                        <small class="form-text text-muted">Use exact Meta language code for that template (example: en_US).</small>
                                        @error('template_language')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-footer">
                                    <button type="submit" class="btn btn-primary">Send Message</button>
                                    <a href="{{ route('car-rentals.customers.index') }}" class="btn btn-secondary">Cancel</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const sendModeSelect = document.getElementById('send_mode');
    const customerSelect = document.getElementById('customer_id');
    const bookingSelect = document.getElementById('booking_id');
    const claimSelect = document.getElementById('claim_id');
    const textMessageGroup = document.getElementById('text_message_group');
    const templateMessageGroup = document.getElementById('template_message_group');
    const messageField = document.getElementById('message');
    const templateNameField = document.getElementById('template_name');

    function toggleSendModeFields() {
        const mode = sendModeSelect.value;
        const isTemplate = mode === 'template';

        textMessageGroup.style.display = isTemplate ? 'none' : 'block';
        templateMessageGroup.style.display = isTemplate ? 'block' : 'none';

        messageField.required = !isTemplate;
        templateNameField.required = isTemplate;
    }

    sendModeSelect.addEventListener('change', toggleSendModeFields);
    toggleSendModeFields();

    customerSelect.addEventListener('change', function() {
        const customerId = this.value;
        if (!customerId) {
            bookingSelect.innerHTML = '<option value="">-- None --</option>';
            claimSelect.innerHTML = '<option value="">-- None --</option>';
            return;
        }

        // Fetch bookings
        fetch(`{{ route('car-rentals.whatsapp.customer-bookings') }}?customer_id=${customerId}`)
            .then(response => response.json())
            .then(data => {
                bookingSelect.innerHTML = '<option value="">-- None --</option>';
                data.forEach(booking => {
                    const option = document.createElement('option');
                    option.value = booking.id;
                    option.textContent = booking.label;
                    bookingSelect.appendChild(option);
                });
            });

        // Fetch claims
        fetch(`{{ route('car-rentals.whatsapp.customer-claims') }}?customer_id=${customerId}`)
            .then(response => response.json())
            .then(data => {
                claimSelect.innerHTML = '<option value="">-- None --</option>';
                data.forEach(claim => {
                    const option = document.createElement('option');
                    option.value = claim.id;
                    option.textContent = claim.label;
                    claimSelect.appendChild(option);
                });
            });
    });
});
</script>
@endsection

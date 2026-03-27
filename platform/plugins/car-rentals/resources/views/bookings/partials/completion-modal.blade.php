@php
    $updateRoute = auth()->check()
        ? route('car-rentals.bookings.update-completion', $booking->id)
        : route('customer.bookings.update-completion', $booking->id);
@endphp

<div class="modal fade" id="completion-modal" tabindex="-1" aria-labelledby="completion-modal-label" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="completion-modal-label">
                    {{ trans('plugins/car-rentals::booking.completion_details') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                @php
                                    $isUsingMiles = \Botble\CarRentals\Facades\CarRentalsHelper::isUsingMiles();
                                    $labelKey = $isUsingMiles ? 'completion_miles' : 'completion_kilometers';
                                    $placeholderKey = $isUsingMiles ? 'enter_miles' : 'enter_kilometers';
                                    $helpKey = $isUsingMiles ? 'completion_miles_help' : 'completion_kilometers_help';
                                @endphp
                                <x-core::form.label for="completion_miles" :value="trans('plugins/car-rentals::booking.' . $labelKey)" />
                                <x-core::form.text-input
                                    name="completion_miles"
                                    id="completion_miles"
                                    type="number"
                                    :value="old('completion_miles', $booking->completion_miles)"
                                    :placeholder="trans('plugins/car-rentals::booking.' . $placeholderKey)"
                                />
                                <x-core::form.helper-text>
                                    {{ trans('plugins/car-rentals::booking.' . $helpKey) }}
                                </x-core::form.helper-text>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <x-core::form.label for="completion_gas_level" :value="trans('plugins/car-rentals::booking.completion_gas_level')" />
                                <x-core::form.select
                                    name="completion_gas_level"
                                    id="completion_gas_level"
                                    :value="old('completion_gas_level', $booking->completion_gas_level)"
                                >
                                    <option value="">{{ trans('plugins/car-rentals::booking.select_gas_level') }}</option>
                                    <option value="empty" {{ old('completion_gas_level', $booking->completion_gas_level) == 'empty' ? 'selected' : '' }}>
                                        {{ trans('plugins/car-rentals::booking.gas_empty') }}
                                    </option>
                                    <option value="quarter" {{ old('completion_gas_level', $booking->completion_gas_level) == 'quarter' ? 'selected' : '' }}>
                                        {{ trans('plugins/car-rentals::booking.gas_quarter') }}
                                    </option>
                                    <option value="half" {{ old('completion_gas_level', $booking->completion_gas_level) == 'half' ? 'selected' : '' }}>
                                        {{ trans('plugins/car-rentals::booking.gas_half') }}
                                    </option>
                                    <option value="three_quarters" {{ old('completion_gas_level', $booking->completion_gas_level) == 'three_quarters' ? 'selected' : '' }}>
                                        {{ trans('plugins/car-rentals::booking.gas_three_quarters') }}
                                    </option>
                                    <option value="full" {{ old('completion_gas_level', $booking->completion_gas_level) == 'full' ? 'selected' : '' }}>
                                        {{ trans('plugins/car-rentals::booking.gas_full') }}
                                    </option>
                                </x-core::form.select>
                                <x-core::form.helper-text>
                                    {{ trans('plugins/car-rentals::booking.completion_gas_level_help') }}
                                </x-core::form.helper-text>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <x-core::form.label for="completion_damage_images" :value="trans('plugins/car-rentals::booking.damage_images')" />
                        <input
                            type="file"
                            name="completion_damage_images[]"
                            id="completion_damage_images"
                            class="form-control"
                            multiple
                            accept="image/*"
                        />
                        <x-core::form.helper-text>
                            {{ trans('plugins/car-rentals::booking.damage_images_help') }}
                        </x-core::form.helper-text>

                        @if ($booking->completion_damage_images)
                            @php
                                $existingImages = is_string($booking->completion_damage_images)
                                    ? json_decode($booking->completion_damage_images, true)
                                    : $booking->completion_damage_images;
                            @endphp

                            @if ($existingImages && count($existingImages) > 0)
                                <div class="mt-2">
                                    <small class="text-muted">{{ trans('plugins/car-rentals::booking.existing_images') }}:</small>
                                    <div class="row mt-1">
                                        @foreach ($existingImages as $index => $image)
                                            <div class="col-md-3 col-sm-6 mb-2">
                                                <div class="position-relative">
                                                    <img
                                                        src="{{ RvMedia::getImageUrl($image, 'thumb') }}"
                                                        alt="Damage image"
                                                        class="img-thumbnail"
                                                        style="width: 100%; height: 80px; object-fit: cover;"
                                                    >
                                                    <button
                                                        type="button"
                                                        class="btn btn-sm btn-danger position-absolute top-0 end-0"
                                                        onclick="removeExistingImage({{ $index }})"
                                                        style="padding: 2px 6px; font-size: 10px;"
                                                    >
                                                        <x-core::icon name="ti ti-x" />
                                                    </button>
                                                    <input type="hidden" name="existing_damage_images[]" value="{{ $image }}">
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        @endif
                    </div>

                    <div class="mb-3">
                        <x-core::form.label for="completion_notes" :value="trans('plugins/car-rentals::booking.completion_notes')" />
                        <x-core::form.textarea
                            name="completion_notes"
                            id="completion_notes"
                            rows="4"
                            :value="old('completion_notes', $booking->completion_notes)"
                            :placeholder="trans('plugins/car-rentals::booking.completion_notes_placeholder')"
                        />
                        <x-core::form.helper-text>
                            {{ trans('plugins/car-rentals::booking.completion_notes_help') }}
                        </x-core::form.helper-text>
                    </div>
                </div>

            <div class="modal-footer">
                <x-core::button type="button" color="secondary" data-bs-dismiss="modal">
                    {{ __('core/base::forms.cancel') }}
                </x-core::button>
                <x-core::button type="button" color="primary" icon="ti ti-device-floppy" id="save-completion-btn">
                    {{ __('core/base::forms.save') }}
                </x-core::button>
            </div>
        </div>
    </div>
</div>

<script>
function removeExistingImage(index) {
    const imageContainer = event.target.closest('.col-md-3');
    const hiddenInput = imageContainer.querySelector('input[name="existing_damage_images[]"]');

    // Remove the image from the form
    imageContainer.remove();
}

document.getElementById('save-completion-btn').addEventListener('click', function(e) {
    e.preventDefault();
    e.stopPropagation();

    const submitBtn = this;
    const originalText = submitBtn.innerHTML;

    // Disable button and show loading state
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>{{ trans("core/base::forms.save") }}';

    // Call the external form submission function
    try {
        if (typeof submitCompletionForm === 'function') {
            submitCompletionForm();
        } else {
            throw new Error('Form submission function not found.');
        }
    } catch (error) {
        console.error('Completion form error:', error);
        alert(error.message || 'An error occurred. Please refresh the page and try again.');
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    }
});
</script>

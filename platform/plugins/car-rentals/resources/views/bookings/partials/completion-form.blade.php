@php
    $updateRoute = auth()->check()
        ? route('car-rentals.bookings.update-completion', $booking->id)
        : route('customer.bookings.update-completion', $booking->id);
@endphp

<form id="completion-form" action="{{ $updateRoute }}" method="POST" enctype="multipart/form-data" style="display: none;">
    @csrf
    @method('PUT')

    <input type="hidden" name="completion_miles" id="hidden_completion_miles">
    <input type="hidden" name="completion_gas_level" id="hidden_completion_gas_level">
    <input type="hidden" name="completion_notes" id="hidden_completion_notes">

    <!-- Hidden file input for damage images -->
    <input type="file" name="completion_damage_images[]" id="hidden_completion_damage_images" multiple accept="image/*" style="display: none;">

    <!-- Hidden inputs for existing images -->
    <div id="hidden_existing_images"></div>
</form>

<script>
// Function to submit the completion form
function submitCompletionForm() {
    // Create a new FormData object for submission
    const formData = new FormData();

    // Add CSRF token and method
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ||
                     document.querySelector('input[name="_token"]')?.value ||
                     '{{ csrf_token() }}';
    formData.append('_token', csrfToken);
    formData.append('_method', 'PUT');

    // Get values from modal
    const modalMiles = document.getElementById('completion_miles')?.value || '';
    const modalGasLevel = document.getElementById('completion_gas_level')?.value || '';
    const modalNotes = document.getElementById('completion_notes')?.value || '';
    const modalFileInput = document.getElementById('completion_damage_images');
    const settlementAction = document.getElementById('deposit_settlement_action')?.value || '';
    const captureAmount = document.getElementById('deposit_capture_amount')?.value || '';

    // Check if modal elements exist
    if (!modalFileInput) {
        throw new Error('Modal form elements not found. Please refresh the page and try again.');
    }

    // Add form data
    if (modalMiles) formData.append('completion_miles', modalMiles);
    if (modalGasLevel) formData.append('completion_gas_level', modalGasLevel);
    if (modalNotes) formData.append('completion_notes', modalNotes);

    if (settlementAction === 'capture_partial') {
        const parsedAmount = parseFloat(captureAmount);

        if (!captureAmount || Number.isNaN(parsedAmount) || parsedAmount <= 0) {
            throw new Error('{{ trans('plugins/car-rentals::booking.validation.deposit_capture_amount_required') }}');
        }
    }

    if (settlementAction) formData.append('deposit_settlement_action', settlementAction);
    if (captureAmount) formData.append('deposit_capture_amount', captureAmount);

    // Add file uploads
    if (modalFileInput.files.length > 0) {
        for (let i = 0; i < modalFileInput.files.length; i++) {
            formData.append('completion_damage_images[]', modalFileInput.files[i]);
        }
    }

    // Add existing images
    const existingImages = document.querySelectorAll('#completion-modal input[name="existing_damage_images[]"]');
    existingImages.forEach(input => {
        if (input.value) {
            formData.append('existing_damage_images[]', input.value);
        }
    });

    // Use the action URL directly
    const actionUrl = '{{ $updateRoute }}';

    // Submit via fetch
    fetch(actionUrl, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(async (response) => {
        let data = null;

        try {
            data = await response.json();
        } catch (e) {
            data = null;
        }

        if (! response.ok || (data && data.error)) {
            throw new Error(data?.message || 'An error occurred while saving completion details.');
        }

        // Success - reload the page to show updated data
        window.location.reload();
    })
    .catch(error => {
        console.error('Error:', error);
        alert(error.message || 'An error occurred while saving completion details.');

        // Re-enable the save button
        const saveBtn = document.getElementById('save-completion-btn');
        if (saveBtn) {
            saveBtn.disabled = false;
            saveBtn.innerHTML = `<x-core::icon name="ti ti-device-floppy" class="me-1" />{{ trans("core/base::forms.save") }}`;
        }
    });
}
</script>

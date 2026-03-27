$(document).ready(function() {
    $(document).on('change', '.booking-form input, .booking-form select', function() {
        calculateTotal()
    })

    $(document).on('changeDate', '.booking-form input[name="rental_start_date"], .booking-form input[name="rental_end_date"]', function() {
        const $input = $(this)
        const $form = $input.closest('form')
        const validator = $form.data('validator')

        if (validator) {
            validator.element($input)

            if ($input.attr('name') === 'rental_start_date') {
                const $endDateInput = $form.find('input[name="rental_end_date"]')
                if ($endDateInput.length) {
                    validator.element($endDateInput)
                }
            }
        }

        calculateTotal()
    })

    function calculateTotal() {
        const form = $('.booking-form').find('form')
        const data = form.serialize()

        $.ajax({
            url: form.attr('data-estimate-url'),
            method: 'POST',
            data: data,
            success: function(response) {
                form.find('.pricing-summary').html(response.data)
            }
        })
    }
})

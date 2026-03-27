'use strict';

$(document).ready(function () {
    // Handle customer selection
    $(document).on('change', '#customer_id', function () {
        let customerId = $(this).val();
        if (customerId == '0') {
            // Show customer fields and clear values
            $('#name').val('').closest('.form-group').show();
            $('#email').val('').closest('.form-group').show();
            $('#phone').val('').closest('.form-group').show();
        } else {
            // Hide customer fields and fetch customer info
            $('#name').closest('.form-group').hide();
            $('#email').closest('.form-group').hide();
            $('#phone').closest('.form-group').hide();

            // Fetch customer info and fill the fields
            $.ajax({
                url: route('car-rentals.bookings.get-customer', { id: customerId }),
                type: 'GET',
                success: function(res) {
                    if (res.error) {
                        Botble.showError(res.message);
                    } else {
                        // Fill the fields with customer info
                        $('#name').val(res.data.customer.name);
                        $('#email').val(res.data.customer.email);
                        $('#phone').val(res.data.customer.phone);
                    }
                },
                error: function(error) {
                    Botble.handleError(error);
                }
            });
        }
    });

    // Trigger change on page load
    $('#customer_id').trigger('change');
});

<?php

return [
    'name' => 'Bookings',
    'create' => 'New booking',
    'reports' => 'Booking Reports',
    'calendar' => 'Booking Calendar',
    'statuses' => [
        'pending' => 'Pending',
        'processing' => 'Processing',
        'completed' => 'Completed',
        'cancelled' => 'Cancelled',
    ],
    'customer' => 'Customer',
    'amount' => 'Amount',
    'rental_period' => 'Rental Period',
    'payment_method' => 'Payment Method',
    'payment_status' => 'Payment Status',
    'booking_information' => 'Booking Information',
    'booking_period' => 'Booking Period',
    'payment_status_label' => 'Payment Status',
    'car' => 'Car',
    'calendar_item_title' => ':car (:customer)',

    // Dates
    'start_date' => 'Start Date',
    'end_date' => 'End Date',

    // Car search
    'search_cars' => 'Search Cars',
    'selected_car' => 'Selected Car',
    'please_select_dates' => 'Please select both start and end dates',
    'please_select_car' => 'Please select a car to continue with the booking',

    // Booking details
    'booking_details' => 'Booking Details',

    // Customer information
    'search_customer' => 'Search customer by name, email or phone...',
    'create_new_customer' => 'Create new customer',
    'customer_created_successfully' => 'Customer created successfully',
    'customer_not_found' => 'Customer not found',
    'customer_information' => 'Customer Information',
    'customer_name' => 'Name',
    'email' => 'Email',
    'phone' => 'Phone',
    'customer_age' => 'Age',
    'address' => 'Address',
    'city' => 'City',
    'state' => 'State/Province',
    'country' => 'Country',
    'zip' => 'ZIP/Postal Code',
    'note' => 'Note',
    'note_placeholder' => 'Enter any special requests or notes',

    // Services
    'services' => 'Additional Services',
    'day' => 'day',

    // Payment
    'payment_status' => 'Payment status',
    'transaction_id' => 'Transaction ID',
    'transaction_id_helper' => 'You can leave this field empty if the payment method is COD or Bank transfer',
    'payment_method_helper' => 'Select the payment method used for this booking',
    'payment_status_helper' => 'Current status of the payment',

    // Form placeholders
    'first_name_placeholder' => 'Enter first name',
    'last_name_placeholder' => 'Enter last name',
    'email_placeholder' => 'Enter email address',
    'phone_placeholder' => 'Enter phone number',
    'address_placeholder' => 'Enter address',
    'city_placeholder' => 'Enter city',
    'state_placeholder' => 'Enter state/province',
    'country_placeholder' => 'Enter country',
    'zip_placeholder' => 'Enter ZIP/Postal code',

    // Misc
    'no_customers_found' => 'No customers found',
    'no_cars_available' => 'No cars available for the selected dates',
    'select_car' => 'Select Car',
    'print_booking_info' => 'Print Booking Info',
    'printed_on' => 'Printed on',
    'computer_generated_document' => 'This is a computer-generated document and does not require a signature.',
    'booking_summary' => 'Booking Summary',
    'booking_details' => 'Booking Details',
    'additional_services' => 'Additional Services',
    'rental_period' => 'Rental Period',
    'to' => 'to',

    // Completion details
    'completion_details' => 'Completion Details',
    'add_completion_details' => 'Add Completion Details',
    'edit_completion_details' => 'Edit Completion Details',
    'no_completion_details' => 'No completion details have been added yet.',
    'completion_details_updated_successfully' => 'Completion details updated successfully.',

    'completion_miles' => 'Final Mileage',
    'completion_kilometers' => 'Final Kilometers',
    'miles' => 'miles',
    'kilometers' => 'kilometers',
    'enter_miles' => 'Enter final mileage',
    'enter_kilometers' => 'Enter final kilometers',
    'completion_miles_help' => 'Enter the final mileage reading when the car was returned.',
    'completion_kilometers_help' => 'Enter the final kilometer reading when the car was returned.',

    'completion_gas_level' => 'Gas Level',
    'select_gas_level' => 'Select gas level',
    'gas_empty' => 'Empty',
    'gas_quarter' => '1/4 Tank',
    'gas_half' => '1/2 Tank',
    'gas_three_quarters' => '3/4 Tank',
    'gas_full' => 'Full Tank',
    'completion_gas_level_help' => 'Select the gas level when the car was returned.',

    'damage_images' => 'Damage Images',
    'damage_image' => 'Damage Image',
    'damage_images_help' => 'Upload images of any damage found on the vehicle (max 5MB per image).',
    'existing_images' => 'Existing images',

    'completion_notes' => 'Completion Notes',
    'completion_notes_placeholder' => 'Enter any notes about the vehicle condition, damages, or other observations...',
    'completion_notes_help' => 'Add any additional notes about the vehicle condition or rental completion.',

    'completed_at' => 'Completed At',

    // Coupon fields
    'coupon_code' => 'Coupon Code',
    'coupon_amount' => 'Coupon Discount Amount',
    'enter_coupon_code' => 'Enter coupon code',
    'coupon_discount_amount' => 'Discount Amount',
    'applied_coupon' => 'Applied Coupon',

    // Validation messages
    'validation' => [
        'completion_miles_integer' => 'The mileage must be a valid number.',
        'completion_miles_min' => 'The mileage must be at least 0.',
        'completion_gas_level_invalid' => 'Please select a valid gas level.',
        'damage_image_invalid' => 'The uploaded file must be a valid image.',
        'damage_image_max_size' => 'The image size must not exceed 5MB.',
        'completion_notes_max' => 'The notes must not exceed 10,000 characters.',
    ],

    // Vendor booking actions
    'approve_booking' => 'Approve Booking',
    'cancel_booking' => 'Cancel Booking',
    'processing' => 'Processing...',
    'pending_approval_notice' => 'Pending Approval',
    'pending_approval_description' => 'This booking is awaiting your approval. Please review the details and approve or cancel the reservation.',
    'approve_booking_confirmation' => 'Are you sure you want to approve this booking? The customer will be notified.',
    'cancel_booking_confirmation' => 'Are you sure you want to cancel this booking? This action cannot be undone.',
    'booking_approved_successfully' => 'Booking has been approved successfully.',
    'booking_cancelled_successfully' => 'Booking has been cancelled successfully.',
    'cannot_approve_booking' => 'This booking cannot be approved. Only pending bookings can be approved.',
    'cannot_cancel_booking' => 'This booking cannot be cancelled. Completed or already cancelled bookings cannot be cancelled.',
];

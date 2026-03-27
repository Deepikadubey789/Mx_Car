<?php

return [
    'name' => 'Bookinger',
    'create' => 'Ny booking',
    'reports' => 'Bookingrapporter',
    'calendar' => 'Bookingkalender',
    'statuses' => [
        'pending' => 'Afventer',
        'processing' => 'Behandler',
        'completed' => 'Gennemført',
        'cancelled' => 'Annulleret',
    ],
    'customer' => 'Kunde',
    'amount' => 'Beløb',
    'rental_period' => 'Lejeperiode',
    'payment_method' => 'Betalingsmetode',
    'payment_status' => 'Betalingsstatus',
    'booking_information' => 'Bookinginformation',
    'booking_period' => 'Bookingperiode',
    'payment_status_label' => 'Betalingsstatus',
    'car' => 'Bil',
    'calendar_item_title' => ':car (:customer)',

    // Dates
    'start_date' => 'Startdato',
    'end_date' => 'Slutdato',

    // Car search
    'search_cars' => 'Søg efter biler',
    'selected_car' => 'Valgt bil',
    'please_select_dates' => 'Vælg venligst både start- og slutdato',
    'please_select_car' => 'Vælg venligst en bil for at fortsætte med bookingen',

    // Booking details
    'booking_details' => 'Bookingdetaljer',

    // Customer information
    'search_customer' => 'Søg efter kunde efter navn, e-mail eller telefon...',
    'create_new_customer' => 'Opret ny kunde',
    'customer_created_successfully' => 'Kunde oprettet',
    'customer_not_found' => 'Kunde ikke fundet',
    'customer_information' => 'Kundeinformation',
    'customer_name' => 'Navn',
    'email' => 'E-mail',
    'phone' => 'Telefon',
    'customer_age' => 'Alder',
    'address' => 'Adresse',
    'city' => 'By',
    'state' => 'Stat/provins',
    'country' => 'Land',
    'zip' => 'Postnummer',
    'note' => 'Note',
    'note_placeholder' => 'Indtast eventuelle særlige ønsker eller noter',

    // Services
    'services' => 'Ekstra tjenester',
    'day' => 'dag',

    // Payment
    'payment_status' => 'Betalingsstatus',
    'transaction_id' => 'Transaktions-ID',
    'transaction_id_helper' => 'Du kan lade dette felt være tomt, hvis betalingsmetoden er efterkrav eller bankoverførsel',
    'payment_method_helper' => 'Vælg den betalingsmetode, der bruges til denne booking',
    'payment_status_helper' => 'Aktuel status for betalingen',

    // Form placeholders
    'first_name_placeholder' => 'Indtast fornavn',
    'last_name_placeholder' => 'Indtast efternavn',
    'email_placeholder' => 'Indtast e-mailadresse',
    'phone_placeholder' => 'Indtast telefonnummer',
    'address_placeholder' => 'Indtast adresse',
    'city_placeholder' => 'Indtast by',
    'state_placeholder' => 'Indtast stat/provins',
    'country_placeholder' => 'Indtast land',
    'zip_placeholder' => 'Indtast postnummer',

    // Misc
    'no_customers_found' => 'Ingen kunder fundet',
    'no_cars_available' => 'Ingen biler tilgængelige for de valgte datoer',
    'select_car' => 'Vælg bil',
    'print_booking_info' => 'Udskriv bookinginformation',
    'printed_on' => 'Udskrevet den',
    'computer_generated_document' => 'Dette er et computergenereret dokument og kræver ikke en underskrift.',
    'booking_summary' => 'Bookingoversigt',
    'booking_details' => 'Bookingdetaljer',
    'additional_services' => 'Ekstra tjenester',
    'rental_period' => 'Lejeperiode',
    'to' => 'til',

    // Completion details
    'completion_details' => 'Afslutningsdetaljer',
    'add_completion_details' => 'Tilføj afslutningsdetaljer',
    'edit_completion_details' => 'Rediger afslutningsdetaljer',
    'no_completion_details' => 'Ingen afslutningsdetaljer er tilføjet endnu.',
    'completion_details_updated_successfully' => 'Afslutningsdetaljer opdateret.',

    'completion_miles' => 'Endelig kilometerstand',
    'completion_kilometers' => 'Endelige kilometer',
    'miles' => 'miles',
    'kilometers' => 'kilometer',
    'enter_miles' => 'Indtast endelig kilometerstand',
    'enter_kilometers' => 'Indtast endelige kilometer',
    'completion_miles_help' => 'Indtast den endelige kilometerstands aflæsning, da bilen blev returneret.',
    'completion_kilometers_help' => 'Indtast den endelige kilometer aflæsning, da bilen blev returneret.',

    'completion_gas_level' => 'Benzinniveau',
    'select_gas_level' => 'Vælg benzinniveau',
    'gas_empty' => 'Tom',
    'gas_quarter' => '1/4 tank',
    'gas_half' => '1/2 tank',
    'gas_three_quarters' => '3/4 tank',
    'gas_full' => 'Fuld tank',
    'completion_gas_level_help' => 'Vælg benzinniveauet, da bilen blev returneret.',

    'damage_images' => 'Skadesfotos',
    'damage_image' => 'Skadesfoto',
    'damage_images_help' => 'Upload billeder af eventuelle skader fundet på køretøjet (maks. 5MB pr. billede).',
    'existing_images' => 'Eksisterende billeder',

    'completion_notes' => 'Afslutningsnoter',
    'completion_notes_placeholder' => 'Indtast eventuelle noter om køretøjets tilstand, skader eller andre observationer...',
    'completion_notes_help' => 'Tilføj eventuelle yderligere noter om køretøjets tilstand eller lejeafslutning.',

    'completed_at' => 'Gennemført den',

    // Coupon fields
    'coupon_code' => 'Kuponkode',
    'coupon_amount' => 'Kuponrabatbeløb',
    'enter_coupon_code' => 'Indtast kuponkode',
    'coupon_discount_amount' => 'Rabatbeløb',
    'applied_coupon' => 'Anvendt kupon',

    // Validation messages
    'validation' => [
        'completion_miles_integer' => 'Kilometerstanden skal være et gyldigt nummer.',
        'completion_miles_min' => 'Kilometerstanden skal være mindst 0.',
        'completion_gas_level_invalid' => 'Vælg venligst et gyldigt benzinniveau.',
        'damage_image_invalid' => 'Den uploadede fil skal være et gyldigt billede.',
        'damage_image_max_size' => 'Billedstørrelsen må ikke overstige 5MB.',
        'completion_notes_max' => 'Noterne må ikke overstige 10.000 tegn.',
    ],

    // Vendor booking actions
    'approve_booking' => 'Godkend booking',
    'cancel_booking' => 'Annuller booking',
    'processing' => 'Behandler...',
    'pending_approval_notice' => 'Afventer godkendelse',
    'pending_approval_description' => 'Denne booking afventer din godkendelse. Gennemgå venligst detaljerne og godkend eller annuller bookingen.',
    'approve_booking_confirmation' => 'Er du sikker på, at du vil godkende denne booking? Kunden vil blive underrettet.',
    'cancel_booking_confirmation' => 'Er du sikker på, at du vil annullere denne booking? Denne handling kan ikke fortrydes.',
    'booking_approved_successfully' => 'Bookingen er blevet godkendt.',
    'booking_cancelled_successfully' => 'Bookingen er blevet annulleret.',
    'cannot_approve_booking' => 'Denne booking kan ikke godkendes. Kun afventende bookinger kan godkendes.',
    'cannot_cancel_booking' => 'Denne booking kan ikke annulleres. Gennemførte eller allerede annullerede bookinger kan ikke annulleres.',
];

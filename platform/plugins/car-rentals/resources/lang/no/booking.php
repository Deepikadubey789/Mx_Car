<?php

return [
    'name' => 'Bestillinger',
    'create' => 'Ny bestilling',
    'reports' => 'Bestillingsrapporter',
    'calendar' => 'Bestillingskalender',
    'statuses' => [
        'pending' => 'Venter',
        'processing' => 'Behandler',
        'completed' => 'Fullført',
        'cancelled' => 'Kansellert',
    ],
    'customer' => 'Kunde',
    'amount' => 'Beløp',
    'rental_period' => 'Leieperiode',
    'payment_method' => 'Betalingsmåte',
    'payment_status' => 'Betalingsstatus',
    'booking_information' => 'Bestillingsinformasjon',
    'booking_period' => 'Bestillingsperiode',
    'payment_status_label' => 'Betalingsstatus',
    'car' => 'Bil',
    'calendar_item_title' => ':car (:customer)',

    // Dates
    'start_date' => 'Startdato',
    'end_date' => 'Sluttdato',

    // Car search
    'search_cars' => 'Søk biler',
    'selected_car' => 'Valgt bil',
    'please_select_dates' => 'Vennligst velg både start- og sluttdato',
    'please_select_car' => 'Vennligst velg en bil for å fortsette med bestillingen',

    // Booking details
    'booking_details' => 'Bestillingsdetaljer',

    // Customer information
    'search_customer' => 'Søk kunde etter navn, e-post eller telefon...',
    'create_new_customer' => 'Opprett ny kunde',
    'customer_created_successfully' => 'Kunde opprettet',
    'customer_not_found' => 'Kunde ikke funnet',
    'customer_information' => 'Kundeinformasjon',
    'customer_name' => 'Navn',
    'email' => 'E-post',
    'phone' => 'Telefon',
    'customer_age' => 'Alder',
    'address' => 'Adresse',
    'city' => 'By',
    'state' => 'Fylke',
    'country' => 'Land',
    'zip' => 'Postnummer',
    'note' => 'Merknad',
    'note_placeholder' => 'Skriv inn spesielle ønsker eller merknader',

    // Services
    'services' => 'Tilleggstjenester',
    'day' => 'dag',

    // Payment
    'payment_status' => 'Betalingsstatus',
    'transaction_id' => 'Transaksjons-ID',
    'transaction_id_helper' => 'Du kan la dette feltet stå tomt hvis betalingsmåten er kontant eller bankoverføring',
    'payment_method_helper' => 'Velg betalingsmåten som brukes for denne bestillingen',
    'payment_status_helper' => 'Nåværende status for betalingen',

    // Form placeholders
    'first_name_placeholder' => 'Skriv inn fornavn',
    'last_name_placeholder' => 'Skriv inn etternavn',
    'email_placeholder' => 'Skriv inn e-postadresse',
    'phone_placeholder' => 'Skriv inn telefonnummer',
    'address_placeholder' => 'Skriv inn adresse',
    'city_placeholder' => 'Skriv inn by',
    'state_placeholder' => 'Skriv inn fylke',
    'country_placeholder' => 'Skriv inn land',
    'zip_placeholder' => 'Skriv inn postnummer',

    // Misc
    'no_customers_found' => 'Ingen kunder funnet',
    'no_cars_available' => 'Ingen biler tilgjengelig for de valgte datoene',
    'select_car' => 'Velg bil',
    'print_booking_info' => 'Skriv ut bestillingsinformasjon',
    'printed_on' => 'Skrevet ut',
    'computer_generated_document' => 'Dette er et datamaskingenerert dokument og krever ikke signatur.',
    'booking_summary' => 'Bestillingsoversikt',
    'booking_details' => 'Bestillingsdetaljer',
    'additional_services' => 'Tilleggstjenester',
    'rental_period' => 'Leieperiode',
    'to' => 'til',

    // Completion details
    'completion_details' => 'Fullføringsdetaljer',
    'add_completion_details' => 'Legg til fullføringsdetaljer',
    'edit_completion_details' => 'Rediger fullføringsdetaljer',
    'no_completion_details' => 'Ingen fullføringsdetaljer har blitt lagt til ennå.',
    'completion_details_updated_successfully' => 'Fullføringsdetaljer oppdatert.',

    'completion_miles' => 'Endelig kjørelengde',
    'completion_kilometers' => 'Endelige kilometer',
    'miles' => 'miles',
    'kilometers' => 'kilometer',
    'enter_miles' => 'Skriv inn endelig kjørelengde',
    'enter_kilometers' => 'Skriv inn endelige kilometer',
    'completion_miles_help' => 'Skriv inn den endelige kjørelengden når bilen ble returnert.',
    'completion_kilometers_help' => 'Skriv inn den endelige kilometeravlesningen når bilen ble returnert.',

    'completion_gas_level' => 'Drivstoffnivå',
    'select_gas_level' => 'Velg drivstoffnivå',
    'gas_empty' => 'Tom',
    'gas_quarter' => '1/4 tank',
    'gas_half' => '1/2 tank',
    'gas_three_quarters' => '3/4 tank',
    'gas_full' => 'Full tank',
    'completion_gas_level_help' => 'Velg drivstoffnivået når bilen ble returnert.',

    'damage_images' => 'Skadebilder',
    'damage_image' => 'Skadebilde',
    'damage_images_help' => 'Last opp bilder av eventuelle skader funnet på kjøretøyet (maks 5 MB per bilde).',
    'existing_images' => 'Eksisterende bilder',

    'completion_notes' => 'Fullføringsmerknader',
    'completion_notes_placeholder' => 'Skriv inn eventuelle merknader om kjøretøytilstand, skader eller andre observasjoner...',
    'completion_notes_help' => 'Legg til eventuelle tilleggsmerknader om kjøretøytilstand eller fullføring av leie.',

    'completed_at' => 'Fullført',

    // Coupon fields
    'coupon_code' => 'Kupongkode',
    'coupon_amount' => 'Kupongrabattbeløp',
    'enter_coupon_code' => 'Skriv inn kupongkode',
    'coupon_discount_amount' => 'Rabattbeløp',
    'applied_coupon' => 'Brukt kupong',

    // Validation messages
    'validation' => [
        'completion_miles_integer' => 'Kjørelengden må være et gyldig tall.',
        'completion_miles_min' => 'Kjørelengden må være minst 0.',
        'completion_gas_level_invalid' => 'Vennligst velg et gyldig drivstoffnivå.',
        'damage_image_invalid' => 'Den opplastede filen må være et gyldig bilde.',
        'damage_image_max_size' => 'Bildestørrelsen må ikke overstige 5 MB.',
        'completion_notes_max' => 'Merknadene må ikke overstige 10 000 tegn.',
    ],

    // Vendor booking actions
    'approve_booking' => 'Godkjenn bestilling',
    'cancel_booking' => 'Avbryt bestilling',
    'processing' => 'Behandler...',
    'pending_approval_notice' => 'Venter på godkjenning',
    'pending_approval_description' => 'Denne bestillingen venter på din godkjenning. Vennligst gjennomgå detaljene og godkjenn eller avbryt bestillingen.',
    'approve_booking_confirmation' => 'Er du sikker på at du vil godkjenne denne bestillingen? Kunden vil bli varslet.',
    'cancel_booking_confirmation' => 'Er du sikker på at du vil avbryte denne bestillingen? Denne handlingen kan ikke angres.',
    'booking_approved_successfully' => 'Bestillingen er godkjent.',
    'booking_cancelled_successfully' => 'Bestillingen er avbrutt.',
    'cannot_approve_booking' => 'Denne bestillingen kan ikke godkjennes. Bare ventende bestillinger kan godkjennes.',
    'cannot_cancel_booking' => 'Denne bestillingen kan ikke avbrytes. Fullførte eller allerede avbrutte bestillinger kan ikke avbrytes.',
];

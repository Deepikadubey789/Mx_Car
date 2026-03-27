<?php

return [
    'name' => 'Bokningar',
    'create' => 'Ny bokning',
    'reports' => 'Bokningsrapporter',
    'calendar' => 'Bokningskalender',
    'statuses' => [
        'pending' => 'Väntar',
        'processing' => 'Bearbetar',
        'completed' => 'Slutförd',
        'cancelled' => 'Avbruten',
    ],
    'customer' => 'Kund',
    'amount' => 'Belopp',
    'rental_period' => 'Hyresperiod',
    'payment_method' => 'Betalningsmetod',
    'payment_status' => 'Betalningsstatus',
    'booking_information' => 'Bokningsinformation',
    'booking_period' => 'Bokningsperiod',
    'payment_status_label' => 'Betalningsstatus',
    'car' => 'Bil',
    'calendar_item_title' => ':car (:customer)',

    // Dates
    'start_date' => 'Startdatum',
    'end_date' => 'Slutdatum',

    // Car search
    'search_cars' => 'Sök bilar',
    'selected_car' => 'Vald bil',
    'please_select_dates' => 'Vänligen välj både start- och slutdatum',
    'please_select_car' => 'Vänligen välj en bil för att fortsätta med bokningen',

    // Booking details
    'booking_details' => 'Bokningsdetaljer',

    // Customer information
    'search_customer' => 'Sök kund efter namn, e-post eller telefon...',
    'create_new_customer' => 'Skapa ny kund',
    'customer_created_successfully' => 'Kund skapad',
    'customer_not_found' => 'Kund inte hittade',
    'customer_information' => 'Kundinformation',
    'customer_name' => 'Namn',
    'email' => 'E-post',
    'phone' => 'Telefon',
    'customer_age' => 'Ålder',
    'address' => 'Adress',
    'city' => 'Stad',
    'state' => 'Län',
    'country' => 'Land',
    'zip' => 'Postnummer',
    'note' => 'Anteckning',
    'note_placeholder' => 'Ange speciella önskemål eller anteckningar',

    // Services
    'services' => 'Tilläggstjänster',
    'day' => 'dag',

    // Payment
    'payment_status' => 'Betalningsstatus',
    'transaction_id' => 'Transaktions-ID',
    'transaction_id_helper' => 'Du kan lämna detta fält tomt om betalningsmetoden är kontant eller banköverföring',
    'payment_method_helper' => 'Välj betalningsmetoden som används för denna bokningen',
    'payment_status_helper' => 'Nuvarande status för betalningen',

    // Form placeholders
    'first_name_placeholder' => 'Ange förnamn',
    'last_name_placeholder' => 'Ange efternamn',
    'email_placeholder' => 'Ange e-postadress',
    'phone_placeholder' => 'Ange telefonnummer',
    'address_placeholder' => 'Ange adress',
    'city_placeholder' => 'Ange stad',
    'state_placeholder' => 'Ange län',
    'country_placeholder' => 'Ange land',
    'zip_placeholder' => 'Ange postnummer',

    // Misc
    'no_customers_found' => 'Ingen kunder hittade',
    'no_cars_available' => 'Ingen bilar tillgänglig för de valda datumen',
    'select_car' => 'Välj bil',
    'print_booking_info' => 'Skriv ut bokningsinformation',
    'printed_on' => 'Skrivet ut',
    'computer_generated_document' => 'Detta är ett datagenererat dokument och kräver inte signatur.',
    'booking_summary' => 'Bokningsöversikt',
    'booking_details' => 'Bokningsdetaljer',
    'additional_services' => 'Tilläggstjänster',
    'rental_period' => 'Hyresperiod',
    'to' => 'till',

    // Completion details
    'completion_details' => 'Slutföringsdetaljer',
    'add_completion_details' => 'Lägg till slutföringsdetaljer',
    'edit_completion_details' => 'Redigera slutföringsdetaljer',
    'no_completion_details' => 'Ingen slutföringsdetaljer har lagts till ännu.',
    'completion_details_updated_successfully' => 'Slutföringsdetaljer uppdaterad.',

    'completion_miles' => 'Slutlig körsträcka',
    'completion_kilometers' => 'Slutliga kilometer',
    'miles' => 'miles',
    'kilometers' => 'kilometer',
    'enter_miles' => 'Ange slutlig körsträcka',
    'enter_kilometers' => 'Ange slutliga kilometer',
    'completion_miles_help' => 'Ange den slutliga körsträckan när bilen returnerades.',
    'completion_kilometers_help' => 'Ange den slutliga kilometeravläsningen när bilen returnerades.',

    'completion_gas_level' => 'Bränslenivå',
    'select_gas_level' => 'Välj bränslenivå',
    'gas_empty' => 'Tom',
    'gas_quarter' => '1/4 tank',
    'gas_half' => '1/2 tank',
    'gas_three_quarters' => '3/4 tank',
    'gas_full' => 'Full tank',
    'completion_gas_level_help' => 'Välj bränslenivån när bilen returnerades.',

    'damage_images' => 'Skadebilder',
    'damage_image' => 'Skadebild',
    'damage_images_help' => 'Ladda upp bilder av eventuella skader hittade på fordonet (max 5 MB per bild).',
    'existing_images' => 'Befintliga bilder',

    'completion_notes' => 'Slutföringsanteckningar',
    'completion_notes_placeholder' => 'Ange eventuella anteckningar om fordonstillstånd, skader eller andra observationer...',
    'completion_notes_help' => 'Lägg till eventuella tilläggsanteckningar om fordonstillstånd eller slutföring av hyra.',

    'completed_at' => 'Slutförd',

    // Coupon fields
    'coupon_code' => 'Kupongkod',
    'coupon_amount' => 'Kupongrabattbelopp',
    'enter_coupon_code' => 'Ange kupongkod',
    'coupon_discount_amount' => 'Rabattbelopp',
    'applied_coupon' => 'Använd kupong',

    // Validation messages
    'validation' => [
        'completion_miles_integer' => 'Körsträckan måste vara ett giltigt tal.',
        'completion_miles_min' => 'Körsträckan måste vara minst 0.',
        'completion_gas_level_invalid' => 'Vänligen välj en giltig bränslenivå.',
        'damage_image_invalid' => 'Den uppladdade filen måste vara en giltig bild.',
        'damage_image_max_size' => 'Bildstorleken måste inte överskrida 5 MB.',
        'completion_notes_max' => 'Anteckningarna måste inte överskrida 10 000 tecken.',
    ],

    // Vendor booking actions
    'approve_booking' => 'Godkänn bokning',
    'cancel_booking' => 'Avboka bokning',
    'processing' => 'Bearbetar...',
    'pending_approval_notice' => 'Väntar på godkännande',
    'pending_approval_description' => 'Denna bokning väntar på ditt godkännande. Granska detaljerna och godkänn eller avboka bokningen.',
    'approve_booking_confirmation' => 'Är du säker på att du vill godkänna denna bokning? Kunden kommer att meddelas.',
    'cancel_booking_confirmation' => 'Är du säker på att du vill avboka denna bokning? Denna åtgärd kan inte ångras.',
    'booking_approved_successfully' => 'Bokningen har godkänts.',
    'booking_cancelled_successfully' => 'Bokningen har avbokats.',
    'cannot_approve_booking' => 'Denna bokning kan inte godkännas. Endast väntande bokningar kan godkännas.',
    'cannot_cancel_booking' => 'Denna bokning kan inte avbokas. Slutförda eller redan avbokade bokningar kan inte avbokas.',
];

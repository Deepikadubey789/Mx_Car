<?php

return [
    'name' => 'Boekingen',
    'create' => 'Nieuwe boeking',
    'reports' => 'Boekingsrapporten',
    'calendar' => 'Boekingskalender',
    'statuses' => [
        'pending' => 'In behandeling',
        'processing' => 'Verwerken',
        'completed' => 'Voltooid',
        'cancelled' => 'Geannuleerd',
    ],
    'customer' => 'Klant',
    'amount' => 'Bedrag',
    'rental_period' => 'Huurperiode',
    'payment_method' => 'Betaalmethode',
    'payment_status' => 'Betalingsstatus',
    'booking_information' => 'Boekingsinformatie',
    'booking_period' => 'Boekingsperiode',
    'payment_status_label' => 'Betalingsstatus',
    'car' => 'Auto',
    'calendar_item_title' => ':car (:customer)',

    // Dates
    'start_date' => 'Startdatum',
    'end_date' => 'Einddatum',

    // Car search
    'search_cars' => 'Auto\'s zoeken',
    'selected_car' => 'Geselecteerde auto',
    'please_select_dates' => 'Selecteer zowel start- als einddatum',
    'please_select_car' => 'Selecteer een auto om door te gaan met de boeking',

    // Booking details
    'booking_details' => 'Boekingsdetails',

    // Customer information
    'search_customer' => 'Zoek klant op naam, e-mail of telefoon...',
    'create_new_customer' => 'Nieuwe klant aanmaken',
    'customer_created_successfully' => 'Klant succesvol aangemaakt',
    'customer_not_found' => 'Klant niet gevonden',
    'customer_information' => 'Klantinformatie',
    'customer_name' => 'Naam',
    'email' => 'E-mail',
    'phone' => 'Telefoon',
    'customer_age' => 'Leeftijd',
    'address' => 'Adres',
    'city' => 'Stad',
    'state' => 'Provincie',
    'country' => 'Land',
    'zip' => 'Postcode',
    'note' => 'Opmerking',
    'note_placeholder' => 'Voer speciale verzoeken of opmerkingen in',

    // Services
    'services' => 'Aanvullende diensten',
    'day' => 'dag',

    // Payment
    'payment_status' => 'Betalingsstatus',
    'transaction_id' => 'Transactie-ID',
    'transaction_id_helper' => 'U kunt dit veld leeg laten als de betaalmethode Rembours of Bankoverschrijving is',
    'payment_method_helper' => 'Selecteer de betaalmethode die voor deze boeking wordt gebruikt',
    'payment_status_helper' => 'Huidige status van de betaling',

    // Form placeholders
    'first_name_placeholder' => 'Voer voornaam in',
    'last_name_placeholder' => 'Voer achternaam in',
    'email_placeholder' => 'Voer e-mailadres in',
    'phone_placeholder' => 'Voer telefoonnummer in',
    'address_placeholder' => 'Voer adres in',
    'city_placeholder' => 'Voer stad in',
    'state_placeholder' => 'Voer provincie in',
    'country_placeholder' => 'Voer land in',
    'zip_placeholder' => 'Voer postcode in',

    // Misc
    'no_customers_found' => 'Geen klanten gevonden',
    'no_cars_available' => 'Geen auto\'s beschikbaar voor de geselecteerde data',
    'select_car' => 'Selecteer Auto',
    'print_booking_info' => 'Boekingsinformatie afdrukken',
    'printed_on' => 'Afgedrukt op',
    'computer_generated_document' => 'Dit is een computergegenereerd document en vereist geen handtekening.',
    'booking_summary' => 'Boekingsoverzicht',
    'booking_details' => 'Boekingsdetails',
    'additional_services' => 'Aanvullende diensten',
    'rental_period' => 'Huurperiode',
    'to' => 'tot',

    // Completion details
    'completion_details' => 'Voltooiingsdetails',
    'add_completion_details' => 'Voltooiingsdetails toevoegen',
    'edit_completion_details' => 'Voltooiingsdetails bewerken',
    'no_completion_details' => 'Er zijn nog geen voltooiingsdetails toegevoegd.',
    'completion_details_updated_successfully' => 'Voltooiingsdetails succesvol bijgewerkt.',

    'completion_miles' => 'Eindkilometerstand',
    'completion_kilometers' => 'Eindkilometers',
    'miles' => 'mijlen',
    'kilometers' => 'kilometers',
    'enter_miles' => 'Voer eindkilometerstand in',
    'enter_kilometers' => 'Voer eindkilometers in',
    'completion_miles_help' => 'Voer de eindkilometerstand in toen de auto werd teruggebracht.',
    'completion_kilometers_help' => 'Voer de eindkilometerstand in toen de auto werd teruggebracht.',

    'completion_gas_level' => 'Brandstofniveau',
    'select_gas_level' => 'Selecteer brandstofniveau',
    'gas_empty' => 'Leeg',
    'gas_quarter' => '1/4 Tank',
    'gas_half' => '1/2 Tank',
    'gas_three_quarters' => '3/4 Tank',
    'gas_full' => 'Volle Tank',
    'completion_gas_level_help' => 'Selecteer het brandstofniveau toen de auto werd teruggebracht.',

    'damage_images' => 'Schadebeelden',
    'damage_image' => 'Schadebeeld',
    'damage_images_help' => 'Upload afbeeldingen van eventuele schade aan het voertuig (max 5MB per afbeelding).',
    'existing_images' => 'Bestaande afbeeldingen',

    'completion_notes' => 'Voltooiingsopmerkingen',
    'completion_notes_placeholder' => 'Voer opmerkingen in over de staat van het voertuig, schade of andere observaties...',
    'completion_notes_help' => 'Voeg eventuele aanvullende opmerkingen toe over de staat van het voertuig of de voltooiing van de huur.',

    'completed_at' => 'Voltooid op',

    // Coupon fields
    'coupon_code' => 'Couponcode',
    'coupon_amount' => 'Couponkortingsbedrag',
    'enter_coupon_code' => 'Voer couponcode in',
    'coupon_discount_amount' => 'Kortingsbedrag',
    'applied_coupon' => 'Toegepaste coupon',

    // Validation messages
    'validation' => [
        'completion_miles_integer' => 'De kilometerstand moet een geldig getal zijn.',
        'completion_miles_min' => 'De kilometerstand moet minimaal 0 zijn.',
        'completion_gas_level_invalid' => 'Selecteer een geldig brandstofniveau.',
        'damage_image_invalid' => 'Het geüploade bestand moet een geldige afbeelding zijn.',
        'damage_image_max_size' => 'De afbeeldingsgrootte mag niet meer dan 5MB zijn.',
        'completion_notes_max' => 'De opmerkingen mogen niet meer dan 10.000 tekens bevatten.',
    ],

    // Vendor booking actions
    'approve_booking' => 'Boeking goedkeuren',
    'cancel_booking' => 'Boeking annuleren',
    'processing' => 'Verwerken...',
    'pending_approval_notice' => 'In afwachting van goedkeuring',
    'pending_approval_description' => 'Deze boeking wacht op uw goedkeuring. Controleer de details en keur de boeking goed of annuleer deze.',
    'approve_booking_confirmation' => 'Weet u zeker dat u deze boeking wilt goedkeuren? De klant wordt op de hoogte gebracht.',
    'cancel_booking_confirmation' => 'Weet u zeker dat u deze boeking wilt annuleren? Deze actie kan niet ongedaan worden gemaakt.',
    'booking_approved_successfully' => 'De boeking is succesvol goedgekeurd.',
    'booking_cancelled_successfully' => 'De boeking is succesvol geannuleerd.',
    'cannot_approve_booking' => 'Deze boeking kan niet worden goedgekeurd. Alleen boekingen in behandeling kunnen worden goedgekeurd.',
    'cannot_cancel_booking' => 'Deze boeking kan niet worden geannuleerd. Voltooide of reeds geannuleerde boekingen kunnen niet worden geannuleerd.',
];

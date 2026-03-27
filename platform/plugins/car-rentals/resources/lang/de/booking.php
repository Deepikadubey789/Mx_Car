<?php

return [
    'name' => 'Buchungen',
    'create' => 'Neue Buchung',
    'reports' => 'Buchungsberichte',
    'calendar' => 'Buchungskalender',
    'statuses' => [
        'pending' => 'Ausstehend',
        'processing' => 'In Bearbeitung',
        'completed' => 'Abgeschlossen',
        'cancelled' => 'Storniert',
    ],
    'customer' => 'Kunde',
    'amount' => 'Betrag',
    'rental_period' => 'Mietzeitraum',
    'payment_method' => 'Zahlungsmethode',
    'payment_status' => 'Zahlungsstatus',
    'booking_information' => 'Buchungsinformationen',
    'booking_period' => 'Buchungszeitraum',
    'payment_status_label' => 'Zahlungsstatus',
    'car' => 'Fahrzeug',
    'calendar_item_title' => ':car (:customer)',

    // Dates
    'start_date' => 'Startdatum',
    'end_date' => 'Enddatum',

    // Car search
    'search_cars' => 'Fahrzeuge suchen',
    'selected_car' => 'Ausgewähltes Fahrzeug',
    'please_select_dates' => 'Bitte wählen Sie sowohl Start- als auch Enddatum',
    'please_select_car' => 'Bitte wählen Sie ein Fahrzeug aus, um mit der Buchung fortzufahren',

    // Booking details
    'booking_details' => 'Buchungsdetails',

    // Customer information
    'search_customer' => 'Kunde nach Name, E-Mail oder Telefon suchen...',
    'create_new_customer' => 'Neuen Kunden erstellen',
    'customer_created_successfully' => 'Kunde erfolgreich erstellt',
    'customer_not_found' => 'Kunde nicht gefunden',
    'customer_information' => 'Kundeninformationen',
    'customer_name' => 'Name',
    'email' => 'E-Mail',
    'phone' => 'Telefon',
    'customer_age' => 'Alter',
    'address' => 'Adresse',
    'city' => 'Stadt',
    'state' => 'Bundesland/Provinz',
    'country' => 'Land',
    'zip' => 'PLZ/Postleitzahl',
    'note' => 'Notiz',
    'note_placeholder' => 'Geben Sie besondere Wünsche oder Notizen ein',

    // Services
    'services' => 'Zusätzliche Dienstleistungen',
    'day' => 'Tag',

    // Payment
    'payment_status' => 'Zahlungsstatus',
    'transaction_id' => 'Transaktions-ID',
    'transaction_id_helper' => 'Sie können dieses Feld leer lassen, wenn die Zahlungsmethode Nachnahme oder Banküberweisung ist',
    'payment_method_helper' => 'Wählen Sie die für diese Buchung verwendete Zahlungsmethode',
    'payment_status_helper' => 'Aktueller Status der Zahlung',

    // Form placeholders
    'first_name_placeholder' => 'Vorname eingeben',
    'last_name_placeholder' => 'Nachname eingeben',
    'email_placeholder' => 'E-Mail-Adresse eingeben',
    'phone_placeholder' => 'Telefonnummer eingeben',
    'address_placeholder' => 'Adresse eingeben',
    'city_placeholder' => 'Stadt eingeben',
    'state_placeholder' => 'Bundesland/Provinz eingeben',
    'country_placeholder' => 'Land eingeben',
    'zip_placeholder' => 'PLZ/Postleitzahl eingeben',

    // Misc
    'no_customers_found' => 'Keine Kunden gefunden',
    'no_cars_available' => 'Keine Fahrzeuge für die ausgewählten Daten verfügbar',
    'select_car' => 'Fahrzeug auswählen',
    'print_booking_info' => 'Buchungsinformationen drucken',
    'printed_on' => 'Gedruckt am',
    'computer_generated_document' => 'Dies ist ein computergestütztes Dokument und erfordert keine Unterschrift.',
    'booking_summary' => 'Buchungszusammenfassung',
    'booking_details' => 'Buchungsdetails',
    'additional_services' => 'Zusätzliche Dienstleistungen',
    'rental_period' => 'Mietzeitraum',
    'to' => 'bis',

    // Completion details
    'completion_details' => 'Abschlussdetails',
    'add_completion_details' => 'Abschlussdetails hinzufügen',
    'edit_completion_details' => 'Abschlussdetails bearbeiten',
    'no_completion_details' => 'Noch keine Abschlussdetails hinzugefügt.',
    'completion_details_updated_successfully' => 'Abschlussdetails erfolgreich aktualisiert.',

    'completion_miles' => 'Endkilometerstand',
    'completion_kilometers' => 'Endkilometer',
    'miles' => 'Meilen',
    'kilometers' => 'Kilometer',
    'enter_miles' => 'Endkilometerstand eingeben',
    'enter_kilometers' => 'Endkilometer eingeben',
    'completion_miles_help' => 'Geben Sie den Endkilometerstand ein, als das Fahrzeug zurückgegeben wurde.',
    'completion_kilometers_help' => 'Geben Sie den Endkilometerstand ein, als das Fahrzeug zurückgegeben wurde.',

    'completion_gas_level' => 'Tankfüllstand',
    'select_gas_level' => 'Tankfüllstand auswählen',
    'gas_empty' => 'Leer',
    'gas_quarter' => '1/4 Tank',
    'gas_half' => '1/2 Tank',
    'gas_three_quarters' => '3/4 Tank',
    'gas_full' => 'Voller Tank',
    'completion_gas_level_help' => 'Wählen Sie den Tankfüllstand, als das Fahrzeug zurückgegeben wurde.',

    'damage_images' => 'Schadensbilder',
    'damage_image' => 'Schadensbild',
    'damage_images_help' => 'Laden Sie Bilder von Schäden am Fahrzeug hoch (max. 5 MB pro Bild).',
    'existing_images' => 'Vorhandene Bilder',

    'completion_notes' => 'Abschlussnotizen',
    'completion_notes_placeholder' => 'Geben Sie Notizen zum Fahrzeugzustand, Schäden oder andere Beobachtungen ein...',
    'completion_notes_help' => 'Fügen Sie zusätzliche Notizen zum Fahrzeugzustand oder Mietabschluss hinzu.',

    'completed_at' => 'Abgeschlossen am',

    // Coupon fields
    'coupon_code' => 'Gutscheincode',
    'coupon_amount' => 'Gutschein-Rabattbetrag',
    'enter_coupon_code' => 'Gutscheincode eingeben',
    'coupon_discount_amount' => 'Rabattbetrag',
    'applied_coupon' => 'Angewendeter Gutschein',

    // Validation messages
    'validation' => [
        'completion_miles_integer' => 'Der Kilometerstand muss eine gültige Zahl sein.',
        'completion_miles_min' => 'Der Kilometerstand muss mindestens 0 sein.',
        'completion_gas_level_invalid' => 'Bitte wählen Sie einen gültigen Tankfüllstand.',
        'damage_image_invalid' => 'Die hochgeladene Datei muss ein gültiges Bild sein.',
        'damage_image_max_size' => 'Die Bildgröße darf 5 MB nicht überschreiten.',
        'completion_notes_max' => 'Die Notizen dürfen 10.000 Zeichen nicht überschreiten.',
    ],

    // Vendor booking actions
    'approve_booking' => 'Buchung genehmigen',
    'cancel_booking' => 'Buchung stornieren',
    'processing' => 'Wird verarbeitet...',
    'pending_approval_notice' => 'Genehmigung ausstehend',
    'pending_approval_description' => 'Diese Buchung wartet auf Ihre Genehmigung. Bitte überprüfen Sie die Details und genehmigen oder stornieren Sie die Buchung.',
    'approve_booking_confirmation' => 'Sind Sie sicher, dass Sie diese Buchung genehmigen möchten? Der Kunde wird benachrichtigt.',
    'cancel_booking_confirmation' => 'Sind Sie sicher, dass Sie diese Buchung stornieren möchten? Diese Aktion kann nicht rückgängig gemacht werden.',
    'booking_approved_successfully' => 'Die Buchung wurde erfolgreich genehmigt.',
    'booking_cancelled_successfully' => 'Die Buchung wurde erfolgreich storniert.',
    'cannot_approve_booking' => 'Diese Buchung kann nicht genehmigt werden. Nur ausstehende Buchungen können genehmigt werden.',
    'cannot_cancel_booking' => 'Diese Buchung kann nicht storniert werden. Abgeschlossene oder bereits stornierte Buchungen können nicht storniert werden.',
];

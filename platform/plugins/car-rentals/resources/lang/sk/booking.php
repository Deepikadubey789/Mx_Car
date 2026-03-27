<?php

return [
    'name' => 'Rezervácie',
    'create' => 'Nová rezervácie',
    'reports' => 'Přehledy rezervací',
    'calendar' => 'Kalendár rezervací',
    'statuses' => [
        'pending' => 'Čeká na vyřízení',
        'processing' => 'Spracováva sa',
        'completed' => 'Dokončené',
        'cancelled' => 'Zrušené',
    ],
    'customer' => 'Zákazník',
    'amount' => 'Suma',
    'rental_period' => 'Doba prenájmu',
    'payment_method' => 'Platební metoda',
    'payment_status' => 'Stav platby',
    'booking_information' => 'Informácie o rezervaci',
    'booking_period' => 'Období rezervácie',
    'payment_status_label' => 'Stav platby',
    'car' => 'Vozidlo',
    'calendar_item_title' => ':car (:customer)',

    // Dates
    'start_date' => 'Datum zahájení',
    'end_date' => 'Datum ukončení',

    // Car search
    'search_cars' => 'Vyhľadať vozidla',
    'selected_car' => 'Vybrané vozidlo',
    'please_select_dates' => 'Prosím vyberte datum zahájení i ukončení',
    'please_select_car' => 'Prosím vyberte vozidlo pro pokračování v rezervaci',

    // Booking details
    'booking_details' => 'Podrobnosti rezervácie',

    // Customer information
    'search_customer' => 'Vyhľadať zákazníka podle jména, e-mailu nebo telefónu...',
    'create_new_customer' => 'Vytvoriť nového zákazníka',
    'customer_created_successfully' => 'Zákazník byl úspěšně vytvořen',
    'customer_not_found' => 'Zákazník nebyl nalezen',
    'customer_information' => 'Informácie o zákazníkovi',
    'customer_name' => 'Meno',
    'email' => 'Email',
    'phone' => 'Telefón',
    'customer_age' => 'Vek',
    'address' => 'Adresa',
    'city' => 'Mesto',
    'state' => 'Štát/Kraj',
    'country' => 'Krajina',
    'zip' => 'PSČ',
    'note' => 'Poznámka',
    'note_placeholder' => 'Zadejte jakékoli speciální požadavky nebo poznámky',

    // Services
    'services' => 'Doplňkové služby',
    'day' => 'deň',

    // Payment
    'payment_status' => 'Stav platby',
    'transaction_id' => 'ID transakcia',
    'transaction_id_helper' => 'Toto pole můžete nechat prázdné, pokud je platební metodou dobírka nebo bankovní převod',
    'payment_method_helper' => 'Vyberte platební metodu použitou pro tuto rezervaci',
    'payment_status_helper' => 'Aktuální stav platby',

    // Form placeholders
    'first_name_placeholder' => 'Zadejte křestní meno',
    'last_name_placeholder' => 'Zadejte příjmení',
    'email_placeholder' => 'Zadejte e-mailovou adresu',
    'phone_placeholder' => 'Zadejte telefónní číslo',
    'address_placeholder' => 'Zadejte adresu',
    'city_placeholder' => 'Zadejte mesto',
    'state_placeholder' => 'Zadejte štát/kraj',
    'country_placeholder' => 'Zadejte zemi',
    'zip_placeholder' => 'Zadejte PSČ',

    // Misc
    'no_customers_found' => 'Neboli nájdení žádní zákazníci',
    'no_cars_available' => 'Pro vybrané datum nejsou k dispozici žádná vozidla',
    'select_car' => 'Vybrat vozidlo',
    'print_booking_info' => 'Vytlačiť informácie o rezervaci',
    'printed_on' => 'Vytištěno dne',
    'computer_generated_document' => 'Tento dokument je generován počítačem a nevyžaduje podpis.',
    'booking_summary' => 'Súhrn rezervácie',
    'booking_details' => 'Podrobnosti rezervácie',
    'additional_services' => 'Doplňkové služby',
    'rental_period' => 'Doba prenájmu',
    'to' => 'do',

    // Completion details
    'completion_details' => 'Údaje o dokončení',
    'add_completion_details' => 'Přidat údaje o dokončení',
    'edit_completion_details' => 'Upravit údaje o dokončení',
    'no_completion_details' => 'Zatím nebyly přidány žádné údaje o dokončení.',
    'completion_details_updated_successfully' => 'Údaje o dokončení byly úspěšně aktualizovány.',

    'completion_miles' => 'Konečný nájezd',
    'completion_kilometers' => 'Konečné kilometry',
    'miles' => 'míle',
    'kilometers' => 'kilometry',
    'enter_miles' => 'Zadejte konečný nájezd',
    'enter_kilometers' => 'Zadejte konečné kilometry',
    'completion_miles_help' => 'Zadejte konečný stav tachometru při vrácení vozidla.',
    'completion_kilometers_help' => 'Zadejte konečný stav kilometrů při vrácení vozidla.',

    'completion_gas_level' => 'Úroveň paliva',
    'select_gas_level' => 'Vyberte úroveň paliva',
    'gas_empty' => 'Prázdná',
    'gas_quarter' => '1/4 nádrže',
    'gas_half' => '1/2 nádrže',
    'gas_three_quarters' => '3/4 nádrže',
    'gas_full' => 'Plná nádrž',
    'completion_gas_level_help' => 'Vyberte úroveň paliva při vrácení vozidla.',

    'damage_images' => 'Fotografie poškození',
    'damage_image' => 'Fotografie poškození',
    'damage_images_help' => 'Nahrajte fotografie jakéhokoli poškození nalezeného na vozidle (max. 5 MB na obrázek).',
    'existing_images' => 'Stávající obrázky',

    'completion_notes' => 'Poznámky k dokončení',
    'completion_notes_placeholder' => 'Zadejte jakékoli poznámky o stavu vozidla, poškozeních nebo dalších pozorováních...',
    'completion_notes_help' => 'Přidejte jakékoli další poznámky o stavu vozidla nebo dokončení prenájmu.',

    'completed_at' => 'Dokončené v',

    // Coupon fields
    'coupon_code' => 'Kód kupónu',
    'coupon_amount' => 'Suma slevy kupónu',
    'enter_coupon_code' => 'Zadejte kód kupónu',
    'coupon_discount_amount' => 'Suma slevy',
    'applied_coupon' => 'Použitý kupón',

    // Validation messages
    'validation' => [
        'completion_miles_integer' => 'Nájezd musí být platné číslo.',
        'completion_miles_min' => 'Nájezd musí být alespoň 0.',
        'completion_gas_level_invalid' => 'Prosím vyberte platnou úroveň paliva.',
        'damage_image_invalid' => 'Nahraný soubor musí být platný obrázek.',
        'damage_image_max_size' => 'Velikost obrázku nesmí překročit 5 MB.',
        'completion_notes_max' => 'Poznámky nesmí překročit 10 000 znaků.',
    ],

    // Vendor booking actions
    'approve_booking' => 'Schváliť rezerváciu',
    'cancel_booking' => 'Zrušiť rezerváciu',
    'processing' => 'Spracúva sa...',
    'pending_approval_notice' => 'Čaká na schválenie',
    'pending_approval_description' => 'Táto rezervácia čaká na vaše schválenie. Skontrolujte prosím podrobnosti a schváľte alebo zrušte rezerváciu.',
    'approve_booking_confirmation' => 'Ste si istí, že chcete schváliť túto rezerváciu? Zákazník bude upozornený.',
    'cancel_booking_confirmation' => 'Ste si istí, že chcete zrušiť túto rezerváciu? Túto akciu nemožno vrátiť späť.',
    'booking_approved_successfully' => 'Rezervácia bola úspešne schválená.',
    'booking_cancelled_successfully' => 'Rezervácia bola úspešne zrušená.',
    'cannot_approve_booking' => 'Túto rezerváciu nemožno schváliť. Schváliť možno iba čakajúce rezervácie.',
    'cannot_cancel_booking' => 'Túto rezerváciu nemožno zrušiť. Dokončené alebo už zrušené rezervácie nemožno zrušiť.',
];

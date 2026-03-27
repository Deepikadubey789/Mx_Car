<?php

return [
    'name' => 'Rezervace',
    'create' => 'Nová rezervace',
    'reports' => 'Přehledy rezervací',
    'calendar' => 'Kalendář rezervací',
    'statuses' => [
        'pending' => 'Čeká na vyřízení',
        'processing' => 'Zpracovává se',
        'completed' => 'Dokončeno',
        'cancelled' => 'Zrušeno',
    ],
    'customer' => 'Zákazník',
    'amount' => 'Částka',
    'rental_period' => 'Doba pronájmu',
    'payment_method' => 'Platební metoda',
    'payment_status' => 'Stav platby',
    'booking_information' => 'Informace o rezervaci',
    'booking_period' => 'Období rezervace',
    'payment_status_label' => 'Stav platby',
    'car' => 'Vozidlo',
    'calendar_item_title' => ':car (:customer)',

    // Dates
    'start_date' => 'Datum zahájení',
    'end_date' => 'Datum ukončení',

    // Car search
    'search_cars' => 'Vyhledat vozidla',
    'selected_car' => 'Vybrané vozidlo',
    'please_select_dates' => 'Prosím vyberte datum zahájení i ukončení',
    'please_select_car' => 'Prosím vyberte vozidlo pro pokračování v rezervaci',

    // Booking details
    'booking_details' => 'Podrobnosti rezervace',

    // Customer information
    'search_customer' => 'Vyhledat zákazníka podle jména, e-mailu nebo telefonu...',
    'create_new_customer' => 'Vytvořit nového zákazníka',
    'customer_created_successfully' => 'Zákazník byl úspěšně vytvořen',
    'customer_not_found' => 'Zákazník nebyl nalezen',
    'customer_information' => 'Informace o zákazníkovi',
    'customer_name' => 'Jméno',
    'email' => 'Email',
    'phone' => 'Telefon',
    'customer_age' => 'Věk',
    'address' => 'Adresa',
    'city' => 'Město',
    'state' => 'Stát/Kraj',
    'country' => 'Země',
    'zip' => 'PSČ',
    'note' => 'Poznámka',
    'note_placeholder' => 'Zadejte jakékoli speciální požadavky nebo poznámky',

    // Services
    'services' => 'Doplňkové služby',
    'day' => 'den',

    // Payment
    'payment_status' => 'Stav platby',
    'transaction_id' => 'ID transakce',
    'transaction_id_helper' => 'Toto pole můžete nechat prázdné, pokud je platební metodou dobírka nebo bankovní převod',
    'payment_method_helper' => 'Vyberte platební metodu použitou pro tuto rezervaci',
    'payment_status_helper' => 'Aktuální stav platby',

    // Form placeholders
    'first_name_placeholder' => 'Zadejte křestní jméno',
    'last_name_placeholder' => 'Zadejte příjmení',
    'email_placeholder' => 'Zadejte e-mailovou adresu',
    'phone_placeholder' => 'Zadejte telefonní číslo',
    'address_placeholder' => 'Zadejte adresu',
    'city_placeholder' => 'Zadejte město',
    'state_placeholder' => 'Zadejte stát/kraj',
    'country_placeholder' => 'Zadejte zemi',
    'zip_placeholder' => 'Zadejte PSČ',

    // Misc
    'no_customers_found' => 'Nebyli nalezeni žádní zákazníci',
    'no_cars_available' => 'Pro vybrané datum nejsou k dispozici žádná vozidla',
    'select_car' => 'Vybrat vozidlo',
    'print_booking_info' => 'Vytisknout informace o rezervaci',
    'printed_on' => 'Vytištěno dne',
    'computer_generated_document' => 'Tento dokument je generován počítačem a nevyžaduje podpis.',
    'booking_summary' => 'Souhrn rezervace',
    'booking_details' => 'Podrobnosti rezervace',
    'additional_services' => 'Doplňkové služby',
    'rental_period' => 'Doba pronájmu',
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
    'completion_notes_help' => 'Přidejte jakékoli další poznámky o stavu vozidla nebo dokončení pronájmu.',

    'completed_at' => 'Dokončeno v',

    // Coupon fields
    'coupon_code' => 'Kód kupónu',
    'coupon_amount' => 'Částka slevy kupónu',
    'enter_coupon_code' => 'Zadejte kód kupónu',
    'coupon_discount_amount' => 'Částka slevy',
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
    'approve_booking' => 'Schválit rezervaci',
    'cancel_booking' => 'Zrušit rezervaci',
    'processing' => 'Zpracovává se...',
    'pending_approval_notice' => 'Čeká na schválení',
    'pending_approval_description' => 'Tato rezervace čeká na vaše schválení. Zkontrolujte prosím podrobnosti a schvalte nebo zrušte rezervaci.',
    'approve_booking_confirmation' => 'Jste si jisti, že chcete tuto rezervaci schválit? Zákazník bude upozorněn.',
    'cancel_booking_confirmation' => 'Jste si jisti, že chcete tuto rezervaci zrušit? Tuto akci nelze vrátit zpět.',
    'booking_approved_successfully' => 'Rezervace byla úspěšně schválena.',
    'booking_cancelled_successfully' => 'Rezervace byla úspěšně zrušena.',
    'cannot_approve_booking' => 'Tuto rezervaci nelze schválit. Schválit lze pouze čekající rezervace.',
    'cannot_cancel_booking' => 'Tuto rezervaci nelze zrušit. Dokončené nebo již zrušené rezervace nelze zrušit.',
];

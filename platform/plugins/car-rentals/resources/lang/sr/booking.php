<?php

return [
    'name' => 'Rezervacije',
    'create' => 'Nova rezervacija',
    'reports' => 'Izvještaji o rezervacijama',
    'calendar' => 'Kalendar rezervacija',
    'statuses' => [
        'pending' => 'Na čekanju',
        'processing' => 'U obradi',
        'completed' => 'Završeno',
        'cancelled' => 'Otkazano',
    ],
    'customer' => 'Kupac',
    'amount' => 'Iznos',
    'rental_period' => 'Razdoblje najma',
    'payment_method' => 'Način plaćanja',
    'payment_status' => 'Status plaćanja',
    'booking_information' => 'Informacije o rezervaciji',
    'booking_period' => 'Razdoblje rezervacije',
    'payment_status_label' => 'Status plaćanja',
    'car' => 'Vozilo',
    'calendar_item_title' => ':car (:customer)',

    // Dates
    'start_date' => 'Datum početka',
    'end_date' => 'Datum završetka',

    // Car search
    'search_cars' => 'Pretraži vozila',
    'selected_car' => 'Odabrano vozilo',
    'please_select_dates' => 'Molimo odaberite datum početka i završetka',
    'please_select_car' => 'Molimo odaberite vozilo za nastavak rezervacije',

    // Booking details
    'booking_details' => 'Detalji rezervacije',

    // Customer information
    'search_customer' => 'Pretraži kupca po imenu, e-pošti ili telefonu...',
    'create_new_customer' => 'Kreiraj novog kupca',
    'customer_created_successfully' => 'Kupac uspješno kreiran',
    'customer_not_found' => 'Kupac nije pronađen',
    'customer_information' => 'Informacije o kupcu',
    'customer_name' => 'Ime',
    'email' => 'E-pošta',
    'phone' => 'Telefon',
    'customer_age' => 'Dob',
    'address' => 'Adresa',
    'city' => 'Grad',
    'state' => 'Država/Pokrajina',
    'country' => 'Zemlja',
    'zip' => 'Poštanski broj',
    'note' => 'Napomena',
    'note_placeholder' => 'Unesite posebne zahtjeve ili napomene',

    // Services
    'services' => 'Dodatne usluge',
    'day' => 'dan',

    // Payment
    'payment_status' => 'Status plaćanja',
    'transaction_id' => 'ID transakcije',
    'transaction_id_helper' => 'Možete ostaviti ovo polje prazno ako je način plaćanja gotovina ili bankovni prijenos',
    'payment_method_helper' => 'Odaberite način plaćanja korišten za ovu rezervaciju',
    'payment_status_helper' => 'Trenutni status plaćanja',

    // Form placeholders
    'first_name_placeholder' => 'Unesite ime',
    'last_name_placeholder' => 'Unesite prezime',
    'email_placeholder' => 'Unesite adresu e-pošte',
    'phone_placeholder' => 'Unesite telefonski broj',
    'address_placeholder' => 'Unesite adresu',
    'city_placeholder' => 'Unesite grad',
    'state_placeholder' => 'Unesite državu/pokrajinu',
    'country_placeholder' => 'Unesite zemlju',
    'zip_placeholder' => 'Unesite poštanski broj',

    // Misc
    'no_customers_found' => 'Nisu pronađeni kupci',
    'no_cars_available' => 'Nema dostupnih vozila za odabrane datume',
    'select_car' => 'Odaberi vozilo',
    'print_booking_info' => 'Ispiši informacije o rezervaciji',
    'printed_on' => 'Ispisano',
    'computer_generated_document' => 'Ovo je računalno generirani dokument i ne zahtijeva potpis.',
    'booking_summary' => 'Sažetak rezervacije',
    'booking_details' => 'Detalji rezervacije',
    'additional_services' => 'Dodatne usluge',
    'rental_period' => 'Razdoblje najma',
    'to' => 'do',

    // Completion details
    'completion_details' => 'Detalji završetka',
    'add_completion_details' => 'Dodaj detalje završetka',
    'edit_completion_details' => 'Uredi detalje završetka',
    'no_completion_details' => 'Još nisu dodani detalji završetka.',
    'completion_details_updated_successfully' => 'Detalji završetka uspješno ažurirani.',

    'completion_miles' => 'Završna kilometraža',
    'completion_kilometers' => 'Završni kilometri',
    'miles' => 'milje',
    'kilometers' => 'kilometri',
    'enter_miles' => 'Unesite završnu kilometražu',
    'enter_kilometers' => 'Unesite završne kilometre',
    'completion_miles_help' => 'Unesite završno očitanje kilometraže kada je vozilo vraćeno.',
    'completion_kilometers_help' => 'Unesite završno očitanje kilometara kada je vozilo vraćeno.',

    'completion_gas_level' => 'Razina goriva',
    'select_gas_level' => 'Odaberite razinu goriva',
    'gas_empty' => 'Prazno',
    'gas_quarter' => '1/4 spremnika',
    'gas_half' => '1/2 spremnika',
    'gas_three_quarters' => '3/4 spremnika',
    'gas_full' => 'Pun spremnik',
    'completion_gas_level_help' => 'Odaberite razinu goriva kada je vozilo vraćeno.',

    'damage_images' => 'Slike oštećenja',
    'damage_image' => 'Slika oštećenja',
    'damage_images_help' => 'Prenesite slike bilo kakvih oštećenja pronađenih na vozilu (maksimalno 5MB po slici).',
    'existing_images' => 'Postojeće slike',

    'completion_notes' => 'Napomene o završetku',
    'completion_notes_placeholder' => 'Unesite napomene o stanju vozila, oštećenjima ili drugim zapažanjima...',
    'completion_notes_help' => 'Dodajte dodatne napomene o stanju vozila ili završetku najma.',

    'completed_at' => 'Završeno',

    // Coupon fields
    'coupon_code' => 'Kod kupona',
    'coupon_amount' => 'Iznos popusta kupona',
    'enter_coupon_code' => 'Unesite kod kupona',
    'coupon_discount_amount' => 'Iznos popusta',
    'applied_coupon' => 'Primijenjen kupon',

    // Validation messages
    'validation' => [
        'completion_miles_integer' => 'Kilometraža mora biti valjan broj.',
        'completion_miles_min' => 'Kilometraža mora biti najmanje 0.',
        'completion_gas_level_invalid' => 'Molimo odaberite valjanu razinu goriva.',
        'damage_image_invalid' => 'Prenesena datoteka mora biti valjana slika.',
        'damage_image_max_size' => 'Veličina slike ne smije prelaziti 5MB.',
        'completion_notes_max' => 'Napomene ne smiju prelaziti 10,000 znakova.',
    ],

    // Vendor booking actions
    'approve_booking' => 'Одобри резервацију',
    'cancel_booking' => 'Откажи резервацију',
    'processing' => 'Обрада...',
    'pending_approval_notice' => 'Чека одобрење',
    'pending_approval_description' => 'Ова резервација чека ваше одобрење. Молимо вас да прегледате детаље и одобрите или откажете резервацију.',
    'approve_booking_confirmation' => 'Да ли сте сигурни да желите да одобрите ову резервацију? Купац ће бити обавештен.',
    'cancel_booking_confirmation' => 'Да ли сте сигурни да желите да откажете ову резервацију? Ова акција се не може поништити.',
    'booking_approved_successfully' => 'Резервација је успешно одобрена.',
    'booking_cancelled_successfully' => 'Резервација је успешно отказана.',
    'cannot_approve_booking' => 'Ова резервација не може бити одобрена. Само резервације на чекању могу бити одобрене.',
    'cannot_cancel_booking' => 'Ова резервација не може бити отказана. Завршене или већ отказане резервације не могу бити отказане.',
];

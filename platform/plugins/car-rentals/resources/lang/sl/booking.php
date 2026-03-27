<?php

return [
    'name' => 'Rezervacije',
    'create' => 'Nova rezervacija',
    'reports' => 'Poročila o rezervacijah',
    'calendar' => 'Koledar rezervacija',
    'statuses' => [
        'pending' => 'V čakanju',
        'processing' => 'V obdelavi',
        'completed' => 'Zaključeno',
        'cancelled' => 'Preklicano',
    ],
    'customer' => 'Stranka',
    'amount' => 'Znesek',
    'rental_period' => 'Obdobje najema',
    'payment_method' => 'Način plačila',
    'payment_status' => 'Status plačila',
    'booking_information' => 'Informacije o rezervaciji',
    'booking_period' => 'Obdobje rezervacije',
    'payment_status_label' => 'Status plačila',
    'car' => 'Vozilo',
    'calendar_item_title' => ':car (:customer)',

    // Dates
    'start_date' => 'Datum začetka',
    'end_date' => 'Datum zaključka',

    // Car search
    'search_cars' => 'Išči vozila',
    'selected_car' => 'Izbrano vozilo',
    'please_select_dates' => 'Prosimo izberite datum začetka in zaključka',
    'please_select_car' => 'Prosimo izberite vozilo za nadaljevanje rezervacije',

    // Booking details
    'booking_details' => 'Podrobnosti rezervacije',

    // Customer information
    'search_customer' => 'Išči stranko po imenu, e-pošti ali telefonu...',
    'create_new_customer' => 'Ustvari novo stranko',
    'customer_created_successfully' => 'Stranka uspešno ustvarjena',
    'customer_not_found' => 'Stranka ni najdena',
    'customer_information' => 'Informacije o stranki',
    'customer_name' => 'Ime',
    'email' => 'E-pošta',
    'phone' => 'Telefon',
    'customer_age' => 'Starost',
    'address' => 'Naslov',
    'city' => 'Mesto',
    'state' => 'Država/Pokrajina',
    'country' => 'Država',
    'zip' => 'Poštna številka',
    'note' => 'Opomba',
    'note_placeholder' => 'Vnesite posebne zahteve ali opombe',

    // Services
    'services' => 'Dodatne storitve',
    'day' => 'dan',

    // Payment
    'payment_status' => 'Status plačila',
    'transaction_id' => 'ID transakcije',
    'transaction_id_helper' => 'Lahko pustite to polje prazno če je način plačila gotovina ali bančno nakazilo',
    'payment_method_helper' => 'Izberite način plačila uporabljen za to rezervacijo',
    'payment_status_helper' => 'Trenutni status plačila',

    // Form placeholders
    'first_name_placeholder' => 'Vnesite ime',
    'last_name_placeholder' => 'Vnesite priimek',
    'email_placeholder' => 'Vnesite naslov e-pošte',
    'phone_placeholder' => 'Vnesite telefonsko številko',
    'address_placeholder' => 'Vnesite naslov',
    'city_placeholder' => 'Vnesite mesto',
    'state_placeholder' => 'Vnesite državo/pokrajinu',
    'country_placeholder' => 'Vnesite državo',
    'zip_placeholder' => 'Vnesite poštno številko',

    // Misc
    'no_customers_found' => 'Ni najdenih strank',
    'no_cars_available' => 'Ni razpoložljivih vozila za izbrane datume',
    'select_car' => 'Izberi vozilo',
    'print_booking_info' => 'Natisni informacije o rezervaciji',
    'printed_on' => 'Natisnjeno',
    'computer_generated_document' => 'To je računalniško ustvarjen dokument in ne zahteva podpis.',
    'booking_summary' => 'Povzetek rezervacije',
    'booking_details' => 'Podrobnosti rezervacije',
    'additional_services' => 'Dodatne storitve',
    'rental_period' => 'Obdobje najema',
    'to' => 'do',

    // Completion details
    'completion_details' => 'Podrobnosti zaključka',
    'add_completion_details' => 'Dodaj podrobnosti zaključka',
    'edit_completion_details' => 'Uredi podrobnosti zaključka',
    'no_completion_details' => 'Še niso dodane podrobnosti zaključka.',
    'completion_details_updated_successfully' => 'Podrobnosti zaključka uspešno posodobljene.',

    'completion_miles' => 'Končna kilometrina',
    'completion_kilometers' => 'Končni kilometri',
    'miles' => 'milje',
    'kilometers' => 'kilometri',
    'enter_miles' => 'Vnesite končno kilometrino',
    'enter_kilometers' => 'Vnesite končne kilometre',
    'completion_miles_help' => 'Vnesite končno odčitavanje kilometraže ko je vozilo vrnjeno.',
    'completion_kilometers_help' => 'Vnesite končno odčitavanje kilometrov ko je vozilo vrnjeno.',

    'completion_gas_level' => 'Raven goriva',
    'select_gas_level' => 'Izberite raven goriva',
    'gas_empty' => 'Prazno',
    'gas_quarter' => '1/4 rezervoarja',
    'gas_half' => '1/2 rezervoarja',
    'gas_three_quarters' => '3/4 rezervoarja',
    'gas_full' => 'Poln rezervoar',
    'completion_gas_level_help' => 'Izberite raven goriva ko je vozilo vrnjeno.',

    'damage_images' => 'Slike poškodb',
    'damage_image' => 'Slika poškodbe',
    'damage_images_help' => 'Naložite slike katerih koli poškodbe najdenih na vozilu (največ 5MB po slici).',
    'existing_images' => 'Obstoječe slike',

    'completion_notes' => 'Opombe o zaključku',
    'completion_notes_placeholder' => 'Vnesite opombe o stanju vozila, poškodbah ali drugim opažanjih...',
    'completion_notes_help' => 'Dodajte dodatne opombe o stanju vozila ali zaključku najema.',

    'completed_at' => 'Zaključeno',

    // Coupon fields
    'coupon_code' => 'Koda kupona',
    'coupon_amount' => 'Znesek popusta kupona',
    'enter_coupon_code' => 'Vnesite kodo kupona',
    'coupon_discount_amount' => 'Znesek popusta',
    'applied_coupon' => 'Uveljavljen kupon',

    // Validation messages
    'validation' => [
        'completion_miles_integer' => 'Kilometraža mora biti veljavno število.',
        'completion_miles_min' => 'Kilometraža mora biti najmanj 0.',
        'completion_gas_level_invalid' => 'Prosimo izberite veljavno raven goriva.',
        'damage_image_invalid' => 'Naložena datoteka mora biti veljavena slika.',
        'damage_image_max_size' => 'Velikost slike ne sme presegati 5MB.',
        'completion_notes_max' => 'Opombe ne smejo presegati 10,000 znakov.',
    ],

    // Vendor booking actions
    'approve_booking' => 'Odobri rezervacijo',
    'cancel_booking' => 'Prekliči rezervacijo',
    'processing' => 'Obdelava...',
    'pending_approval_notice' => 'Čaka na odobritev',
    'pending_approval_description' => 'Ta rezervacija čaka na vašo odobritev. Preglejte podrobnosti in odobrite ali prekličite rezervacijo.',
    'approve_booking_confirmation' => 'Ali ste prepričani, da želite odobriti to rezervacijo? Stranka bo obveščena.',
    'cancel_booking_confirmation' => 'Ali ste prepričani, da želite preklicati to rezervacijo? Tega dejanja ni mogoče razveljaviti.',
    'booking_approved_successfully' => 'Rezervacija je bila uspešno odobrena.',
    'booking_cancelled_successfully' => 'Rezervacija je bila uspešno preklicana.',
    'cannot_approve_booking' => 'Te rezervacije ni mogoče odobriti. Odobriti je mogoče samo čakajoče rezervacije.',
    'cannot_cancel_booking' => 'Te rezervacije ni mogoče preklicati. Zaključenih ali že preklicanih rezervacij ni mogoče preklicati.',
];

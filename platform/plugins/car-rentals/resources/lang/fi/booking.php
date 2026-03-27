<?php

return [
    'name' => 'Varaukset',
    'create' => 'Uusi varaus',
    'reports' => 'Varausraportit',
    'calendar' => 'Varauskalenteri',
    'statuses' => [
        'pending' => 'Odottaa',
        'processing' => 'Käsittelyssä',
        'completed' => 'Valmis',
        'cancelled' => 'Peruutettu',
    ],
    'customer' => 'Asiakas',
    'amount' => 'Summa',
    'rental_period' => 'Vuokra-aika',
    'payment_method' => 'Maksutapa',
    'payment_status' => 'Maksun tila',
    'booking_information' => 'Varauksen tiedot',
    'booking_period' => 'Varausjakso',
    'payment_status_label' => 'Maksun tila',
    'car' => 'Auto',
    'calendar_item_title' => ':car (:customer)',

    // Dates
    'start_date' => 'Alkamispäivä',
    'end_date' => 'Päättymispäivä',

    // Car search
    'search_cars' => 'Hae autoja',
    'selected_car' => 'Valittu auto',
    'please_select_dates' => 'Valitse sekä alku- että päättymispäivä',
    'please_select_car' => 'Valitse auto jatkaaksesi varausta',

    // Booking details
    'booking_details' => 'Varauksen tiedot',

    // Customer information
    'search_customer' => 'Hae asiakasta nimellä, sähköpostilla tai puhelimella...',
    'create_new_customer' => 'Luo uusi asiakas',
    'customer_created_successfully' => 'Asiakas luotu onnistuneesti',
    'customer_not_found' => 'Asiakasta ei löytynyt',
    'customer_information' => 'Asiakkaan tiedot',
    'customer_name' => 'Nimi',
    'email' => 'Sähköposti',
    'phone' => 'Puhelin',
    'customer_age' => 'Ikä',
    'address' => 'Osoite',
    'city' => 'Kaupunki',
    'state' => 'Lääni/Maakunta',
    'country' => 'Maa',
    'zip' => 'Postinumero',
    'note' => 'Huomautus',
    'note_placeholder' => 'Kirjoita erityistoiveet tai huomautukset',

    // Services
    'services' => 'Lisäpalvelut',
    'day' => 'päivä',

    // Payment
    'payment_status' => 'Maksun tila',
    'transaction_id' => 'Maksutapahtuman tunnus',
    'transaction_id_helper' => 'Voit jättää tämän kentän tyhjäksi, jos maksutapa on postiennakko tai tilisiirto',
    'payment_method_helper' => 'Valitse tähän varaukseen käytetty maksutapa',
    'payment_status_helper' => 'Maksun nykyinen tila',

    // Form placeholders
    'first_name_placeholder' => 'Kirjoita etunimi',
    'last_name_placeholder' => 'Kirjoita sukunimi',
    'email_placeholder' => 'Kirjoita sähköpostiosoite',
    'phone_placeholder' => 'Kirjoita puhelinnumero',
    'address_placeholder' => 'Kirjoita osoite',
    'city_placeholder' => 'Kirjoita kaupunki',
    'state_placeholder' => 'Kirjoita lääni/maakunta',
    'country_placeholder' => 'Kirjoita maa',
    'zip_placeholder' => 'Kirjoita postinumero',

    // Misc
    'no_customers_found' => 'Asiakkaita ei löytynyt',
    'no_cars_available' => 'Valituilla päivämäärillä ei ole autoja saatavilla',
    'select_car' => 'Valitse auto',
    'print_booking_info' => 'Tulosta varauksen tiedot',
    'printed_on' => 'Tulostettu',
    'computer_generated_document' => 'Tämä on tietokoneen luoma asiakirja, eikä vaadi allekirjoitusta.',
    'booking_summary' => 'Varauksen yhteenveto',
    'booking_details' => 'Varauksen tiedot',
    'additional_services' => 'Lisäpalvelut',
    'rental_period' => 'Vuokra-aika',
    'to' => 'asti',

    // Completion details
    'completion_details' => 'Valmistumisen tiedot',
    'add_completion_details' => 'Lisää valmistumisen tiedot',
    'edit_completion_details' => 'Muokkaa valmistumisen tietoja',
    'no_completion_details' => 'Valmistumisen tietoja ei ole vielä lisätty.',
    'completion_details_updated_successfully' => 'Valmistumisen tiedot päivitetty onnistuneesti.',

    'completion_miles' => 'Lopullinen mittarilukema',
    'completion_kilometers' => 'Lopulliset kilometrit',
    'miles' => 'mailia',
    'kilometers' => 'kilometriä',
    'enter_miles' => 'Kirjoita lopullinen mittarilukema',
    'enter_kilometers' => 'Kirjoita lopulliset kilometrit',
    'completion_miles_help' => 'Kirjoita lopullinen mittarilukema auton palautuksen yhteydessä.',
    'completion_kilometers_help' => 'Kirjoita lopullinen kilometrilukema auton palautuksen yhteydessä.',

    'completion_gas_level' => 'Polttoaineen taso',
    'select_gas_level' => 'Valitse polttoaineen taso',
    'gas_empty' => 'Tyhjä',
    'gas_quarter' => '1/4 tankki',
    'gas_half' => '1/2 tankki',
    'gas_three_quarters' => '3/4 tankki',
    'gas_full' => 'Täysi tankki',
    'completion_gas_level_help' => 'Valitse polttoaineen taso auton palautuksen yhteydessä.',

    'damage_images' => 'Vahinkokuvat',
    'damage_image' => 'Vahinkokuva',
    'damage_images_help' => 'Lataa kuvia ajoneuvosta löydetyistä vaurioista (enintään 5 Mt kuvaa kohden).',
    'existing_images' => 'Olemassa olevat kuvat',

    'completion_notes' => 'Valmistumisen huomautukset',
    'completion_notes_placeholder' => 'Kirjoita huomautuksia ajoneuvon kunnosta, vaurioista tai muista havainnoista...',
    'completion_notes_help' => 'Lisää lisähuomautuksia ajoneuvon kunnosta tai vuokrauksen päättymisestä.',

    'completed_at' => 'Valmistunut',

    // Coupon fields
    'coupon_code' => 'Kuponkikoodi',
    'coupon_amount' => 'Kupongin alennussumma',
    'enter_coupon_code' => 'Kirjoita kuponkikoodi',
    'coupon_discount_amount' => 'Alennussumma',
    'applied_coupon' => 'Käytetty kuponki',

    // Validation messages
    'validation' => [
        'completion_miles_integer' => 'Mittarilukeman on oltava kelvollinen luku.',
        'completion_miles_min' => 'Mittarilukeman on oltava vähintään 0.',
        'completion_gas_level_invalid' => 'Valitse kelvollinen polttoaineen taso.',
        'damage_image_invalid' => 'Ladatun tiedoston on oltava kelvollinen kuva.',
        'damage_image_max_size' => 'Kuvan koko ei saa ylittää 5 Mt.',
        'completion_notes_max' => 'Huomautukset eivät saa ylittää 10 000 merkkiä.',
    ],

    // Vendor booking actions
    'approve_booking' => 'Hyväksy varaus',
    'cancel_booking' => 'Peruuta varaus',
    'processing' => 'Käsitellään...',
    'pending_approval_notice' => 'Odottaa hyväksyntää',
    'pending_approval_description' => 'Tämä varaus odottaa hyväksyntääsi. Tarkista tiedot ja hyväksy tai peruuta varaus.',
    'approve_booking_confirmation' => 'Oletko varma, että haluat hyväksyä tämän varauksen? Asiakkaalle ilmoitetaan.',
    'cancel_booking_confirmation' => 'Oletko varma, että haluat peruuttaa tämän varauksen? Tätä toimintoa ei voi peruuttaa.',
    'booking_approved_successfully' => 'Varaus on hyväksytty onnistuneesti.',
    'booking_cancelled_successfully' => 'Varaus on peruutettu onnistuneesti.',
    'cannot_approve_booking' => 'Tätä varausta ei voida hyväksyä. Vain odottavat varaukset voidaan hyväksyä.',
    'cannot_cancel_booking' => 'Tätä varausta ei voida peruuttaa. Valmistuneita tai jo peruutettuja varauksia ei voi peruuttaa.',
];

<?php

return [
    'name' => 'Rezerwacje',
    'create' => 'Nowa rezerwacja',
    'reports' => 'Raporty Rezerwacji',
    'calendar' => 'Kalendarz Rezerwacji',
    'statuses' => [
        'pending' => 'Oczekująca',
        'processing' => 'Przetwarzanie',
        'completed' => 'Zakończona',
        'cancelled' => 'Anulowana',
    ],
    'customer' => 'Klient',
    'amount' => 'Kwota',
    'rental_period' => 'Okres Wynajmu',
    'payment_method' => 'Metoda Płatności',
    'payment_status' => 'Status Płatności',
    'booking_information' => 'Informacje o Rezerwacji',
    'booking_period' => 'Okres Rezerwacji',
    'payment_status_label' => 'Status Płatności',
    'car' => 'Samochód',
    'calendar_item_title' => ':car (:customer)',

    // Dates
    'start_date' => 'Data Rozpoczęcia',
    'end_date' => 'Data Zakończenia',

    // Car search
    'search_cars' => 'Szukaj Samochodów',
    'selected_car' => 'Wybrany Samochód',
    'please_select_dates' => 'Proszę wybrać datę rozpoczęcia i zakończenia',
    'please_select_car' => 'Proszę wybrać samochód, aby kontynuować rezerwację',

    // Booking details
    'booking_details' => 'Szczegóły Rezerwacji',

    // Customer information
    'search_customer' => 'Szukaj klienta po nazwisku, e-mailu lub telefonie...',
    'create_new_customer' => 'Utwórz nowego klienta',
    'customer_created_successfully' => 'Klient został pomyślnie utworzony',
    'customer_not_found' => 'Nie znaleziono klienta',
    'customer_information' => 'Informacje o Kliencie',
    'customer_name' => 'Nazwisko',
    'email' => 'E-mail',
    'phone' => 'Telefon',
    'customer_age' => 'Wiek',
    'address' => 'Adres',
    'city' => 'Miasto',
    'state' => 'Województwo',
    'country' => 'Kraj',
    'zip' => 'Kod Pocztowy',
    'note' => 'Uwaga',
    'note_placeholder' => 'Wpisz specjalne życzenia lub uwagi',

    // Services
    'services' => 'Dodatkowe Usługi',
    'day' => 'dzień',

    // Payment
    'payment_status' => 'Status płatności',
    'transaction_id' => 'ID Transakcji',
    'transaction_id_helper' => 'To pole można pozostawić puste, jeśli metoda płatności to COD lub przelew bankowy',
    'payment_method_helper' => 'Wybierz metodę płatności użytą w tej rezerwacji',
    'payment_status_helper' => 'Aktualny status płatności',

    // Form placeholders
    'first_name_placeholder' => 'Wpisz imię',
    'last_name_placeholder' => 'Wpisz nazwisko',
    'email_placeholder' => 'Wpisz adres e-mail',
    'phone_placeholder' => 'Wpisz numer telefonu',
    'address_placeholder' => 'Wpisz adres',
    'city_placeholder' => 'Wpisz miasto',
    'state_placeholder' => 'Wpisz województwo',
    'country_placeholder' => 'Wpisz kraj',
    'zip_placeholder' => 'Wpisz kod pocztowy',

    // Misc
    'no_customers_found' => 'Nie znaleziono klientów',
    'no_cars_available' => 'Brak dostępnych samochodów dla wybranych dat',
    'select_car' => 'Wybierz Samochód',
    'print_booking_info' => 'Drukuj Informacje o Rezerwacji',
    'printed_on' => 'Wydrukowano dnia',
    'computer_generated_document' => 'Jest to dokument wygenerowany komputerowo i nie wymaga podpisu.',
    'booking_summary' => 'Podsumowanie Rezerwacji',
    'booking_details' => 'Szczegóły Rezerwacji',
    'additional_services' => 'Dodatkowe Usługi',
    'rental_period' => 'Okres Wynajmu',
    'to' => 'do',

    // Completion details
    'completion_details' => 'Szczegóły Zakończenia',
    'add_completion_details' => 'Dodaj Szczegóły Zakończenia',
    'edit_completion_details' => 'Edytuj Szczegóły Zakończenia',
    'no_completion_details' => 'Nie dodano jeszcze szczegółów zakończenia.',
    'completion_details_updated_successfully' => 'Szczegóły zakończenia zostały pomyślnie zaktualizowane.',

    'completion_miles' => 'Końcowy Przebieg',
    'completion_kilometers' => 'Końcowe Kilometry',
    'miles' => 'mile',
    'kilometers' => 'kilometry',
    'enter_miles' => 'Wpisz końcowy przebieg',
    'enter_kilometers' => 'Wpisz końcowe kilometry',
    'completion_miles_help' => 'Wpisz końcowy odczyt przebiegu z chwili zwrotu samochodu.',
    'completion_kilometers_help' => 'Wpisz końcowy odczyt kilometrów z chwili zwrotu samochodu.',

    'completion_gas_level' => 'Poziom Paliwa',
    'select_gas_level' => 'Wybierz poziom paliwa',
    'gas_empty' => 'Pusty',
    'gas_quarter' => '1/4 Baku',
    'gas_half' => '1/2 Baku',
    'gas_three_quarters' => '3/4 Baku',
    'gas_full' => 'Pełny Bak',
    'completion_gas_level_help' => 'Wybierz poziom paliwa z chwili zwrotu samochodu.',

    'damage_images' => 'Zdjęcia Uszkodzeń',
    'damage_image' => 'Zdjęcie Uszkodzenia',
    'damage_images_help' => 'Prześlij zdjęcia wszelkich uszkodzeń znalezionych na pojeździe (max 5 MB na zdjęcie).',
    'existing_images' => 'Istniejące zdjęcia',

    'completion_notes' => 'Uwagi Zakończenia',
    'completion_notes_placeholder' => 'Wpisz wszelkie uwagi dotyczące stanu pojazdu, uszkodzeń lub innych obserwacji...',
    'completion_notes_help' => 'Dodaj wszelkie dodatkowe uwagi dotyczące stanu pojazdu lub zakończenia wynajmu.',

    'completed_at' => 'Zakończono O',

    // Coupon fields
    'coupon_code' => 'Kod Kuponu',
    'coupon_amount' => 'Kwota Rabatu Kuponu',
    'enter_coupon_code' => 'Wpisz kod kuponu',
    'coupon_discount_amount' => 'Kwota Rabatu',
    'applied_coupon' => 'Zastosowany Kupon',

    // Validation messages
    'validation' => [
        'completion_miles_integer' => 'Przebieg musi być poprawną liczbą.',
        'completion_miles_min' => 'Przebieg musi wynosić co najmniej 0.',
        'completion_gas_level_invalid' => 'Proszę wybrać prawidłowy poziom paliwa.',
        'damage_image_invalid' => 'Przesłany plik musi być prawidłowym obrazem.',
        'damage_image_max_size' => 'Rozmiar obrazu nie może przekraczać 5 MB.',
        'completion_notes_max' => 'Uwagi nie mogą przekraczać 10 000 znaków.',
    ],

    // Vendor booking actions
    'approve_booking' => 'Zatwierdź rezerwację',
    'cancel_booking' => 'Anuluj rezerwację',
    'processing' => 'Przetwarzanie...',
    'pending_approval_notice' => 'Oczekuje na zatwierdzenie',
    'pending_approval_description' => 'Ta rezerwacja oczekuje na zatwierdzenie. Przejrzyj szczegóły i zatwierdź lub anuluj rezerwację.',
    'approve_booking_confirmation' => 'Czy na pewno chcesz zatwierdzić tę rezerwację? Klient zostanie powiadomiony.',
    'cancel_booking_confirmation' => 'Czy na pewno chcesz anulować tę rezerwację? Tej czynności nie można cofnąć.',
    'booking_approved_successfully' => 'Rezerwacja została zatwierdzona pomyślnie.',
    'booking_cancelled_successfully' => 'Rezerwacja została anulowana pomyślnie.',
    'cannot_approve_booking' => 'Tej rezerwacji nie można zatwierdzić. Można zatwierdzić tylko oczekujące rezerwacje.',
    'cannot_cancel_booking' => 'Tej rezerwacji nie można anulować. Ukończonych lub już anulowanych rezerwacji nie można anulować.',
];

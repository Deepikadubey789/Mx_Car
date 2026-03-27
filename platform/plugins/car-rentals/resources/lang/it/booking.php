<?php

return [
    'name' => 'Prenotazioni',
    'create' => 'Nuova prenotazione',
    'reports' => 'Report Prenotazioni',
    'calendar' => 'Calendario Prenotazioni',
    'statuses' => [
        'pending' => 'In attesa',
        'processing' => 'In elaborazione',
        'completed' => 'Completata',
        'cancelled' => 'Annullata',
    ],
    'customer' => 'Cliente',
    'amount' => 'Importo',
    'rental_period' => 'Periodo di Noleggio',
    'payment_method' => 'Metodo di Pagamento',
    'payment_status' => 'Stato Pagamento',
    'booking_information' => 'Informazioni Prenotazione',
    'booking_period' => 'Periodo Prenotazione',
    'payment_status_label' => 'Stato Pagamento',
    'car' => 'Auto',
    'calendar_item_title' => ':car (:customer)',

    // Dates
    'start_date' => 'Data Inizio',
    'end_date' => 'Data Fine',

    // Car search
    'search_cars' => 'Cerca Auto',
    'selected_car' => 'Auto Selezionata',
    'please_select_dates' => 'Seleziona sia la data di inizio che quella di fine',
    'please_select_car' => 'Seleziona un\'auto per continuare con la prenotazione',

    // Booking details
    'booking_details' => 'Dettagli Prenotazione',

    // Customer information
    'search_customer' => 'Cerca cliente per nome, email o telefono...',
    'create_new_customer' => 'Crea nuovo cliente',
    'customer_created_successfully' => 'Cliente creato con successo',
    'customer_not_found' => 'Cliente non trovato',
    'customer_information' => 'Informazioni Cliente',
    'customer_name' => 'Nome',
    'email' => 'Email',
    'phone' => 'Telefono',
    'customer_age' => 'Età',
    'address' => 'Indirizzo',
    'city' => 'Città',
    'state' => 'Stato/Provincia',
    'country' => 'Paese',
    'zip' => 'CAP/Codice Postale',
    'note' => 'Nota',
    'note_placeholder' => 'Inserisci richieste speciali o note',

    // Services
    'services' => 'Servizi Aggiuntivi',
    'day' => 'giorno',

    // Payment
    'payment_status' => 'Stato pagamento',
    'transaction_id' => 'ID Transazione',
    'transaction_id_helper' => 'Puoi lasciare vuoto questo campo se il metodo di pagamento è COD o Bonifico bancario',
    'payment_method_helper' => 'Seleziona il metodo di pagamento utilizzato per questa prenotazione',
    'payment_status_helper' => 'Stato attuale del pagamento',

    // Form placeholders
    'first_name_placeholder' => 'Inserisci nome',
    'last_name_placeholder' => 'Inserisci cognome',
    'email_placeholder' => 'Inserisci indirizzo email',
    'phone_placeholder' => 'Inserisci numero di telefono',
    'address_placeholder' => 'Inserisci indirizzo',
    'city_placeholder' => 'Inserisci città',
    'state_placeholder' => 'Inserisci stato/provincia',
    'country_placeholder' => 'Inserisci paese',
    'zip_placeholder' => 'Inserisci CAP/Codice postale',

    // Misc
    'no_customers_found' => 'Nessun cliente trovato',
    'no_cars_available' => 'Nessuna auto disponibile per le date selezionate',
    'select_car' => 'Seleziona Auto',
    'print_booking_info' => 'Stampa Info Prenotazione',
    'printed_on' => 'Stampato il',
    'computer_generated_document' => 'Questo è un documento generato automaticamente e non richiede firma.',
    'booking_summary' => 'Riepilogo Prenotazione',
    'booking_details' => 'Dettagli Prenotazione',
    'additional_services' => 'Servizi Aggiuntivi',
    'rental_period' => 'Periodo di Noleggio',
    'to' => 'a',

    // Completion details
    'completion_details' => 'Dettagli Completamento',
    'add_completion_details' => 'Aggiungi Dettagli Completamento',
    'edit_completion_details' => 'Modifica Dettagli Completamento',
    'no_completion_details' => 'Non sono ancora stati aggiunti dettagli di completamento.',
    'completion_details_updated_successfully' => 'Dettagli di completamento aggiornati con successo.',

    'completion_miles' => 'Chilometraggio Finale',
    'completion_kilometers' => 'Chilometri Finali',
    'miles' => 'miglia',
    'kilometers' => 'chilometri',
    'enter_miles' => 'Inserisci chilometraggio finale',
    'enter_kilometers' => 'Inserisci chilometri finali',
    'completion_miles_help' => 'Inserisci il chilometraggio finale quando l\'auto è stata restituita.',
    'completion_kilometers_help' => 'Inserisci i chilometri finali quando l\'auto è stata restituita.',

    'completion_gas_level' => 'Livello Carburante',
    'select_gas_level' => 'Seleziona livello carburante',
    'gas_empty' => 'Vuoto',
    'gas_quarter' => '1/4 Serbatoio',
    'gas_half' => '1/2 Serbatoio',
    'gas_three_quarters' => '3/4 Serbatoio',
    'gas_full' => 'Serbatoio Pieno',
    'completion_gas_level_help' => 'Seleziona il livello di carburante quando l\'auto è stata restituita.',

    'damage_images' => 'Immagini Danni',
    'damage_image' => 'Immagine Danno',
    'damage_images_help' => 'Carica immagini di eventuali danni trovati sul veicolo (max 5MB per immagine).',
    'existing_images' => 'Immagini esistenti',

    'completion_notes' => 'Note Completamento',
    'completion_notes_placeholder' => 'Inserisci eventuali note sulle condizioni del veicolo, danni o altre osservazioni...',
    'completion_notes_help' => 'Aggiungi eventuali note aggiuntive sulle condizioni del veicolo o il completamento del noleggio.',

    'completed_at' => 'Completato il',

    // Coupon fields
    'coupon_code' => 'Codice Coupon',
    'coupon_amount' => 'Importo Sconto Coupon',
    'enter_coupon_code' => 'Inserisci codice coupon',
    'coupon_discount_amount' => 'Importo Sconto',
    'applied_coupon' => 'Coupon Applicato',

    // Validation messages
    'validation' => [
        'completion_miles_integer' => 'Il chilometraggio deve essere un numero valido.',
        'completion_miles_min' => 'Il chilometraggio deve essere almeno 0.',
        'completion_gas_level_invalid' => 'Seleziona un livello di carburante valido.',
        'damage_image_invalid' => 'Il file caricato deve essere un\'immagine valida.',
        'damage_image_max_size' => 'La dimensione dell\'immagine non deve superare 5MB.',
        'completion_notes_max' => 'Le note non devono superare 10.000 caratteri.',
    ],

    // Vendor booking actions
    'approve_booking' => 'Approva prenotazione',
    'cancel_booking' => 'Annulla prenotazione',
    'processing' => 'Elaborazione...',
    'pending_approval_notice' => 'In attesa di approvazione',
    'pending_approval_description' => 'Questa prenotazione è in attesa della tua approvazione. Si prega di rivedere i dettagli e approvare o annullare la prenotazione.',
    'approve_booking_confirmation' => 'Sei sicuro di voler approvare questa prenotazione? Il cliente verrà notificato.',
    'cancel_booking_confirmation' => 'Sei sicuro di voler annullare questa prenotazione? Questa azione non può essere annullata.',
    'booking_approved_successfully' => 'La prenotazione è stata approvata con successo.',
    'booking_cancelled_successfully' => 'La prenotazione è stata annullata con successo.',
    'cannot_approve_booking' => 'Questa prenotazione non può essere approvata. Solo le prenotazioni in sospeso possono essere approvate.',
    'cannot_cancel_booking' => 'Questa prenotazione non può essere annullata. Le prenotazioni completate o già annullate non possono essere annullate.',
];

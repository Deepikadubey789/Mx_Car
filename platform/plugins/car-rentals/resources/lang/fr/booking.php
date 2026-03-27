<?php

return [
    'name' => 'Réservations',
    'create' => 'Nouvelle réservation',
    'reports' => 'Rapports de Réservation',
    'calendar' => 'Calendrier des Réservations',
    'statuses' => [
        'pending' => 'En attente',
        'processing' => 'En cours de traitement',
        'completed' => 'Terminée',
        'cancelled' => 'Annulée',
    ],
    'customer' => 'Client',
    'amount' => 'Montant',
    'rental_period' => 'Période de Location',
    'payment_method' => 'Méthode de Paiement',
    'payment_status' => 'Statut du Paiement',
    'booking_information' => 'Informations de Réservation',
    'booking_period' => 'Période de Réservation',
    'payment_status_label' => 'Statut du Paiement',
    'car' => 'Voiture',
    'calendar_item_title' => ':car (:customer)',

    // Dates
    'start_date' => 'Date de Début',
    'end_date' => 'Date de Fin',

    // Car search
    'search_cars' => 'Rechercher des Voitures',
    'selected_car' => 'Voiture Sélectionnée',
    'please_select_dates' => 'Veuillez sélectionner les dates de début et de fin',
    'please_select_car' => 'Veuillez sélectionner une voiture pour continuer avec la réservation',

    // Booking details
    'booking_details' => 'Détails de la Réservation',

    // Customer information
    'search_customer' => 'Rechercher un client par nom, email ou téléphone...',
    'create_new_customer' => 'Créer un nouveau client',
    'customer_created_successfully' => 'Client créé avec succès',
    'customer_not_found' => 'Client non trouvé',
    'customer_information' => 'Informations du Client',
    'customer_name' => 'Nom',
    'email' => 'Email',
    'phone' => 'Téléphone',
    'customer_age' => 'Âge',
    'address' => 'Adresse',
    'city' => 'Ville',
    'state' => 'État/Province',
    'country' => 'Pays',
    'zip' => 'Code Postal',
    'note' => 'Note',
    'note_placeholder' => 'Entrez toute demande spéciale ou note',

    // Services
    'services' => 'Services Supplémentaires',
    'day' => 'jour',

    // Payment
    'payment_status' => 'Statut du paiement',
    'transaction_id' => 'ID de Transaction',
    'transaction_id_helper' => 'Vous pouvez laisser ce champ vide si la méthode de paiement est Paiement à la livraison ou Virement bancaire',
    'payment_method_helper' => 'Sélectionnez la méthode de paiement utilisée pour cette réservation',
    'payment_status_helper' => 'Statut actuel du paiement',

    // Form placeholders
    'first_name_placeholder' => 'Entrez le prénom',
    'last_name_placeholder' => 'Entrez le nom de famille',
    'email_placeholder' => 'Entrez l\'adresse email',
    'phone_placeholder' => 'Entrez le numéro de téléphone',
    'address_placeholder' => 'Entrez l\'adresse',
    'city_placeholder' => 'Entrez la ville',
    'state_placeholder' => 'Entrez l\'état/province',
    'country_placeholder' => 'Entrez le pays',
    'zip_placeholder' => 'Entrez le code postal',

    // Misc
    'no_customers_found' => 'Aucun client trouvé',
    'no_cars_available' => 'Aucune voiture disponible pour les dates sélectionnées',
    'select_car' => 'Sélectionner une Voiture',
    'print_booking_info' => 'Imprimer les Informations de Réservation',
    'printed_on' => 'Imprimé le',
    'computer_generated_document' => 'Ceci est un document généré par ordinateur et ne nécessite pas de signature.',
    'booking_summary' => 'Résumé de Réservation',
    'additional_services' => 'Services Supplémentaires',
    'to' => 'à',

    // Completion details
    'completion_details' => 'Détails de Finalisation',
    'add_completion_details' => 'Ajouter les Détails de Finalisation',
    'edit_completion_details' => 'Modifier les Détails de Finalisation',
    'no_completion_details' => 'Aucun détail de finalisation n\'a encore été ajouté.',
    'completion_details_updated_successfully' => 'Détails de finalisation mis à jour avec succès.',

    'completion_miles' => 'Kilométrage Final',
    'completion_kilometers' => 'Kilomètres Finaux',
    'miles' => 'miles',
    'kilometers' => 'kilomètres',
    'enter_miles' => 'Entrez le kilométrage final',
    'enter_kilometers' => 'Entrez les kilomètres finaux',
    'completion_miles_help' => 'Entrez la lecture finale du kilométrage lorsque la voiture a été retournée.',
    'completion_kilometers_help' => 'Entrez la lecture finale des kilomètres lorsque la voiture a été retournée.',

    'completion_gas_level' => 'Niveau d\'Essence',
    'select_gas_level' => 'Sélectionner le niveau d\'essence',
    'gas_empty' => 'Vide',
    'gas_quarter' => '1/4 de Réservoir',
    'gas_half' => '1/2 Réservoir',
    'gas_three_quarters' => '3/4 de Réservoir',
    'gas_full' => 'Réservoir Plein',
    'completion_gas_level_help' => 'Sélectionnez le niveau d\'essence lorsque la voiture a été retournée.',

    'damage_images' => 'Images de Dommages',
    'damage_image' => 'Image de Dommage',
    'damage_images_help' => 'Téléchargez des images de tout dommage trouvé sur le véhicule (max 5MB par image).',
    'existing_images' => 'Images existantes',

    'completion_notes' => 'Notes de Finalisation',
    'completion_notes_placeholder' => 'Entrez toute note sur l\'état du véhicule, les dommages ou autres observations...',
    'completion_notes_help' => 'Ajoutez toute note supplémentaire sur l\'état du véhicule ou la finalisation de la location.',

    'completed_at' => 'Terminé le',

    // Coupon fields
    'coupon_code' => 'Code de Coupon',
    'coupon_amount' => 'Montant de Remise du Coupon',
    'enter_coupon_code' => 'Entrez le code de coupon',
    'coupon_discount_amount' => 'Montant de Remise',
    'applied_coupon' => 'Coupon Appliqué',

    // Validation messages
    'validation' => [
        'completion_miles_integer' => 'Le kilométrage doit être un nombre valide.',
        'completion_miles_min' => 'Le kilométrage doit être au moins 0.',
        'completion_gas_level_invalid' => 'Veuillez sélectionner un niveau d\'essence valide.',
        'damage_image_invalid' => 'Le fichier téléchargé doit être une image valide.',
        'damage_image_max_size' => 'La taille de l\'image ne doit pas dépasser 5MB.',
        'completion_notes_max' => 'Les notes ne doivent pas dépasser 10,000 caractères.',
    ],

    // Vendor booking actions
    'approve_booking' => 'Approuver la réservation',
    'cancel_booking' => 'Annuler la réservation',
    'processing' => 'Traitement en cours...',
    'pending_approval_notice' => 'En attente d\'approbation',
    'pending_approval_description' => 'Cette réservation est en attente de votre approbation. Veuillez examiner les détails et approuver ou annuler la réservation.',
    'approve_booking_confirmation' => 'Êtes-vous sûr de vouloir approuver cette réservation ? Le client sera notifié.',
    'cancel_booking_confirmation' => 'Êtes-vous sûr de vouloir annuler cette réservation ? Cette action ne peut pas être annulée.',
    'booking_approved_successfully' => 'La réservation a été approuvée avec succès.',
    'booking_cancelled_successfully' => 'La réservation a été annulée avec succès.',
    'cannot_approve_booking' => 'Cette réservation ne peut pas être approuvée. Seules les réservations en attente peuvent être approuvées.',
    'cannot_cancel_booking' => 'Cette réservation ne peut pas être annulée. Les réservations terminées ou déjà annulées ne peuvent pas être annulées.',
];

<?php

return [
    'name' => 'Reservas',
    'create' => 'Nueva reserva',
    'reports' => 'Informes de Reservas',
    'calendar' => 'Calendario de Reservas',
    'statuses' => [
        'pending' => 'Pendiente',
        'processing' => 'Procesando',
        'completed' => 'Completada',
        'cancelled' => 'Cancelada',
    ],
    'customer' => 'Cliente',
    'amount' => 'Monto',
    'rental_period' => 'Período de Alquiler',
    'payment_method' => 'Método de Pago',
    'payment_status' => 'Estado del Pago',
    'booking_information' => 'Información de la Reserva',
    'booking_period' => 'Período de Reserva',
    'payment_status_label' => 'Estado del Pago',
    'car' => 'Coche',
    'calendar_item_title' => ':car (:customer)',

    // Dates
    'start_date' => 'Fecha de Inicio',
    'end_date' => 'Fecha de Fin',

    // Car search
    'search_cars' => 'Buscar Coches',
    'selected_car' => 'Coche Seleccionado',
    'please_select_dates' => 'Por favor seleccione fechas de inicio y fin',
    'please_select_car' => 'Por favor seleccione un coche para continuar con la reserva',

    // Booking details
    'booking_details' => 'Detalles de la Reserva',

    // Customer information
    'search_customer' => 'Buscar cliente por nombre, email o teléfono...',
    'create_new_customer' => 'Crear nuevo cliente',
    'customer_created_successfully' => 'Cliente creado con éxito',
    'customer_not_found' => 'Cliente no encontrado',
    'customer_information' => 'Información del Cliente',
    'customer_name' => 'Nombre',
    'email' => 'Email',
    'phone' => 'Teléfono',
    'customer_age' => 'Edad',
    'address' => 'Dirección',
    'city' => 'Ciudad',
    'state' => 'Estado/Provincia',
    'country' => 'País',
    'zip' => 'Código Postal',
    'note' => 'Nota',
    'note_placeholder' => 'Ingrese cualquier solicitud especial o notas',

    // Services
    'services' => 'Servicios Adicionales',
    'day' => 'día',

    // Payment
    'payment_status' => 'Estado del pago',
    'transaction_id' => 'ID de Transacción',
    'transaction_id_helper' => 'Puede dejar este campo vacío si el método de pago es Contra Reembolso o Transferencia Bancaria',
    'payment_method_helper' => 'Seleccione el método de pago utilizado para esta reserva',
    'payment_status_helper' => 'Estado actual del pago',

    // Form placeholders
    'first_name_placeholder' => 'Ingrese nombre',
    'last_name_placeholder' => 'Ingrese apellido',
    'email_placeholder' => 'Ingrese dirección de email',
    'phone_placeholder' => 'Ingrese número de teléfono',
    'address_placeholder' => 'Ingrese dirección',
    'city_placeholder' => 'Ingrese ciudad',
    'state_placeholder' => 'Ingrese estado/provincia',
    'country_placeholder' => 'Ingrese país',
    'zip_placeholder' => 'Ingrese código postal',

    // Misc
    'no_customers_found' => 'No se encontraron clientes',
    'no_cars_available' => 'No hay coches disponibles para las fechas seleccionadas',
    'select_car' => 'Seleccionar Coche',
    'print_booking_info' => 'Imprimir Información de Reserva',
    'printed_on' => 'Impreso el',
    'computer_generated_document' => 'Este es un documento generado por computadora y no requiere una firma.',
    'booking_summary' => 'Resumen de Reserva',
    'additional_services' => 'Servicios Adicionales',
    'to' => 'a',

    // Completion details
    'completion_details' => 'Detalles de Finalización',
    'add_completion_details' => 'Agregar Detalles de Finalización',
    'edit_completion_details' => 'Editar Detalles de Finalización',
    'no_completion_details' => 'Aún no se han agregado detalles de finalización.',
    'completion_details_updated_successfully' => 'Detalles de finalización actualizados con éxito.',

    'completion_miles' => 'Kilometraje Final',
    'completion_kilometers' => 'Kilómetros Finales',
    'miles' => 'millas',
    'kilometers' => 'kilómetros',
    'enter_miles' => 'Ingrese el kilometraje final',
    'enter_kilometers' => 'Ingrese los kilómetros finales',
    'completion_miles_help' => 'Ingrese la lectura final del kilometraje cuando se devolvió el coche.',
    'completion_kilometers_help' => 'Ingrese la lectura final de kilómetros cuando se devolvió el coche.',

    'completion_gas_level' => 'Nivel de Combustible',
    'select_gas_level' => 'Seleccionar nivel de combustible',
    'gas_empty' => 'Vacío',
    'gas_quarter' => '1/4 de Tanque',
    'gas_half' => '1/2 Tanque',
    'gas_three_quarters' => '3/4 de Tanque',
    'gas_full' => 'Tanque Lleno',
    'completion_gas_level_help' => 'Seleccione el nivel de combustible cuando se devolvió el coche.',

    'damage_images' => 'Imágenes de Daños',
    'damage_image' => 'Imagen de Daño',
    'damage_images_help' => 'Suba imágenes de cualquier daño encontrado en el vehículo (máximo 5MB por imagen).',
    'existing_images' => 'Imágenes existentes',

    'completion_notes' => 'Notas de Finalización',
    'completion_notes_placeholder' => 'Ingrese cualquier nota sobre el estado del vehículo, daños u otras observaciones...',
    'completion_notes_help' => 'Agregue cualquier nota adicional sobre el estado del vehículo o finalización del alquiler.',

    'completed_at' => 'Completado el',

    // Coupon fields
    'coupon_code' => 'Código de Cupón',
    'coupon_amount' => 'Monto de Descuento del Cupón',
    'enter_coupon_code' => 'Ingrese código de cupón',
    'coupon_discount_amount' => 'Monto de Descuento',
    'applied_coupon' => 'Cupón Aplicado',

    // Validation messages
    'validation' => [
        'completion_miles_integer' => 'El kilometraje debe ser un número válido.',
        'completion_miles_min' => 'El kilometraje debe ser al menos 0.',
        'completion_gas_level_invalid' => 'Por favor seleccione un nivel de combustible válido.',
        'damage_image_invalid' => 'El archivo subido debe ser una imagen válida.',
        'damage_image_max_size' => 'El tamaño de la imagen no debe exceder 5MB.',
        'completion_notes_max' => 'Las notas no deben exceder 10,000 caracteres.',
    ],

    // Vendor booking actions
    'approve_booking' => 'Aprobar reserva',
    'cancel_booking' => 'Cancelar reserva',
    'processing' => 'Procesando...',
    'pending_approval_notice' => 'Pendiente de aprobación',
    'pending_approval_description' => 'Esta reserva está esperando su aprobación. Por favor revise los detalles y apruebe o cancele la reserva.',
    'approve_booking_confirmation' => '¿Está seguro de que desea aprobar esta reserva? Se notificará al cliente.',
    'cancel_booking_confirmation' => '¿Está seguro de que desea cancelar esta reserva? Esta acción no se puede deshacer.',
    'booking_approved_successfully' => 'La reserva ha sido aprobada exitosamente.',
    'booking_cancelled_successfully' => 'La reserva ha sido cancelada exitosamente.',
    'cannot_approve_booking' => 'Esta reserva no puede ser aprobada. Solo se pueden aprobar reservas pendientes.',
    'cannot_cancel_booking' => 'Esta reserva no puede ser cancelada. Las reservas completadas o ya canceladas no pueden ser canceladas.',
];

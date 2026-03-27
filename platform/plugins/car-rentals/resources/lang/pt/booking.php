<?php

return [
    'name' => 'Reservas',
    'create' => 'Nova reserva',
    'reports' => 'Relatórios de Reservas',
    'calendar' => 'Calendário de Reservas',
    'statuses' => [
        'pending' => 'Pendente',
        'processing' => 'A processar',
        'completed' => 'Concluída',
        'cancelled' => 'Cancelada',
    ],
    'customer' => 'Cliente',
    'amount' => 'Montante',
    'rental_period' => 'Período de Aluguer',
    'payment_method' => 'Método de Pagamento',
    'payment_status' => 'Estado do Pagamento',
    'booking_information' => 'Informação da Reserva',
    'booking_period' => 'Período da Reserva',
    'payment_status_label' => 'Estado do Pagamento',
    'car' => 'Carro',
    'calendar_item_title' => ':car (:customer)',

    // Dates
    'start_date' => 'Data de Início',
    'end_date' => 'Data de Fim',

    // Car search
    'search_cars' => 'Pesquisar Carros',
    'selected_car' => 'Carro Selecionado',
    'please_select_dates' => 'Por favor, selecione as datas de início e fim',
    'please_select_car' => 'Por favor, selecione um carro para continuar com a reserva',

    // Booking details
    'booking_details' => 'Detalhes da Reserva',

    // Customer information
    'search_customer' => 'Pesquisar cliente por nome, email ou telefone...',
    'create_new_customer' => 'Criar novo cliente',
    'customer_created_successfully' => 'Cliente criado com sucesso',
    'customer_not_found' => 'Cliente não encontrado',
    'customer_information' => 'Informação do Cliente',
    'customer_name' => 'Nome',
    'email' => 'Email',
    'phone' => 'Telefone',
    'customer_age' => 'Idade',
    'address' => 'Morada',
    'city' => 'Cidade',
    'state' => 'Estado/Província',
    'country' => 'País',
    'zip' => 'Código Postal',
    'note' => 'Nota',
    'note_placeholder' => 'Inserir pedidos especiais ou notas',

    // Services
    'services' => 'Serviços Adicionais',
    'day' => 'dia',

    // Payment
    'payment_status' => 'Estado do pagamento',
    'transaction_id' => 'ID da Transação',
    'transaction_id_helper' => 'Pode deixar este campo vazio se o método de pagamento for à cobrança ou transferência bancária',
    'payment_method_helper' => 'Selecionar o método de pagamento usado para esta reserva',
    'payment_status_helper' => 'Estado atual do pagamento',

    // Form placeholders
    'first_name_placeholder' => 'Inserir primeiro nome',
    'last_name_placeholder' => 'Inserir último nome',
    'email_placeholder' => 'Inserir endereço de email',
    'phone_placeholder' => 'Inserir número de telefone',
    'address_placeholder' => 'Inserir morada',
    'city_placeholder' => 'Inserir cidade',
    'state_placeholder' => 'Inserir estado/província',
    'country_placeholder' => 'Inserir país',
    'zip_placeholder' => 'Inserir código postal',

    // Misc
    'no_customers_found' => 'Nenhum cliente encontrado',
    'no_cars_available' => 'Nenhum carro disponível para as datas selecionadas',
    'select_car' => 'Selecionar Carro',
    'print_booking_info' => 'Imprimir Informação da Reserva',
    'printed_on' => 'Impresso em',
    'computer_generated_document' => 'Este é um documento gerado por computador e não requer assinatura.',
    'booking_summary' => 'Resumo da Reserva',
    'booking_details' => 'Detalhes da Reserva',
    'additional_services' => 'Serviços Adicionais',
    'rental_period' => 'Período de Aluguer',
    'to' => 'até',

    // Completion details
    'completion_details' => 'Detalhes de Conclusão',
    'add_completion_details' => 'Adicionar Detalhes de Conclusão',
    'edit_completion_details' => 'Editar Detalhes de Conclusão',
    'no_completion_details' => 'Ainda não foram adicionados detalhes de conclusão.',
    'completion_details_updated_successfully' => 'Detalhes de conclusão atualizados com sucesso.',

    'completion_miles' => 'Quilometragem Final',
    'completion_kilometers' => 'Quilómetros Finais',
    'miles' => 'milhas',
    'kilometers' => 'quilómetros',
    'enter_miles' => 'Inserir quilometragem final',
    'enter_kilometers' => 'Inserir quilómetros finais',
    'completion_miles_help' => 'Inserir a leitura final da quilometragem quando o carro foi devolvido.',
    'completion_kilometers_help' => 'Inserir a leitura final dos quilómetros quando o carro foi devolvido.',

    'completion_gas_level' => 'Nível de Combustível',
    'select_gas_level' => 'Selecionar nível de combustível',
    'gas_empty' => 'Vazio',
    'gas_quarter' => '1/4 do Depósito',
    'gas_half' => '1/2 do Depósito',
    'gas_three_quarters' => '3/4 do Depósito',
    'gas_full' => 'Depósito Cheio',
    'completion_gas_level_help' => 'Selecionar o nível de combustível quando o carro foi devolvido.',

    'damage_images' => 'Imagens de Danos',
    'damage_image' => 'Imagem de Dano',
    'damage_images_help' => 'Carregar imagens de qualquer dano encontrado no veículo (máx. 5MB por imagem).',
    'existing_images' => 'Imagens existentes',

    'completion_notes' => 'Notas de Conclusão',
    'completion_notes_placeholder' => 'Inserir notas sobre o estado do veículo, danos ou outras observações...',
    'completion_notes_help' => 'Adicionar notas adicionais sobre o estado do veículo ou conclusão do aluguer.',

    'completed_at' => 'Concluído Em',

    // Coupon fields
    'coupon_code' => 'Código do Cupão',
    'coupon_amount' => 'Montante de Desconto do Cupão',
    'enter_coupon_code' => 'Inserir código do cupão',
    'coupon_discount_amount' => 'Montante do Desconto',
    'applied_coupon' => 'Cupão Aplicado',

    // Validation messages
    'validation' => [
        'completion_miles_integer' => 'A quilometragem deve ser um número válido.',
        'completion_miles_min' => 'A quilometragem deve ser pelo menos 0.',
        'completion_gas_level_invalid' => 'Por favor, selecione um nível de combustível válido.',
        'damage_image_invalid' => 'O ficheiro carregado deve ser uma imagem válida.',
        'damage_image_max_size' => 'O tamanho da imagem não deve exceder 5MB.',
        'completion_notes_max' => 'As notas não devem exceder 10.000 caracteres.',
    ],

    // Vendor booking actions
    'approve_booking' => 'Aprovar reserva',
    'cancel_booking' => 'Cancelar reserva',
    'processing' => 'Processando...',
    'pending_approval_notice' => 'Aguardando aprovação',
    'pending_approval_description' => 'Esta reserva está aguardando sua aprovação. Por favor, revise os detalhes e aprove ou cancele a reserva.',
    'approve_booking_confirmation' => 'Tem certeza de que deseja aprovar esta reserva? O cliente será notificado.',
    'cancel_booking_confirmation' => 'Tem certeza de que deseja cancelar esta reserva? Esta ação não pode ser desfeita.',
    'booking_approved_successfully' => 'A reserva foi aprovada com sucesso.',
    'booking_cancelled_successfully' => 'A reserva foi cancelada com sucesso.',
    'cannot_approve_booking' => 'Esta reserva não pode ser aprovada. Apenas reservas pendentes podem ser aprovadas.',
    'cannot_cancel_booking' => 'Esta reserva não pode ser cancelada. Reservas concluídas ou já canceladas não podem ser canceladas.',
];

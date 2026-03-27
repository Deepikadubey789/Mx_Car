<?php

return [
    'name' => 'Reservas',
    'create' => 'Nova reserva',
    'reports' => 'Relatórios de Reservas',
    'calendar' => 'Calendário de Reservas',
    'statuses' => [
        'pending' => 'Pendente',
        'processing' => 'Processando',
        'completed' => 'Concluído',
        'cancelled' => 'Cancelado',
    ],
    'customer' => 'Cliente',
    'amount' => 'Valor',
    'rental_period' => 'Período de Aluguel',
    'payment_method' => 'Método de Pagamento',
    'payment_status' => 'Status do Pagamento',
    'booking_information' => 'Informações da Reserva',
    'booking_period' => 'Período da Reserva',
    'payment_status_label' => 'Status do Pagamento',
    'car' => 'Carro',
    'calendar_item_title' => ':car (:customer)',

    // Dates
    'start_date' => 'Data de Início',
    'end_date' => 'Data de Término',

    // Car search
    'search_cars' => 'Pesquisar Carros',
    'selected_car' => 'Carro Selecionado',
    'please_select_dates' => 'Por favor, selecione as datas de início e término',
    'please_select_car' => 'Por favor, selecione um carro para continuar com a reserva',

    // Booking details
    'booking_details' => 'Detalhes da Reserva',

    // Customer information
    'search_customer' => 'Pesquisar cliente por nome, e-mail ou telefone...',
    'create_new_customer' => 'Criar novo cliente',
    'customer_created_successfully' => 'Cliente criado com sucesso',
    'customer_not_found' => 'Cliente não encontrado',
    'customer_information' => 'Informações do Cliente',
    'customer_name' => 'Nome',
    'email' => 'E-mail',
    'phone' => 'Telefone',
    'customer_age' => 'Idade',
    'address' => 'Endereço',
    'city' => 'Cidade',
    'state' => 'Estado/Província',
    'country' => 'País',
    'zip' => 'CEP/Código Postal',
    'note' => 'Observação',
    'note_placeholder' => 'Digite quaisquer solicitações especiais ou observações',

    // Services
    'services' => 'Serviços Adicionais',
    'day' => 'dia',

    // Payment
    'payment_status' => 'Status do pagamento',
    'transaction_id' => 'ID da Transação',
    'transaction_id_helper' => 'Você pode deixar este campo vazio se o método de pagamento for Pagamento na Entrega ou Transferência Bancária',
    'payment_method_helper' => 'Selecione o método de pagamento usado para esta reserva',
    'payment_status_helper' => 'Status atual do pagamento',

    // Form placeholders
    'first_name_placeholder' => 'Digite o primeiro nome',
    'last_name_placeholder' => 'Digite o sobrenome',
    'email_placeholder' => 'Digite o endereço de e-mail',
    'phone_placeholder' => 'Digite o número de telefone',
    'address_placeholder' => 'Digite o endereço',
    'city_placeholder' => 'Digite a cidade',
    'state_placeholder' => 'Digite o estado/província',
    'country_placeholder' => 'Digite o país',
    'zip_placeholder' => 'Digite o CEP/Código Postal',

    // Misc
    'no_customers_found' => 'Nenhum cliente encontrado',
    'no_cars_available' => 'Nenhum carro disponível para as datas selecionadas',
    'select_car' => 'Selecionar Carro',
    'print_booking_info' => 'Imprimir Informações da Reserva',
    'printed_on' => 'Impresso em',
    'computer_generated_document' => 'Este é um documento gerado por computador e não requer assinatura.',
    'booking_summary' => 'Resumo da Reserva',
    'booking_details' => 'Detalhes da Reserva',
    'additional_services' => 'Serviços Adicionais',
    'rental_period' => 'Período de Aluguel',
    'to' => 'a',

    // Completion details
    'completion_details' => 'Detalhes de Conclusão',
    'add_completion_details' => 'Adicionar Detalhes de Conclusão',
    'edit_completion_details' => 'Editar Detalhes de Conclusão',
    'no_completion_details' => 'Nenhum detalhe de conclusão foi adicionado ainda.',
    'completion_details_updated_successfully' => 'Detalhes de conclusão atualizados com sucesso.',

    'completion_miles' => 'Quilometragem Final',
    'completion_kilometers' => 'Quilômetros Finais',
    'miles' => 'milhas',
    'kilometers' => 'quilômetros',
    'enter_miles' => 'Digite a quilometragem final',
    'enter_kilometers' => 'Digite os quilômetros finais',
    'completion_miles_help' => 'Digite a leitura final da quilometragem quando o carro foi devolvido.',
    'completion_kilometers_help' => 'Digite a leitura final dos quilômetros quando o carro foi devolvido.',

    'completion_gas_level' => 'Nível de Combustível',
    'select_gas_level' => 'Selecione o nível de combustível',
    'gas_empty' => 'Vazio',
    'gas_quarter' => '1/4 do Tanque',
    'gas_half' => '1/2 do Tanque',
    'gas_three_quarters' => '3/4 do Tanque',
    'gas_full' => 'Tanque Cheio',
    'completion_gas_level_help' => 'Selecione o nível de combustível quando o carro foi devolvido.',

    'damage_images' => 'Imagens de Danos',
    'damage_image' => 'Imagem de Dano',
    'damage_images_help' => 'Faça upload de imagens de quaisquer danos encontrados no veículo (máximo 5MB por imagem).',
    'existing_images' => 'Imagens existentes',

    'completion_notes' => 'Observações de Conclusão',
    'completion_notes_placeholder' => 'Digite quaisquer observações sobre a condição do veículo, danos ou outras observações...',
    'completion_notes_help' => 'Adicione quaisquer observações adicionais sobre a condição do veículo ou conclusão do aluguel.',

    'completed_at' => 'Concluído Em',

    // Coupon fields
    'coupon_code' => 'Código do Cupom',
    'coupon_amount' => 'Valor de Desconto do Cupom',
    'enter_coupon_code' => 'Digite o código do cupom',
    'coupon_discount_amount' => 'Valor de Desconto',
    'applied_coupon' => 'Cupom Aplicado',

    // Validation messages
    'validation' => [
        'completion_miles_integer' => 'A quilometragem deve ser um número válido.',
        'completion_miles_min' => 'A quilometragem deve ser no mínimo 0.',
        'completion_gas_level_invalid' => 'Por favor, selecione um nível de combustível válido.',
        'damage_image_invalid' => 'O arquivo enviado deve ser uma imagem válida.',
        'damage_image_max_size' => 'O tamanho da imagem não deve exceder 5MB.',
        'completion_notes_max' => 'As observações não devem exceder 10.000 caracteres.',
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

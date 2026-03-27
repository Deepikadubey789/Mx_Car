<?php

return [
    'name' => 'Бронирования',
    'create' => 'Новое бронирование',
    'reports' => 'Отчеты по Бронированиям',
    'calendar' => 'Календарь Бронирований',
    'statuses' => [
        'pending' => 'В ожидании',
        'processing' => 'Обработка',
        'completed' => 'Завершено',
        'cancelled' => 'Отменено',
    ],
    'customer' => 'Клиент',
    'amount' => 'Сумма',
    'rental_period' => 'Период Аренды',
    'payment_method' => 'Способ Оплаты',
    'payment_status' => 'Статус Оплаты',
    'booking_information' => 'Информация о Бронировании',
    'booking_period' => 'Период Бронирования',
    'payment_status_label' => 'Статус Оплаты',
    'car' => 'Автомобиль',
    'calendar_item_title' => ':car (:customer)',

    // Dates
    'start_date' => 'Дата Начала',
    'end_date' => 'Дата Окончания',

    // Car search
    'search_cars' => 'Поиск Автомобилей',
    'selected_car' => 'Выбранный Автомобиль',
    'please_select_dates' => 'Пожалуйста, выберите дату начала и окончания',
    'please_select_car' => 'Пожалуйста, выберите автомобиль, чтобы продолжить бронирование',

    // Booking details
    'booking_details' => 'Детали Бронирования',

    // Customer information
    'search_customer' => 'Поиск клиента по имени, электронной почте или телефону...',
    'create_new_customer' => 'Создать нового клиента',
    'customer_created_successfully' => 'Клиент успешно создан',
    'customer_not_found' => 'Клиент не найден',
    'customer_information' => 'Информация о Клиенте',
    'customer_name' => 'Имя',
    'email' => 'Электронная почта',
    'phone' => 'Телефон',
    'customer_age' => 'Возраст',
    'address' => 'Адрес',
    'city' => 'Город',
    'state' => 'Регион/Область',
    'country' => 'Страна',
    'zip' => 'Почтовый Индекс',
    'note' => 'Примечание',
    'note_placeholder' => 'Введите любые специальные пожелания или примечания',

    // Services
    'services' => 'Дополнительные Услуги',
    'day' => 'день',

    // Payment
    'payment_status' => 'Статус оплаты',
    'transaction_id' => 'ID Транзакции',
    'transaction_id_helper' => 'Вы можете оставить это поле пустым, если способ оплаты COD или банковский перевод',
    'payment_method_helper' => 'Выберите способ оплаты, используемый для этого бронирования',
    'payment_status_helper' => 'Текущий статус оплаты',

    // Form placeholders
    'first_name_placeholder' => 'Введите имя',
    'last_name_placeholder' => 'Введите фамилию',
    'email_placeholder' => 'Введите адрес электронной почты',
    'phone_placeholder' => 'Введите номер телефона',
    'address_placeholder' => 'Введите адрес',
    'city_placeholder' => 'Введите город',
    'state_placeholder' => 'Введите регион/область',
    'country_placeholder' => 'Введите страну',
    'zip_placeholder' => 'Введите почтовый индекс',

    // Misc
    'no_customers_found' => 'Клиенты не найдены',
    'no_cars_available' => 'Нет доступных автомобилей на выбранные даты',
    'select_car' => 'Выбрать Автомобиль',
    'print_booking_info' => 'Печать Информации о Бронировании',
    'printed_on' => 'Напечатано',
    'computer_generated_document' => 'Это компьютерный документ и не требует подписи.',
    'booking_summary' => 'Сводка Бронирования',
    'booking_details' => 'Детали Бронирования',
    'additional_services' => 'Дополнительные Услуги',
    'rental_period' => 'Период Аренды',
    'to' => 'до',

    // Completion details
    'completion_details' => 'Детали Завершения',
    'add_completion_details' => 'Добавить Детали Завершения',
    'edit_completion_details' => 'Редактировать Детали Завершения',
    'no_completion_details' => 'Детали завершения еще не добавлены.',
    'completion_details_updated_successfully' => 'Детали завершения успешно обновлены.',

    'completion_miles' => 'Окончательный Пробег',
    'completion_kilometers' => 'Окончательные Километры',
    'miles' => 'миль',
    'kilometers' => 'километров',
    'enter_miles' => 'Введите окончательный пробег',
    'enter_kilometers' => 'Введите окончательные километры',
    'completion_miles_help' => 'Введите окончательные показания пробега на момент возврата автомобиля.',
    'completion_kilometers_help' => 'Введите окончательные показания километража на момент возврата автомобиля.',

    'completion_gas_level' => 'Уровень Топлива',
    'select_gas_level' => 'Выберите уровень топлива',
    'gas_empty' => 'Пусто',
    'gas_quarter' => '1/4 Бака',
    'gas_half' => '1/2 Бака',
    'gas_three_quarters' => '3/4 Бака',
    'gas_full' => 'Полный Бак',
    'completion_gas_level_help' => 'Выберите уровень топлива на момент возврата автомобиля.',

    'damage_images' => 'Фотографии Повреждений',
    'damage_image' => 'Фото Повреждения',
    'damage_images_help' => 'Загрузите фотографии любых повреждений, обнаруженных на автомобиле (макс. 5 МБ на фото).',
    'existing_images' => 'Существующие фотографии',

    'completion_notes' => 'Примечания о Завершении',
    'completion_notes_placeholder' => 'Введите любые примечания о состоянии автомобиля, повреждениях или других наблюдениях...',
    'completion_notes_help' => 'Добавьте любые дополнительные примечания о состоянии автомобиля или завершении аренды.',

    'completed_at' => 'Завершено В',

    // Coupon fields
    'coupon_code' => 'Код Купона',
    'coupon_amount' => 'Сумма Скидки Купона',
    'enter_coupon_code' => 'Введите код купона',
    'coupon_discount_amount' => 'Сумма Скидки',
    'applied_coupon' => 'Примененный Купон',

    // Validation messages
    'validation' => [
        'completion_miles_integer' => 'Пробег должен быть действительным числом.',
        'completion_miles_min' => 'Пробег должен быть не менее 0.',
        'completion_gas_level_invalid' => 'Пожалуйста, выберите действительный уровень топлива.',
        'damage_image_invalid' => 'Загруженный файл должен быть действительным изображением.',
        'damage_image_max_size' => 'Размер изображения не должен превышать 5 МБ.',
        'completion_notes_max' => 'Примечания не должны превышать 10 000 символов.',
    ],

    // Vendor booking actions
    'approve_booking' => 'Утвердить бронирование',
    'cancel_booking' => 'Отменить бронирование',
    'processing' => 'Обработка...',
    'pending_approval_notice' => 'Ожидает подтверждения',
    'pending_approval_description' => 'Это бронирование ожидает вашего подтверждения. Пожалуйста, проверьте детали и подтвердите или отмените бронирование.',
    'approve_booking_confirmation' => 'Вы уверены, что хотите подтвердить это бронирование? Клиент будет уведомлен.',
    'cancel_booking_confirmation' => 'Вы уверены, что хотите отменить это бронирование? Это действие нельзя отменить.',
    'booking_approved_successfully' => 'Бронирование успешно подтверждено.',
    'booking_cancelled_successfully' => 'Бронирование успешно отменено.',
    'cannot_approve_booking' => 'Это бронирование не может быть подтверждено. Подтвердить можно только ожидающие бронирования.',
    'cannot_cancel_booking' => 'Это бронирование не может быть отменено. Завершенные или уже отмененные бронирования не могут быть отменены.',
];

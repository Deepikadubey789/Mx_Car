<?php

return [
    'name' => 'Бронювання',
    'create' => 'Нове бронювання',
    'reports' => 'Звіти про бронювання',
    'calendar' => 'Календар бронювання',
    'statuses' => [
        'pending' => 'Очікує',
        'processing' => 'Обробляється',
        'completed' => 'Завершено',
        'cancelled' => 'Скасовано',
    ],
    'customer' => 'Клієнт',
    'amount' => 'Сума',
    'rental_period' => 'Період оренди',
    'payment_method' => 'Спосіб оплати',
    'payment_status' => 'Статус платежу',
    'booking_information' => 'Інформація про бронювання',
    'booking_period' => 'Період бронювання',
    'payment_status_label' => 'Статус платежу',
    'car' => 'Автомобіль',
    'calendar_item_title' => ':car (:customer)',

    // Dates
    'start_date' => 'Дата початку',
    'end_date' => 'Дата закінчення',

    // Car search
    'search_cars' => 'Пошук автомобілів',
    'selected_car' => 'Вибраний автомобіль',
    'please_select_dates' => 'Будь ласка, оберіть дати початку та закінчення',
    'please_select_car' => 'Будь ласка, оберіть автомобіль для продовження бронювання',

    // Booking details
    'booking_details' => 'Деталі бронювання',

    // Customer information
    'search_customer' => 'Пошук клієнта за ім\'ям, електронною поштою або телефоном...',
    'create_new_customer' => 'Створити нового клієнта',
    'customer_created_successfully' => 'Клієнта успішно створено',
    'customer_not_found' => 'Клієнта не знайдено',
    'customer_information' => 'Інформація про клієнта',
    'customer_name' => 'Ім\'я',
    'email' => 'Електронна пошта',
    'phone' => 'Телефон',
    'customer_age' => 'Вік',
    'address' => 'Адреса',
    'city' => 'Місто',
    'state' => 'Область/Регіон',
    'country' => 'Країна',
    'zip' => 'Поштовий індекс',
    'note' => 'Примітка',
    'note_placeholder' => 'Введіть особливі вимоги або примітки',

    // Services
    'services' => 'Додаткові послуги',
    'day' => 'день',

    // Payment
    'payment_status' => 'Статус платежу',
    'transaction_id' => 'ID транзакції',
    'transaction_id_helper' => 'Ви можете залишити це поле порожнім, якщо спосіб оплати - готівка або банківський переказ',
    'payment_method_helper' => 'Оберіть спосіб оплати для цього бронювання',
    'payment_status_helper' => 'Поточний статус платежу',

    // Form placeholders
    'first_name_placeholder' => 'Введіть ім\'я',
    'last_name_placeholder' => 'Введіть прізвище',
    'email_placeholder' => 'Введіть адресу електронної пошти',
    'phone_placeholder' => 'Введіть номер телефону',
    'address_placeholder' => 'Введіть адресу',
    'city_placeholder' => 'Введіть місто',
    'state_placeholder' => 'Введіть область/регіон',
    'country_placeholder' => 'Введіть країну',
    'zip_placeholder' => 'Введіть поштовий індекс',

    // Misc
    'no_customers_found' => 'Клієнтів не знайдено',
    'no_cars_available' => 'Немає доступних автомобілів на вибрані дати',
    'select_car' => 'Оберіть автомобіль',
    'print_booking_info' => 'Друкувати інформацію про бронювання',
    'printed_on' => 'Надруковано',
    'computer_generated_document' => 'Це документ, згенерований комп\'ютером, і не вимагає підпису.',
    'booking_summary' => 'Підсумок бронювання',
    'booking_details' => 'Деталі бронювання',
    'additional_services' => 'Додаткові послуги',
    'rental_period' => 'Період оренди',
    'to' => 'до',

    // Completion details
    'completion_details' => 'Деталі завершення',
    'add_completion_details' => 'Додати деталі завершення',
    'edit_completion_details' => 'Редагувати деталі завершення',
    'no_completion_details' => 'Деталі завершення ще не додано.',
    'completion_details_updated_successfully' => 'Деталі завершення успішно оновлено.',

    'completion_miles' => 'Фінальний пробіг',
    'completion_kilometers' => 'Фінальні кілометри',
    'miles' => 'милі',
    'kilometers' => 'кілометри',
    'enter_miles' => 'Введіть фінальний пробіг',
    'enter_kilometers' => 'Введіть фінальні кілометри',
    'completion_miles_help' => 'Введіть показання фінального пробігу при поверненні автомобіля.',
    'completion_kilometers_help' => 'Введіть показання фінальних кілометрів при поверненні автомобіля.',

    'completion_gas_level' => 'Рівень пального',
    'select_gas_level' => 'Оберіть рівень пального',
    'gas_empty' => 'Порожній',
    'gas_quarter' => '1/4 бака',
    'gas_half' => '1/2 бака',
    'gas_three_quarters' => '3/4 бака',
    'gas_full' => 'Повний бак',
    'completion_gas_level_help' => 'Оберіть рівень пального при поверненні автомобіля.',

    'damage_images' => 'Зображення пошкоджень',
    'damage_image' => 'Зображення пошкодження',
    'damage_images_help' => 'Завантажте зображення будь-яких пошкоджень, виявлених на транспортному засобі (максимум 5МБ на зображення).',
    'existing_images' => 'Існуючі зображення',

    'completion_notes' => 'Примітки про завершення',
    'completion_notes_placeholder' => 'Введіть будь-які примітки про стан транспортного засобу, пошкодження або інші спостереження...',
    'completion_notes_help' => 'Додайте будь-які додаткові примітки про стан транспортного засобу або завершення оренди.',

    'completed_at' => 'Завершено',

    // Coupon fields
    'coupon_code' => 'Код купона',
    'coupon_amount' => 'Сума знижки купона',
    'enter_coupon_code' => 'Введіть код купона',
    'coupon_discount_amount' => 'Сума знижки',
    'applied_coupon' => 'Застосований купон',

    // Validation messages
    'validation' => [
        'completion_miles_integer' => 'Пробіг повинен бути правильним числом.',
        'completion_miles_min' => 'Пробіг повинен бути принаймні 0.',
        'completion_gas_level_invalid' => 'Будь ласка, оберіть правильний рівень пального.',
        'damage_image_invalid' => 'Завантажений файл повинен бути правильним зображенням.',
        'damage_image_max_size' => 'Розмір зображення не повинен перевищувати 5МБ.',
        'completion_notes_max' => 'Примітки не повинні перевищувати 10,000 символів.',
    ],

    // Vendor booking actions
    'approve_booking' => 'Затвердити бронювання',
    'cancel_booking' => 'Скасувати бронювання',
    'processing' => 'Обробка...',
    'pending_approval_notice' => 'Очікує підтвердження',
    'pending_approval_description' => 'Це бронювання очікує вашого підтвердження. Будь ласка, перевірте деталі та підтвердіть або скасуйте бронювання.',
    'approve_booking_confirmation' => 'Ви впевнені, що хочете підтвердити це бронювання? Клієнт буде повідомлений.',
    'cancel_booking_confirmation' => 'Ви впевнені, що хочете скасувати це бронювання? Цю дію неможливо скасувати.',
    'booking_approved_successfully' => 'Бронювання успішно підтверджено.',
    'booking_cancelled_successfully' => 'Бронювання успішно скасовано.',
    'cannot_approve_booking' => 'Це бронювання не може бути підтверджено. Підтвердити можна лише бронювання, що очікують.',
    'cannot_cancel_booking' => 'Це бронювання не може бути скасовано. Завершені або вже скасовані бронювання не можуть бути скасовані.',
];

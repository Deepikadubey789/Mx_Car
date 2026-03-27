<?php

return [
    'name' => 'الحجوزات',
    'create' => 'حجز جديد',
    'reports' => 'تقارير الحجوزات',
    'calendar' => 'تقويم الحجوزات',
    'statuses' => [
        'pending' => 'قيد الانتظار',
        'processing' => 'قيد المعالجة',
        'completed' => 'مكتمل',
        'cancelled' => 'ملغى',
    ],
    'customer' => 'العميل',
    'amount' => 'المبلغ',
    'rental_period' => 'فترة الإيجار',
    'payment_method' => 'طريقة الدفع',
    'payment_status' => 'حالة الدفع',
    'booking_information' => 'معلومات الحجز',
    'booking_period' => 'فترة الحجز',
    'payment_status_label' => 'حالة الدفع',
    'car' => 'السيارة',
    'calendar_item_title' => ':car (العميل: :customer)',

    // Dates
    'start_date' => 'تاريخ البداية',
    'end_date' => 'تاريخ النهاية',

    // Car search
    'search_cars' => 'البحث عن سيارات',
    'selected_car' => 'السيارة المحددة',
    'please_select_dates' => 'يرجى تحديد تاريخي البداية والنهاية',
    'please_select_car' => 'يرجى تحديد سيارة لمتابعة الحجز',

    // Booking details
    'booking_details' => 'تفاصيل الحجز',

    // Customer information
    'search_customer' => 'البحث عن عميل بالاسم أو البريد الإلكتروني أو الهاتف...',
    'create_new_customer' => 'إنشاء عميل جديد',
    'customer_created_successfully' => 'تم إنشاء العميل بنجاح',
    'customer_not_found' => 'العميل غير موجود',
    'customer_information' => 'معلومات العميل',
    'customer_name' => 'الاسم',
    'email' => 'البريد الإلكتروني',
    'phone' => 'الهاتف',
    'customer_age' => 'العمر',
    'address' => 'العنوان',
    'city' => 'المدينة',
    'state' => 'الولاية/المقاطعة',
    'country' => 'البلد',
    'zip' => 'الرمز البريدي',
    'note' => 'ملاحظة',
    'note_placeholder' => 'أدخل أي طلبات خاصة أو ملاحظات',

    // Services
    'services' => 'الخدمات الإضافية',
    'day' => 'يوم',

    // Payment
    'payment_status' => 'حالة الدفع',
    'transaction_id' => 'معرف المعاملة',
    'transaction_id_helper' => 'يمكنك ترك هذا الحقل فارغاً إذا كانت طريقة الدفع نقداً عند التسليم أو تحويل بنكي',
    'payment_method_helper' => 'اختر طريقة الدفع المستخدمة لهذا الحجز',
    'payment_status_helper' => 'الحالة الحالية للدفع',

    // Form placeholders
    'first_name_placeholder' => 'أدخل الاسم الأول',
    'last_name_placeholder' => 'أدخل اسم العائلة',
    'email_placeholder' => 'أدخل البريد الإلكتروني',
    'phone_placeholder' => 'أدخل رقم الهاتف',
    'address_placeholder' => 'أدخل العنوان',
    'city_placeholder' => 'أدخل المدينة',
    'state_placeholder' => 'أدخل الولاية/المقاطعة',
    'country_placeholder' => 'أدخل البلد',
    'zip_placeholder' => 'أدخل الرمز البريدي',

    // Misc
    'no_customers_found' => 'لم يتم العثور على عملاء',
    'no_cars_available' => 'لا توجد سيارات متاحة للتواريخ المحددة',
    'select_car' => 'اختر سيارة',
    'print_booking_info' => 'طباعة معلومات الحجز',
    'printed_on' => 'تم الطباعة في',
    'computer_generated_document' => 'هذا مستند مُنشأ بواسطة الكمبيوتر ولا يتطلب توقيعاً.',
    'booking_summary' => 'ملخص الحجز',
    'booking_details' => 'تفاصيل الحجز',
    'additional_services' => 'الخدمات الإضافية',
    'rental_period' => 'فترة الإيجار',
    'to' => 'إلى',

    // Completion details
    'completion_details' => 'تفاصيل الإنجاز',
    'add_completion_details' => 'إضافة تفاصيل الإنجاز',
    'edit_completion_details' => 'تعديل تفاصيل الإنجاز',
    'no_completion_details' => 'لم تتم إضافة تفاصيل الإنجاز بعد.',
    'completion_details_updated_successfully' => 'تم تحديث تفاصيل الإنجاز بنجاح.',

    'completion_miles' => 'الأميال النهائية',
    'completion_kilometers' => 'الكيلومترات النهائية',
    'miles' => 'أميال',
    'kilometers' => 'كيلومترات',
    'enter_miles' => 'أدخل الأميال النهائية',
    'enter_kilometers' => 'أدخل الكيلومترات النهائية',
    'completion_miles_help' => 'أدخل قراءة الأميال النهائية عند إرجاع السيارة.',
    'completion_kilometers_help' => 'أدخل قراءة الكيلومترات النهائية عند إرجاع السيارة.',

    'completion_gas_level' => 'مستوى الوقود',
    'select_gas_level' => 'اختر مستوى الوقود',
    'gas_empty' => 'فارغ',
    'gas_quarter' => '1/4 خزان',
    'gas_half' => '1/2 خزان',
    'gas_three_quarters' => '3/4 خزان',
    'gas_full' => 'خزان ممتلئ',
    'completion_gas_level_help' => 'اختر مستوى الوقود عند إرجاع السيارة.',

    'damage_images' => 'صور الأضرار',
    'damage_image' => 'صورة الضرر',
    'damage_images_help' => 'قم بتحميل صور أي ضرر موجود على المركبة (بحد أقصى 5 ميجابايت لكل صورة).',
    'existing_images' => 'الصور الموجودة',

    'completion_notes' => 'ملاحظات الإنجاز',
    'completion_notes_placeholder' => 'أدخل أي ملاحظات حول حالة المركبة أو الأضرار أو ملاحظات أخرى...',
    'completion_notes_help' => 'أضف أي ملاحظات إضافية حول حالة المركبة أو إكمال التأجير.',

    'completed_at' => 'تم الإكمال في',

    // Coupon fields
    'coupon_code' => 'كود القسيمة',
    'coupon_amount' => 'مبلغ خصم القسيمة',
    'enter_coupon_code' => 'أدخل كود القسيمة',
    'coupon_discount_amount' => 'مبلغ الخصم',
    'applied_coupon' => 'القسيمة المطبقة',

    // Validation messages
    'validation' => [
        'completion_miles_integer' => 'يجب أن تكون الأميال رقماً صحيحاً.',
        'completion_miles_min' => 'يجب أن تكون الأميال 0 على الأقل.',
        'completion_gas_level_invalid' => 'يرجى اختيار مستوى وقود صالح.',
        'damage_image_invalid' => 'يجب أن يكون الملف المرفوع صورة صالحة.',
        'damage_image_max_size' => 'يجب ألا يتجاوز حجم الصورة 5 ميجابايت.',
        'completion_notes_max' => 'يجب ألا تتجاوز الملاحظات 10,000 حرف.',
    ],

    // Vendor booking actions
    'approve_booking' => 'قبول الحجز',
    'cancel_booking' => 'إلغاء الحجز',
    'processing' => 'جاري المعالجة...',
    'pending_approval_notice' => 'بانتظار القبول',
    'pending_approval_description' => 'هذا الحجز بانتظار موافقتك. يرجى مراجعة التفاصيل والموافقة على الحجز أو إلغائه.',
    'approve_booking_confirmation' => 'هل أنت متأكد من الموافقة على هذا الحجز؟ سيتم إخطار العميل.',
    'cancel_booking_confirmation' => 'هل أنت متأكد من إلغاء هذا الحجز؟ لا يمكن التراجع عن هذا الإجراء.',
    'booking_approved_successfully' => 'تمت الموافقة على الحجز بنجاح.',
    'booking_cancelled_successfully' => 'تم إلغاء الحجز بنجاح.',
    'cannot_approve_booking' => 'لا يمكن قبول هذا الحجز. يمكن فقط قبول الحجوزات المعلقة.',
    'cannot_cancel_booking' => 'لا يمكن إلغاء هذا الحجز. لا يمكن إلغاء الحجوزات المكتملة أو الملغاة بالفعل.',
];

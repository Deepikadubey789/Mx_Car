<?php

return [
    'name' => '預約',
    'create' => '新預約',
    'reports' => '預約報告',
    'calendar' => '預約日曆',
    'statuses' => [
        'pending' => '待處理',
        'processing' => '處理中',
        'completed' => '已完成',
        'cancelled' => '已取消',
    ],
    'customer' => '客戶',
    'amount' => '金金額',
    'rental_period' => '租賃期間',
    'payment_method' => '支付方式',
    'payment_status' => '支付狀態',
    'booking_information' => '預約信息',
    'booking_period' => '預約期間',
    'payment_status_label' => '支付狀態',
    'car' => '車輛',
    'calendar_item_title' => ':car（:customer）',

    // Dates
    'start_date' => '開始日期',
    'end_date' => '結束日期',

    // Car search
    'search_cars' => '車輛搜索',
    'selected_car' => '選擇車輛',
    'please_select_dates' => '開始日期結束日期兩個選擇請',
    'please_select_car' => '預約繼續車輛選擇請',

    // Booking details
    'booking_details' => '預約詳情',

    // Customer information
    'search_customer' => '名字稱、電子郵件、電話號碼客戶搜索...',
    'create_new_customer' => '新客戶創建',
    'customer_created_successfully' => '客戶成功創建了',
    'customer_not_found' => '客戶找',
    'customer_information' => '客戶信息',
    'customer_name' => '名字稱',
    'email' => '電子郵件',
    'phone' => '電話',
    'customer_age' => '年齡',
    'address' => '地址',
    'city' => '城市',
    'state' => '州/省',
    'country' => '國家',
    'zip' => '郵政號碼',
    'note' => '備註',
    'note_placeholder' => '特殊要求備註輸入',

    // Services
    'services' => '添加服務',
    'day' => '日',

    // Payment
    'payment_status' => '支付狀態',
    'transaction_id' => '交易ID',
    'transaction_id_helper' => '支付方式COD銀行振込情況、此字段空欄',
    'payment_method_helper' => '此預約使用支付方式選擇',
    'payment_status_helper' => '支付現在狀態',

    // Form placeholders
    'first_name_placeholder' => '名字輸入',
    'last_name_placeholder' => '姓氏輸入',
    'email_placeholder' => '電子郵件地址輸入',
    'phone_placeholder' => '電話號碼輸入',
    'address_placeholder' => '地址輸入',
    'city_placeholder' => '城市輸入',
    'state_placeholder' => '州/省輸入',
    'country_placeholder' => '國家輸入',
    'zip_placeholder' => '郵政號碼輸入',

    // Misc
    'no_customers_found' => '客戶找',
    'no_cars_available' => '選擇日期可用車輛',
    'select_car' => '車輛選擇',
    'print_booking_info' => '預約信息打印',
    'printed_on' => '打印日',
    'computer_generated_document' => '此計算機生成文檔、簽名字不需要。',
    'booking_summary' => '預約概要',
    'booking_details' => '預約詳情',
    'additional_services' => '添加服務',
    'rental_period' => '租賃期間',
    'to' => '',

    // Completion details
    'completion_details' => '已完成詳情',
    'add_completion_details' => '已完成詳情添加',
    'edit_completion_details' => '已完成詳情編輯',
    'no_completion_details' => '已完成詳情尚未添加尚未。',
    'completion_details_updated_successfully' => '已完成詳情成功更新了。',

    'completion_miles' => '最終里程',
    'completion_kilometers' => '最終公里',
    'miles' => '英里',
    'kilometers' => '公里',
    'enter_miles' => '最終里程輸入',
    'enter_kilometers' => '最終公里輸入',
    'completion_miles_help' => '車輛還車時最終里程輸入請。',
    'completion_kilometers_help' => '車輛還車時最終公里輸入請。',

    'completion_gas_level' => '油量',
    'select_gas_level' => '油量選擇',
    'gas_empty' => '空',
    'gas_quarter' => '1/4油箱',
    'gas_half' => '1/2油箱',
    'gas_three_quarters' => '3/4油箱',
    'gas_full' => '滿油',
    'completion_gas_level_help' => '車輛還車時油量選擇請。',

    'damage_images' => '損壞圖片',
    'damage_image' => '損壞圖片',
    'damage_images_help' => '車輛找損壞圖片上傳（圖片1枚最大5MB）。',
    'existing_images' => '現有圖片',

    'completion_notes' => '已完成備註',
    'completion_notes_placeholder' => '車輛狀態、損壞、他觀察事項關於備註輸入...',
    'completion_notes_help' => '車輛狀態租賃已完成關於添加備註添加請。',

    'completed_at' => '已完成日期時間',

    // Coupon fields
    'coupon_code' => '優惠券代碼',
    'coupon_amount' => '優惠券折扣金額',
    'enter_coupon_code' => '優惠券代碼輸入',
    'coupon_discount_amount' => '折扣金額',
    'applied_coupon' => '應用優惠券',

    // Validation messages
    'validation' => [
        'completion_miles_integer' => '里程有效數值必須。',
        'completion_miles_min' => '里程0以上必須。',
        'completion_gas_level_invalid' => '有效油量選擇請。',
        'damage_image_invalid' => '上傳文件有效圖片必須。',
        'damage_image_max_size' => '圖片大小5MB超過。',
        'completion_notes_max' => '備註10,000字符超過。',
    ],

    // Vendor booking actions
    'approve_booking' => '批准預訂',
    'cancel_booking' => '取消預訂',
    'processing' => '處理中...',
    'pending_approval_notice' => '等待批准',
    'pending_approval_description' => '此預訂正在等待您的批准。請查看詳細信息並批准或取消預訂。',
    'approve_booking_confirmation' => '您確定要批准此預訂嗎？客戶將收到通知。',
    'cancel_booking_confirmation' => '您確定要取消此預訂嗎？此操作無法撤消。',
    'booking_approved_successfully' => '預訂已成功批准。',
    'booking_cancelled_successfully' => '預訂已成功取消。',
    'cannot_approve_booking' => '無法批准此預訂。只能批准待處理的預訂。',
    'cannot_cancel_booking' => '無法取消此預訂。已完成或已取消的預訂無法取消。',
];

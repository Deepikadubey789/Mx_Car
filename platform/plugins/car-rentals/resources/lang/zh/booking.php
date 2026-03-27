<?php

return [
    'name' => '预约',
    'create' => '新预约',
    'reports' => '预约报告',
    'calendar' => '预约日历',
    'statuses' => [
        'pending' => '待处理',
        'processing' => '处理中',
        'completed' => '已完成',
        'cancelled' => '已取消',
    ],
    'customer' => '客户',
    'amount' => '金额',
    'rental_period' => '租赁期间',
    'payment_method' => '支付方式',
    'payment_status' => '支付状态',
    'booking_information' => '预约信息',
    'booking_period' => '预约期间',
    'payment_status_label' => '支付状态',
    'car' => '车辆',
    'calendar_item_title' => ':car（:customer）',

    // Dates
    'start_date' => '开始日期',
    'end_date' => '结束日期',

    // Car search
    'search_cars' => '搜索车辆',
    'selected_car' => '已选择的车辆',
    'please_select_dates' => '开始日期和结束日期的两个选择请',
    'please_select_car' => '请选择车辆以继续预约',

    // Booking details
    'booking_details' => '预约详情',

    // Customer information
    'search_customer' => '按姓氏名字、电子邮件或电话号码搜索客户...',
    'create_new_customer' => '创建新客户',
    'customer_created_successfully' => '客户创建成功',
    'customer_not_found' => '未找到客户',
    'customer_information' => '客户信息',
    'customer_name' => '名字称',
    'email' => '电子邮件',
    'phone' => '电话',
    'customer_age' => '年龄',
    'address' => '地址',
    'city' => '城市',
    'state' => '州/省',
    'country' => '国家家',
    'zip' => '邮政编码',
    'note' => '备注',
    'note_placeholder' => '输入任何特殊要求或备注',

    // Services
    'services' => '添加服务',
    'day' => '天',

    // Payment
    'payment_status' => '支付状态',
    'transaction_id' => '交易ID',
    'transaction_id_helper' => '如果支付方式是货到付款或银行转账，您可以将此字段留空',
    'payment_method_helper' => '选择此预约使用的支付方式',
    'payment_status_helper' => '支付的当前状态',

    // Form placeholders
    'first_name_placeholder' => '输入名字字',
    'last_name_placeholder' => '输入姓氏氏',
    'email_placeholder' => '输入电子邮件地址',
    'phone_placeholder' => '输入电话号码',
    'address_placeholder' => '输入地址',
    'city_placeholder' => '输入城市',
    'state_placeholder' => '输入州/省',
    'country_placeholder' => '输入国家家',
    'zip_placeholder' => '输入邮政编码',

    // Misc
    'no_customers_found' => '未找到客户',
    'no_cars_available' => '所选日期没有可用车辆',
    'select_car' => '选择车辆',
    'print_booking_info' => '打印预约信息',
    'printed_on' => '打印日期',
    'computer_generated_document' => '这是计算机生成的文档，无需签名字。',
    'booking_summary' => '预约概要',
    'booking_details' => '预约详情',
    'additional_services' => '添加服务',
    'rental_period' => '租赁期间',
    'to' => '至',

    // Completion details
    'completion_details' => '完成详情',
    'add_completion_details' => '添加完成详情',
    'edit_completion_details' => '编辑完成详情',
    'no_completion_details' => '尚未添加完成详情。',
    'completion_details_updated_successfully' => '完成详情已成功更新。',

    'completion_miles' => '最终里程',
    'completion_kilometers' => '最终公里数',
    'miles' => '英里',
    'kilometers' => '公里',
    'enter_miles' => '输入最终里程',
    'enter_kilometers' => '输入最终公里数',
    'completion_miles_help' => '车辆还车时的输入最终里程请。',
    'completion_kilometers_help' => '车辆还车时的输入最终公里数请。',

    'completion_gas_level' => '油量',
    'select_gas_level' => '选择油量',
    'gas_empty' => '空',
    'gas_quarter' => '1/4 油箱',
    'gas_half' => '1/2 油箱',
    'gas_three_quarters' => '3/4 油箱',
    'gas_full' => '满油箱',
    'completion_gas_level_help' => '车辆还车时的选择油量请。',

    'damage_images' => '损坏图片',
    'damage_image' => '损坏图片',
    'damage_images_help' => '上传车辆上发现的任何损坏图片（每张图片最大5MB）。',
    'existing_images' => '现有图片',

    'completion_notes' => '完成备注',
    'completion_notes_placeholder' => '输入有关车辆状况、损坏或其他观察的任何备注...',
    'completion_notes_help' => '添加有关车辆状况或租赁完成的任何其他备注。',

    'completed_at' => '完成时间',

    // Coupon fields
    'coupon_code' => '优惠券代码',
    'coupon_amount' => '优惠券折扣金额',
    'enter_coupon_code' => '优惠券代码输入',
    'coupon_discount_amount' => '折扣金额',
    'applied_coupon' => '已应用的优惠券',

    // Validation messages
    'validation' => [
        'completion_miles_integer' => '里程必须是有效数字。',
        'completion_miles_min' => '里程必须至少为0。',
        'completion_gas_level_invalid' => '有效的选择油量请。',
        'damage_image_invalid' => '上传的文件必须是有效图片。',
        'damage_image_max_size' => '图片大小不得超过5MB。',
        'completion_notes_max' => '备注不得超过10,000个字符。',
    ],

    // Vendor booking actions
    'approve_booking' => '批准预订',
    'cancel_booking' => '取消预订',
    'processing' => '处理中...',
    'pending_approval_notice' => '等待批准',
    'pending_approval_description' => '此预订正在等待您的批准。请查看详细信息并批准或取消预订。',
    'approve_booking_confirmation' => '您确定要批准此预订吗？客户将收到通知。',
    'cancel_booking_confirmation' => '您确定要取消此预订吗？此操作无法撤消。',
    'booking_approved_successfully' => '预订已成功批准。',
    'booking_cancelled_successfully' => '预订已成功取消。',
    'cannot_approve_booking' => '无法批准此预订。只能批准待处理的预订。',
    'cannot_cancel_booking' => '无法取消此预订。已完成或已取消的预订无法取消。',
];

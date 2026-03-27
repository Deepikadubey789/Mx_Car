<?php

return [
    'name' => '예약',
    'create' => '새 예약',
    'reports' => '예약 보고서',
    'calendar' => '예약 캘린더',
    'statuses' => [
        'pending' => '대기 중',
        'processing' => '처리 중',
        'completed' => '완료',
        'cancelled' => '취소됨',
    ],
    'customer' => '고객',
    'amount' => '금액',
    'rental_period' => '대여 기간',
    'payment_method' => '결제 방법',
    'payment_status' => '결제 상태',
    'booking_information' => '예약 정보',
    'booking_period' => '예약 기간',
    'payment_status_label' => '결제 상태',
    'car' => '차량',
    'calendar_item_title' => ':car (:customer)',

    // Dates
    'start_date' => '시작일',
    'end_date' => '종료일',

    // Car search
    'search_cars' => '차량 검색',
    'selected_car' => '선택된 차량',
    'please_select_dates' => '시작일과 종료일을 모두 선택하세요',
    'please_select_car' => '예약을 계속하려면 차량을 선택하세요',

    // Booking details
    'booking_details' => '예약 세부정보',

    // Customer information
    'search_customer' => '이름, 이메일 또는 전화번호로 고객 검색...',
    'create_new_customer' => '새 고객 생성',
    'customer_created_successfully' => '고객이 성공적으로 생성되었습니다',
    'customer_not_found' => '고객을 찾을 수 없습니다',
    'customer_information' => '고객 정보',
    'customer_name' => '이름',
    'email' => '이메일',
    'phone' => '전화',
    'customer_age' => '나이',
    'address' => '주소',
    'city' => '도시',
    'state' => '시/도',
    'country' => '국가',
    'zip' => '우편번호',
    'note' => '비고',
    'note_placeholder' => '특별 요청이나 비고를 입력하세요',

    // Services
    'services' => '추가 서비스',
    'day' => '일',

    // Payment
    'payment_status' => '결제 상태',
    'transaction_id' => '거래 ID',
    'transaction_id_helper' => '결제 방법이 COD 또는 은행 송금인 경우 이 필드를 비워둘 수 있습니다',
    'payment_method_helper' => '이 예약에 사용된 결제 방법을 선택하세요',
    'payment_status_helper' => '결제의 현재 상태',

    // Form placeholders
    'first_name_placeholder' => '이름 입력',
    'last_name_placeholder' => '성 입력',
    'email_placeholder' => '이메일 주소 입력',
    'phone_placeholder' => '전화번호 입력',
    'address_placeholder' => '주소 입력',
    'city_placeholder' => '도시 입력',
    'state_placeholder' => '시/도 입력',
    'country_placeholder' => '국가 입력',
    'zip_placeholder' => '우편번호 입력',

    // Misc
    'no_customers_found' => '고객을 찾을 수 없습니다',
    'no_cars_available' => '선택한 날짜에 이용 가능한 차량이 없습니다',
    'select_car' => '차량 선택',
    'print_booking_info' => '예약 정보 인쇄',
    'printed_on' => '인쇄일',
    'computer_generated_document' => '이것은 컴퓨터로 생성된 문서이며 서명이 필요하지 않습니다.',
    'booking_summary' => '예약 요약',
    'booking_details' => '예약 세부정보',
    'additional_services' => '추가 서비스',
    'rental_period' => '대여 기간',
    'to' => '~',

    // Completion details
    'completion_details' => '완료 세부정보',
    'add_completion_details' => '완료 세부정보 추가',
    'edit_completion_details' => '완료 세부정보 편집',
    'no_completion_details' => '완료 세부정보가 아직 추가되지 않았습니다.',
    'completion_details_updated_successfully' => '완료 세부정보가 성공적으로 업데이트되었습니다.',

    'completion_miles' => '최종 주행거리',
    'completion_kilometers' => '최종 킬로미터',
    'miles' => '마일',
    'kilometers' => '킬로미터',
    'enter_miles' => '최종 주행거리 입력',
    'enter_kilometers' => '최종 킬로미터 입력',
    'completion_miles_help' => '차량 반납 시 최종 주행거리를 입력하세요.',
    'completion_kilometers_help' => '차량 반납 시 최종 킬로미터를 입력하세요.',

    'completion_gas_level' => '연료 레벨',
    'select_gas_level' => '연료 레벨 선택',
    'gas_empty' => '비어있음',
    'gas_quarter' => '1/4 탱크',
    'gas_half' => '1/2 탱크',
    'gas_three_quarters' => '3/4 탱크',
    'gas_full' => '만탱크',
    'completion_gas_level_help' => '차량 반납 시 연료 레벨을 선택하세요.',

    'damage_images' => '손상 이미지',
    'damage_image' => '손상 이미지',
    'damage_images_help' => '차량에서 발견된 손상 이미지를 업로드하세요 (이미지당 최대 5MB).',
    'existing_images' => '기존 이미지',

    'completion_notes' => '완료 비고',
    'completion_notes_placeholder' => '차량 상태, 손상 또는 기타 관찰 사항에 대한 비고를 입력하세요...',
    'completion_notes_help' => '차량 상태 또는 대여 완료에 대한 추가 비고를 추가하세요.',

    'completed_at' => '완료 일시',

    // Coupon fields
    'coupon_code' => '쿠폰 코드',
    'coupon_amount' => '쿠폰 할인 금액',
    'enter_coupon_code' => '쿠폰 코드 입력',
    'coupon_discount_amount' => '할인 금액',
    'applied_coupon' => '적용된 쿠폰',

    // Validation messages
    'validation' => [
        'completion_miles_integer' => '주행거리는 유효한 숫자여야 합니다.',
        'completion_miles_min' => '주행거리는 0 이상이어야 합니다.',
        'completion_gas_level_invalid' => '유효한 연료 레벨을 선택하세요.',
        'damage_image_invalid' => '업로드된 파일은 유효한 이미지여야 합니다.',
        'damage_image_max_size' => '이미지 크기는 5MB를 초과할 수 없습니다.',
        'completion_notes_max' => '비고는 10,000자를 초과할 수 없습니다.',
    ],

    // Vendor booking actions
    'approve_booking' => '예약 승인',
    'cancel_booking' => '예약 취소',
    'processing' => '처리 중...',
    'pending_approval_notice' => '승인 대기 중',
    'pending_approval_description' => '이 예약은 귀하의 승인을 기다리고 있습니다. 세부 정보를 검토하고 예약을 승인하거나 취소하십시오.',
    'approve_booking_confirmation' => '이 예약을 승인하시겠습니까? 고객에게 알림이 전송됩니다.',
    'cancel_booking_confirmation' => '이 예약을 취소하시겠습니까? 이 작업은 취소할 수 없습니다.',
    'booking_approved_successfully' => '예약이 성공적으로 승인되었습니다.',
    'booking_cancelled_successfully' => '예약이 성공적으로 취소되었습니다.',
    'cannot_approve_booking' => '이 예약은 승인할 수 없습니다. 대기 중인 예약만 승인할 수 있습니다.',
    'cannot_cancel_booking' => '이 예약은 취소할 수 없습니다. 완료되었거나 이미 취소된 예약은 취소할 수 없습니다.',
];

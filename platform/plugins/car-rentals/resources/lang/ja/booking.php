<?php

return [
    'name' => '予約',
    'create' => '新規予約',
    'reports' => '予約レポート',
    'calendar' => '予約カレンダー',
    'statuses' => [
        'pending' => '保留中',
        'processing' => '処理中',
        'completed' => '完了',
        'cancelled' => 'キャンセル',
    ],
    'customer' => '顧客',
    'amount' => '金額',
    'rental_period' => 'レンタル期間',
    'payment_method' => '支払い方法',
    'payment_status' => '支払いステータス',
    'booking_information' => '予約情報',
    'booking_period' => '予約期間',
    'payment_status_label' => '支払いステータス',
    'car' => '車両',
    'calendar_item_title' => ':car（:customer）',

    // Dates
    'start_date' => '開始日',
    'end_date' => '終了日',

    // Car search
    'search_cars' => '車両を検索',
    'selected_car' => '選択された車両',
    'please_select_dates' => '開始日と終了日の両方を選択してください',
    'please_select_car' => '予約を続行するには車両を選択してください',

    // Booking details
    'booking_details' => '予約詳細',

    // Customer information
    'search_customer' => '名前、メール、電話番号で顧客を検索...',
    'create_new_customer' => '新規顧客を作成',
    'customer_created_successfully' => '顧客が正常に作成されました',
    'customer_not_found' => '顧客が見つかりません',
    'customer_information' => '顧客情報',
    'customer_name' => '名前',
    'email' => 'メール',
    'phone' => '電話',
    'customer_age' => '年齢',
    'address' => '住所',
    'city' => '市区町村',
    'state' => '都道府県',
    'country' => '国',
    'zip' => '郵便番号',
    'note' => '備考',
    'note_placeholder' => '特別なリクエストや備考を入力',

    // Services
    'services' => '追加サービス',
    'day' => '日',

    // Payment
    'payment_status' => '支払いステータス',
    'transaction_id' => '取引ID',
    'transaction_id_helper' => '支払い方法がCODまたは銀行振込の場合、このフィールドは空欄にしておけます',
    'payment_method_helper' => 'この予約に使用された支払い方法を選択',
    'payment_status_helper' => '支払いの現在のステータス',

    // Form placeholders
    'first_name_placeholder' => '名を入力',
    'last_name_placeholder' => '姓を入力',
    'email_placeholder' => 'メールアドレスを入力',
    'phone_placeholder' => '電話番号を入力',
    'address_placeholder' => '住所を入力',
    'city_placeholder' => '市区町村を入力',
    'state_placeholder' => '都道府県を入力',
    'country_placeholder' => '国を入力',
    'zip_placeholder' => '郵便番号を入力',

    // Misc
    'no_customers_found' => '顧客が見つかりません',
    'no_cars_available' => '選択された日付で利用可能な車両はありません',
    'select_car' => '車両を選択',
    'print_booking_info' => '予約情報を印刷',
    'printed_on' => '印刷日',
    'computer_generated_document' => 'これはコンピュータで生成された文書であり、署名は不要です。',
    'booking_summary' => '予約概要',
    'booking_details' => '予約詳細',
    'additional_services' => '追加サービス',
    'rental_period' => 'レンタル期間',
    'to' => 'から',

    // Completion details
    'completion_details' => '完了詳細',
    'add_completion_details' => '完了詳細を追加',
    'edit_completion_details' => '完了詳細を編集',
    'no_completion_details' => '完了詳細はまだ追加されていません。',
    'completion_details_updated_successfully' => '完了詳細が正常に更新されました。',

    'completion_miles' => '最終走行距離',
    'completion_kilometers' => '最終キロメートル',
    'miles' => 'マイル',
    'kilometers' => 'キロメートル',
    'enter_miles' => '最終走行距離を入力',
    'enter_kilometers' => '最終キロメートルを入力',
    'completion_miles_help' => '車両返却時の最終走行距離を入力してください。',
    'completion_kilometers_help' => '車両返却時の最終キロメートルを入力してください。',

    'completion_gas_level' => 'ガスレベル',
    'select_gas_level' => 'ガスレベルを選択',
    'gas_empty' => '空',
    'gas_quarter' => '1/4タンク',
    'gas_half' => '1/2タンク',
    'gas_three_quarters' => '3/4タンク',
    'gas_full' => '満タン',
    'completion_gas_level_help' => '車両返却時のガスレベルを選択してください。',

    'damage_images' => '損傷画像',
    'damage_image' => '損傷画像',
    'damage_images_help' => '車両に見つかった損傷の画像をアップロード（画像1枚あたり最大5MB）。',
    'existing_images' => '既存の画像',

    'completion_notes' => '完了備考',
    'completion_notes_placeholder' => '車両の状態、損傷、その他の観察事項に関する備考を入力...',
    'completion_notes_help' => '車両の状態やレンタル完了に関する追加の備考を追加してください。',

    'completed_at' => '完了日時',

    // Coupon fields
    'coupon_code' => 'クーポンコード',
    'coupon_amount' => 'クーポン割引額',
    'enter_coupon_code' => 'クーポンコードを入力',
    'coupon_discount_amount' => '割引額',
    'applied_coupon' => '適用されたクーポン',

    // Validation messages
    'validation' => [
        'completion_miles_integer' => '走行距離は有効な数値でなければなりません。',
        'completion_miles_min' => '走行距離は0以上でなければなりません。',
        'completion_gas_level_invalid' => '有効なガスレベルを選択してください。',
        'damage_image_invalid' => 'アップロードされたファイルは有効な画像でなければなりません。',
        'damage_image_max_size' => '画像サイズは5MBを超えてはなりません。',
        'completion_notes_max' => '備考は10,000文字を超えてはなりません。',
    ],

    // Vendor booking actions
    'approve_booking' => '予約を承認',
    'cancel_booking' => '予約をキャンセル',
    'processing' => '処理中...',
    'pending_approval_notice' => '承認待ち',
    'pending_approval_description' => 'この予約はあなたの承認を待っています。詳細を確認して、予約を承認またはキャンセルしてください。',
    'approve_booking_confirmation' => 'この予約を承認してもよろしいですか？お客様に通知されます。',
    'cancel_booking_confirmation' => 'この予約をキャンセルしてもよろしいですか？この操作は元に戻せません。',
    'booking_approved_successfully' => '予約が正常に承認されました。',
    'booking_cancelled_successfully' => '予約が正常にキャンセルされました。',
    'cannot_approve_booking' => 'この予約は承認できません。保留中の予約のみ承認できます。',
    'cannot_cancel_booking' => 'この予約はキャンセルできません。完了済みまたはすでにキャンセル済みの予約はキャンセルできません。',
];

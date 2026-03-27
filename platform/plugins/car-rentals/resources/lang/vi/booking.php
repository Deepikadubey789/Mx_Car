<?php

return [
    'name' => 'Đặt xe',
    'create' => 'Đặt xe mới',
    'reports' => 'Báo cáo Đặt xe',
    'calendar' => 'Lịch Đặt xe',
    'statuses' => [
        'pending' => 'Đang chờ',
        'processing' => 'Đang xử lý',
        'completed' => 'Hoàn thành',
        'cancelled' => 'Đã hủy',
    ],
    'customer' => 'Khách hàng',
    'amount' => 'Số tiền',
    'rental_period' => 'Thời gian thuê',
    'payment_method' => 'Phương thức thanh toán',
    'payment_status' => 'Trạng thái thanh toán',
    'booking_information' => 'Thông tin đặt xe',
    'booking_period' => 'Thời gian đặt xe',
    'payment_status_label' => 'Trạng thái thanh toán',
    'car' => 'Xe',
    'calendar_item_title' => ':car (Khách: :customer)',

    // Dates
    'start_date' => 'Ngày bắt đầu',
    'end_date' => 'Ngày kết thúc',

    // Car search
    'search_cars' => 'Tìm kiếm xe',
    'selected_car' => 'Xe đã chọn',
    'please_select_dates' => 'Vui lòng chọn cả ngày bắt đầu và kết thúc',
    'please_select_car' => 'Vui lòng chọn xe để tiếp tục đặt xe',

    // Booking details
    'booking_details' => 'Chi tiết đặt xe',

    // Customer information
    'search_customer' => 'Tìm kiếm khách hàng theo tên, email hoặc điện thoại...',
    'create_new_customer' => 'Tạo khách hàng mới',
    'customer_created_successfully' => 'Tạo khách hàng thành công',
    'customer_not_found' => 'Không tìm thấy khách hàng',
    'customer_information' => 'Thông tin khách hàng',
    'customer_name' => 'Tên',
    'email' => 'Email',
    'phone' => 'Điện thoại',
    'customer_age' => 'Tuổi',
    'address' => 'Địa chỉ',
    'city' => 'Thành phố',
    'state' => 'Tỉnh/Thành',
    'country' => 'Quốc gia',
    'zip' => 'Mã bưu điện',
    'note' => 'Ghi chú',
    'note_placeholder' => 'Nhập yêu cầu đặc biệt hoặc ghi chú',

    // Services
    'services' => 'Dịch vụ bổ sung',
    'day' => 'ngày',

    // Payment
    'payment_status' => 'Trạng thái thanh toán',
    'transaction_id' => 'Mã giao dịch',
    'transaction_id_helper' => 'Bạn có thể để trống trường này nếu phương thức thanh toán là COD hoặc Chuyển khoản ngân hàng',
    'payment_method_helper' => 'Chọn phương thức thanh toán được sử dụng cho đặt xe này',
    'payment_status_helper' => 'Trạng thái hiện tại của thanh toán',

    // Form placeholders
    'first_name_placeholder' => 'Nhập tên',
    'last_name_placeholder' => 'Nhập họ',
    'email_placeholder' => 'Nhập địa chỉ email',
    'phone_placeholder' => 'Nhập số điện thoại',
    'address_placeholder' => 'Nhập địa chỉ',
    'city_placeholder' => 'Nhập thành phố',
    'state_placeholder' => 'Nhập tỉnh/thành',
    'country_placeholder' => 'Nhập quốc gia',
    'zip_placeholder' => 'Nhập mã bưu điện',

    // Misc
    'no_customers_found' => 'Không tìm thấy khách hàng',
    'no_cars_available' => 'Không có xe khả dụng cho ngày đã chọn',
    'select_car' => 'Chọn xe',
    'print_booking_info' => 'In thông tin đặt xe',
    'printed_on' => 'Được in vào',
    'computer_generated_document' => 'Đây là tài liệu được tạo bằng máy tính và không yêu cầu chữ ký.',
    'booking_summary' => 'Tóm tắt đặt xe',
    'booking_details' => 'Chi tiết đặt xe',
    'additional_services' => 'Dịch vụ bổ sung',
    'rental_period' => 'Thời gian thuê',
    'to' => 'đến',

    // Coupon fields
    'coupon_code' => 'Mã giảm giá',
    'coupon_amount' => 'Số tiền giảm giá',
    'enter_coupon_code' => 'Nhập mã giảm giá',
    'coupon_discount_amount' => 'Số tiền giảm',
    'applied_coupon' => 'Mã giảm giá đã áp dụng',

    // Completion details
    'completion_details' => 'Chi tiết hoàn thành',
    'add_completion_details' => 'Thêm chi tiết hoàn thành',
    'edit_completion_details' => 'Chỉnh sửa chi tiết hoàn thành',
    'no_completion_details' => 'Chưa có chi tiết hoàn thành nào được thêm.',
    'completion_details_updated_successfully' => 'Chi tiết hoàn thành đã được cập nhật thành công.',

    'completion_miles' => 'Số dặm cuối cùng',
    'completion_kilometers' => 'Số km cuối cùng',
    'miles' => 'dặm',
    'kilometers' => 'km',
    'enter_miles' => 'Nhập số dặm cuối cùng',
    'enter_kilometers' => 'Nhập số km cuối cùng',
    'completion_miles_help' => 'Nhập số đo dặm cuối cùng khi trả xe.',
    'completion_kilometers_help' => 'Nhập số đo km cuối cùng khi trả xe.',

    'completion_gas_level' => 'Mức nhiên liệu',
    'select_gas_level' => 'Chọn mức nhiên liệu',
    'gas_empty' => 'Trống',
    'gas_quarter' => '1/4 bình',
    'gas_half' => '1/2 bình',
    'gas_three_quarters' => '3/4 bình',
    'gas_full' => 'Đầy bình',
    'completion_gas_level_help' => 'Chọn mức nhiên liệu khi trả xe.',

    'damage_images' => 'Hình ảnh hư hỏng',
    'damage_image' => 'Hình ảnh hư hỏng',
    'damage_images_help' => 'Tải lên hình ảnh của bất kỳ hư hỏng nào phát hiện trên xe (tối đa 5MB mỗi hình).',
    'existing_images' => 'Hình ảnh hiện tại',

    'completion_notes' => 'Ghi chú hoàn thành',
    'completion_notes_placeholder' => 'Nhập bất kỳ ghi chú nào về tình trạng xe, hư hỏng hoặc các quan sát khác...',
    'completion_notes_help' => 'Thêm bất kỳ ghi chú bổ sung nào về tình trạng xe hoặc hoàn thành cho thuê.',

    'completed_at' => 'Hoàn thành lúc',

    // Validation messages
    'validation' => [
        'completion_miles_integer' => 'Số dặm phải là một số hợp lệ.',
        'completion_miles_min' => 'Số dặm phải ít nhất là 0.',
        'completion_gas_level_invalid' => 'Vui lòng chọn mức nhiên liệu hợp lệ.',
        'damage_image_invalid' => 'Tập tin tải lên phải là hình ảnh hợp lệ.',
        'damage_image_max_size' => 'Kích thước hình ảnh không được vượt quá 5MB.',
        'completion_notes_max' => 'Ghi chú không được vượt quá 10,000 ký tự.',
    ],

    // Vendor booking actions
    'approve_booking' => 'Duyệt đặt chỗ',
    'cancel_booking' => 'Hủy đặt chỗ',
    'processing' => 'Đang xử lý...',
    'pending_approval_notice' => 'Chờ phê duyệt',
    'pending_approval_description' => 'Đặt chỗ này đang chờ sự chấp thuận của bạn. Vui lòng xem xét chi tiết và phê duyệt hoặc hủy đặt chỗ.',
    'approve_booking_confirmation' => 'Bạn có chắc chắn muốn phê duyệt đặt chỗ này không? Khách hàng sẽ được thông báo.',
    'cancel_booking_confirmation' => 'Bạn có chắc chắn muốn hủy đặt chỗ này không? Hành động này không thể hoàn tác.',
    'booking_approved_successfully' => 'Đặt chỗ đã được phê duyệt thành công.',
    'booking_cancelled_successfully' => 'Đặt chỗ đã được hủy thành công.',
    'cannot_approve_booking' => 'Không thể phê duyệt đặt chỗ này. Chỉ có thể phê duyệt các đặt chỗ đang chờ xử lý.',
    'cannot_cancel_booking' => 'Không thể hủy đặt chỗ này. Không thể hủy các đặt chỗ đã hoàn thành hoặc đã hủy.',
];

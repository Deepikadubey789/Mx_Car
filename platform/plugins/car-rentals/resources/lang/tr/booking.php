<?php

return [
    'name' => 'Rezervasyonlar',
    'create' => 'Yeni rezervasyon',
    'reports' => 'Rezervasyon Raporları',
    'calendar' => 'Rezervasyon Takvimi',
    'statuses' => [
        'pending' => 'Beklemede',
        'processing' => 'İşleniyor',
        'completed' => 'Tamamlandı',
        'cancelled' => 'İptal Edildi',
    ],
    'customer' => 'Müşteri',
    'amount' => 'Tutar',
    'rental_period' => 'Kiralama Süresi',
    'payment_method' => 'Ödeme Yöntemi',
    'payment_status' => 'Ödeme Durumu',
    'booking_information' => 'Rezervasyon Bilgileri',
    'booking_period' => 'Rezervasyon Süresi',
    'payment_status_label' => 'Ödeme Durumu',
    'car' => 'Araç',
    'calendar_item_title' => ':car (:customer)',

    // Dates
    'start_date' => 'Başlangıç Tarihi',
    'end_date' => 'Bitiş Tarihi',

    // Car search
    'search_cars' => 'Araç Ara',
    'selected_car' => 'Seçilen Araç',
    'please_select_dates' => 'Lütfen başlangıç ve bitiş tarihlerini seçin',
    'please_select_car' => 'Rezervasyona devam etmek için lütfen bir araç seçin',

    // Booking details
    'booking_details' => 'Rezervasyon Detayları',

    // Customer information
    'search_customer' => 'İsim, e-posta veya telefon ile müşteri ara...',
    'create_new_customer' => 'Yeni müşteri oluştur',
    'customer_created_successfully' => 'Müşteri başarıyla oluşturuldu',
    'customer_not_found' => 'Müşteri bulunamadı',
    'customer_information' => 'Müşteri Bilgileri',
    'customer_name' => 'İsim',
    'email' => 'E-posta',
    'phone' => 'Telefon',
    'customer_age' => 'Yaş',
    'address' => 'Adres',
    'city' => 'Şehir',
    'state' => 'Eyalet/İl',
    'country' => 'Ülke',
    'zip' => 'Posta Kodu',
    'note' => 'Not',
    'note_placeholder' => 'Özel istekler veya notlar girin',

    // Services
    'services' => 'Ek Hizmetler',
    'day' => 'gün',

    // Payment
    'payment_status' => 'Ödeme durumu',
    'transaction_id' => 'İşlem ID',
    'transaction_id_helper' => 'Ödeme yöntemi Kapıda Ödeme veya Banka Havalesi ise bu alanı boş bırakabilirsiniz',
    'payment_method_helper' => 'Bu rezervasyon için kullanılan ödeme yöntemini seçin',
    'payment_status_helper' => 'Ödemenin mevcut durumu',

    // Form placeholders
    'first_name_placeholder' => 'Ad girin',
    'last_name_placeholder' => 'Soyad girin',
    'email_placeholder' => 'E-posta adresi girin',
    'phone_placeholder' => 'Telefon numarası girin',
    'address_placeholder' => 'Adres girin',
    'city_placeholder' => 'Şehir girin',
    'state_placeholder' => 'Eyalet/İl girin',
    'country_placeholder' => 'Ülke girin',
    'zip_placeholder' => 'Posta kodu girin',

    // Misc
    'no_customers_found' => 'Müşteri bulunamadı',
    'no_cars_available' => 'Seçilen tarihler için uygun araç yok',
    'select_car' => 'Araç Seç',
    'print_booking_info' => 'Rezervasyon Bilgilerini Yazdır',
    'printed_on' => 'Yazdırılma tarihi',
    'computer_generated_document' => 'Bu bilgisayar tarafından oluşturulan bir belgedir ve imza gerektirmez.',
    'booking_summary' => 'Rezervasyon Özeti',
    'booking_details' => 'Rezervasyon Detayları',
    'additional_services' => 'Ek Hizmetler',
    'rental_period' => 'Kiralama Süresi',
    'to' => 'e kadar',

    // Completion details
    'completion_details' => 'Tamamlama Detayları',
    'add_completion_details' => 'Tamamlama Detayları Ekle',
    'edit_completion_details' => 'Tamamlama Detaylarını Düzenle',
    'no_completion_details' => 'Henüz tamamlama detayları eklenmemiş.',
    'completion_details_updated_successfully' => 'Tamamlama detayları başarıyla güncellendi.',

    'completion_miles' => 'Son Kilometre',
    'completion_kilometers' => 'Son Kilometre',
    'miles' => 'mil',
    'kilometers' => 'kilometre',
    'enter_miles' => 'Son kilometreyi girin',
    'enter_kilometers' => 'Son kilometreyi girin',
    'completion_miles_help' => 'Araç iade edildiğinde son kilometre okumasını girin.',
    'completion_kilometers_help' => 'Araç iade edildiğinde son kilometre okumasını girin.',

    'completion_gas_level' => 'Yakıt Seviyesi',
    'select_gas_level' => 'Yakıt seviyesini seçin',
    'gas_empty' => 'Boş',
    'gas_quarter' => '1/4 Depo',
    'gas_half' => '1/2 Depo',
    'gas_three_quarters' => '3/4 Depo',
    'gas_full' => 'Dolu Depo',
    'completion_gas_level_help' => 'Araç iade edildiğinde yakıt seviyesini seçin.',

    'damage_images' => 'Hasar Görselleri',
    'damage_image' => 'Hasar Görseli',
    'damage_images_help' => 'Araçta bulunan herhangi bir hasarın görsellerini yükleyin (görsel başına maks. 5MB).',
    'existing_images' => 'Mevcut görseller',

    'completion_notes' => 'Tamamlama Notları',
    'completion_notes_placeholder' => 'Araç durumu, hasarlar veya diğer gözlemler hakkında notlar girin...',
    'completion_notes_help' => 'Araç durumu veya kiralama tamamlaması hakkında ek notlar ekleyin.',

    'completed_at' => 'Tamamlanma Tarihi',

    // Coupon fields
    'coupon_code' => 'Kupon Kodu',
    'coupon_amount' => 'Kupon İndirim Tutarı',
    'enter_coupon_code' => 'Kupon kodu girin',
    'coupon_discount_amount' => 'İndirim Tutarı',
    'applied_coupon' => 'Uygulanan Kupon',

    // Validation messages
    'validation' => [
        'completion_miles_integer' => 'Kilometre geçerli bir sayı olmalıdır.',
        'completion_miles_min' => 'Kilometre en az 0 olmalıdır.',
        'completion_gas_level_invalid' => 'Lütfen geçerli bir yakıt seviyesi seçin.',
        'damage_image_invalid' => 'Yüklenen dosya geçerli bir görsel olmalıdır.',
        'damage_image_max_size' => 'Görsel boyutu 5MB\'ı geçmemelidir.',
        'completion_notes_max' => 'Notlar 10.000 karakteri geçmemelidir.',
    ],

    // Vendor booking actions
    'approve_booking' => 'Rezervasyonu onayla',
    'cancel_booking' => 'Rezervasyonu iptal et',
    'processing' => 'İşleniyor...',
    'pending_approval_notice' => 'Onay bekliyor',
    'pending_approval_description' => 'Bu rezervasyon onayınızı bekliyor. Lütfen detayları inceleyin ve rezervasyonu onaylayın veya iptal edin.',
    'approve_booking_confirmation' => 'Bu rezervasyonu onaylamak istediğinizden emin misiniz? Müşteri bilgilendirilecektir.',
    'cancel_booking_confirmation' => 'Bu rezervasyonu iptal etmek istediğinizden emin misiniz? Bu işlem geri alınamaz.',
    'booking_approved_successfully' => 'Rezervasyon başarıyla onaylandı.',
    'booking_cancelled_successfully' => 'Rezervasyon başarıyla iptal edildi.',
    'cannot_approve_booking' => 'Bu rezervasyon onaylanamaz. Sadece bekleyen rezervasyonlar onaylanabilir.',
    'cannot_cancel_booking' => 'Bu rezervasyon iptal edilemez. Tamamlanmış veya zaten iptal edilmiş rezervasyonlar iptal edilemez.',
];

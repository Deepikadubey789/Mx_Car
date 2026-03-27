<?php

return [
    'name' => 'Pemesanan',
    'create' => 'Pemesanan baru',
    'reports' => 'Laporan Pemesanan',
    'calendar' => 'Kalender Pemesanan',
    'statuses' => [
        'pending' => 'Tertunda',
        'processing' => 'Diproses',
        'completed' => 'Selesai',
        'cancelled' => 'Dibatalkan',
    ],
    'customer' => 'Pelanggan',
    'amount' => 'Jumlah',
    'rental_period' => 'Periode Sewa',
    'payment_method' => 'Metode Pembayaran',
    'payment_status' => 'Status Pembayaran',
    'booking_information' => 'Informasi Pemesanan',
    'booking_period' => 'Periode Pemesanan',
    'payment_status_label' => 'Status Pembayaran',
    'car' => 'Mobil',
    'calendar_item_title' => ':car (:customer)',

    // Dates
    'start_date' => 'Tanggal Mulai',
    'end_date' => 'Tanggal Selesai',

    // Car search
    'search_cars' => 'Cari Mobil',
    'selected_car' => 'Mobil Terpilih',
    'please_select_dates' => 'Silakan pilih tanggal mulai dan selesai',
    'please_select_car' => 'Silakan pilih mobil untuk melanjutkan pemesanan',

    // Booking details
    'booking_details' => 'Detail Pemesanan',

    // Customer information
    'search_customer' => 'Cari pelanggan berdasarkan nama, email atau telepon...',
    'create_new_customer' => 'Buat pelanggan baru',
    'customer_created_successfully' => 'Pelanggan berhasil dibuat',
    'customer_not_found' => 'Pelanggan tidak ditemukan',
    'customer_information' => 'Informasi Pelanggan',
    'customer_name' => 'Nama',
    'email' => 'Email',
    'phone' => 'Telepon',
    'customer_age' => 'Usia',
    'address' => 'Alamat',
    'city' => 'Kota',
    'state' => 'Provinsi',
    'country' => 'Negara',
    'zip' => 'Kode Pos',
    'note' => 'Catatan',
    'note_placeholder' => 'Masukkan permintaan khusus atau catatan',

    // Services
    'services' => 'Layanan Tambahan',
    'day' => 'hari',

    // Payment
    'payment_status' => 'Status pembayaran',
    'transaction_id' => 'ID Transaksi',
    'transaction_id_helper' => 'Anda dapat mengosongkan bidang ini jika metode pembayaran adalah COD atau Transfer Bank',
    'payment_method_helper' => 'Pilih metode pembayaran yang digunakan untuk pemesanan ini',
    'payment_status_helper' => 'Status pembayaran saat ini',

    // Form placeholders
    'first_name_placeholder' => 'Masukkan nama depan',
    'last_name_placeholder' => 'Masukkan nama belakang',
    'email_placeholder' => 'Masukkan alamat email',
    'phone_placeholder' => 'Masukkan nomor telepon',
    'address_placeholder' => 'Masukkan alamat',
    'city_placeholder' => 'Masukkan kota',
    'state_placeholder' => 'Masukkan provinsi',
    'country_placeholder' => 'Masukkan negara',
    'zip_placeholder' => 'Masukkan kode pos',

    // Misc
    'no_customers_found' => 'Tidak ada pelanggan yang ditemukan',
    'no_cars_available' => 'Tidak ada mobil yang tersedia untuk tanggal yang dipilih',
    'select_car' => 'Pilih Mobil',
    'print_booking_info' => 'Cetak Info Pemesanan',
    'printed_on' => 'Dicetak pada',
    'computer_generated_document' => 'Ini adalah dokumen yang dibuat komputer dan tidak memerlukan tanda tangan.',
    'booking_summary' => 'Ringkasan Pemesanan',
    'additional_services' => 'Layanan Tambahan',
    'to' => 'hingga',

    // Completion details
    'completion_details' => 'Detail Penyelesaian',
    'add_completion_details' => 'Tambah Detail Penyelesaian',
    'edit_completion_details' => 'Edit Detail Penyelesaian',
    'no_completion_details' => 'Belum ada detail penyelesaian yang ditambahkan.',
    'completion_details_updated_successfully' => 'Detail penyelesaian berhasil diperbarui.',

    'completion_miles' => 'Jarak Tempuh Akhir',
    'completion_kilometers' => 'Kilometer Akhir',
    'miles' => 'mil',
    'kilometers' => 'kilometer',
    'enter_miles' => 'Masukkan jarak tempuh akhir',
    'enter_kilometers' => 'Masukkan kilometer akhir',
    'completion_miles_help' => 'Masukkan pembacaan jarak tempuh akhir saat mobil dikembalikan.',
    'completion_kilometers_help' => 'Masukkan pembacaan kilometer akhir saat mobil dikembalikan.',

    'completion_gas_level' => 'Level Bensin',
    'select_gas_level' => 'Pilih level bensin',
    'gas_empty' => 'Kosong',
    'gas_quarter' => '1/4 Tangki',
    'gas_half' => '1/2 Tangki',
    'gas_three_quarters' => '3/4 Tangki',
    'gas_full' => 'Tangki Penuh',
    'completion_gas_level_help' => 'Pilih level bensin saat mobil dikembalikan.',

    'damage_images' => 'Gambar Kerusakan',
    'damage_image' => 'Gambar Kerusakan',
    'damage_images_help' => 'Unggah gambar kerusakan yang ditemukan pada kendaraan (maksimal 5MB per gambar).',
    'existing_images' => 'Gambar yang ada',

    'completion_notes' => 'Catatan Penyelesaian',
    'completion_notes_placeholder' => 'Masukkan catatan tentang kondisi kendaraan, kerusakan, atau pengamatan lainnya...',
    'completion_notes_help' => 'Tambahkan catatan tambahan tentang kondisi kendaraan atau penyelesaian sewa.',

    'completed_at' => 'Selesai Pada',

    // Coupon fields
    'coupon_code' => 'Kode Kupon',
    'coupon_amount' => 'Jumlah Diskon Kupon',
    'enter_coupon_code' => 'Masukkan kode kupon',
    'coupon_discount_amount' => 'Jumlah Diskon',
    'applied_coupon' => 'Kupon Diterapkan',

    // Validation messages
    'validation' => [
        'completion_miles_integer' => 'Jarak tempuh harus berupa angka yang valid.',
        'completion_miles_min' => 'Jarak tempuh harus minimal 0.',
        'completion_gas_level_invalid' => 'Silakan pilih level bensin yang valid.',
        'damage_image_invalid' => 'File yang diunggah harus berupa gambar yang valid.',
        'damage_image_max_size' => 'Ukuran gambar tidak boleh melebihi 5MB.',
        'completion_notes_max' => 'Catatan tidak boleh melebihi 10.000 karakter.',
    ],

    // Vendor booking actions
    'approve_booking' => 'Setujui pemesanan',
    'cancel_booking' => 'Batalkan pemesanan',
    'processing' => 'Memproses...',
    'pending_approval_notice' => 'Menunggu persetujuan',
    'pending_approval_description' => 'Pemesanan ini menunggu persetujuan Anda. Silakan tinjau detail dan setujui atau batalkan pemesanan.',
    'approve_booking_confirmation' => 'Apakah Anda yakin ingin menyetujui pemesanan ini? Pelanggan akan diberitahu.',
    'cancel_booking_confirmation' => 'Apakah Anda yakin ingin membatalkan pemesanan ini? Tindakan ini tidak dapat dibatalkan.',
    'booking_approved_successfully' => 'Pemesanan telah disetujui dengan sukses.',
    'booking_cancelled_successfully' => 'Pemesanan telah dibatalkan dengan sukses.',
    'cannot_approve_booking' => 'Pemesanan ini tidak dapat disetujui. Hanya pemesanan yang tertunda yang dapat disetujui.',
    'cannot_cancel_booking' => 'Pemesanan ini tidak dapat dibatalkan. Pemesanan yang sudah selesai atau sudah dibatalkan tidak dapat dibatalkan.',
];

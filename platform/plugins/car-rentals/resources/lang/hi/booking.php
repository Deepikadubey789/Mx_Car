<?php

return [
    'name' => 'बुकिंग',
    'create' => 'नई बुकिंग',
    'reports' => 'बुकिंग रिपोर्ट',
    'calendar' => 'बुकिंग कैलेंडर',
    'statuses' => [
        'pending' => 'लंबित',
        'processing' => 'प्रसंस्करण',
        'completed' => 'पूर्ण',
        'cancelled' => 'रद्द',
    ],
    'customer' => 'ग्राहक',
    'amount' => 'राशि',
    'rental_period' => 'किराया अवधि',
    'payment_method' => 'भुगतान विधि',
    'payment_status' => 'भुगतान स्थिति',
    'booking_information' => 'बुकिंग जानकारी',
    'booking_period' => 'बुकिंग अवधि',
    'payment_status_label' => 'भुगतान स्थिति',
    'car' => 'कार',
    'calendar_item_title' => ':car (:customer)',

    // Dates
    'start_date' => 'प्रारंभ तिथि',
    'end_date' => 'समाप्ति तिथि',

    // Car search
    'search_cars' => 'कारें खोजें',
    'selected_car' => 'चयनित कार',
    'please_select_dates' => 'कृपया प्रारंभ और समाप्ति तिथि दोनों का चयन करें',
    'please_select_car' => 'बुकिंग जारी रखने के लिए कृपया एक कार का चयन करें',

    // Booking details
    'booking_details' => 'बुकिंग विवरण',

    // Customer information
    'search_customer' => 'नाम, ईमेल या फोन द्वारा ग्राहक खोजें...',
    'create_new_customer' => 'नया ग्राहक बनाएं',
    'customer_created_successfully' => 'ग्राहक सफलतापूर्वक बनाया गया',
    'customer_not_found' => 'ग्राहक नहीं मिला',
    'customer_information' => 'ग्राहक जानकारी',
    'customer_name' => 'नाम',
    'email' => 'ईमेल',
    'phone' => 'फोन',
    'customer_age' => 'उम्र',
    'address' => 'पता',
    'city' => 'शहर',
    'state' => 'राज्य/प्रांत',
    'country' => 'देश',
    'zip' => 'ज़िप/पोस्टल कोड',
    'note' => 'नोट',
    'note_placeholder' => 'कोई विशेष अनुरोध या नोट दर्ज करें',

    // Services
    'services' => 'अतिरिक्त सेवाएं',
    'day' => 'दिन',

    // Payment
    'payment_status' => 'भुगतान स्थिति',
    'transaction_id' => 'लेनदेन आईडी',
    'transaction_id_helper' => 'यदि भुगतान विधि COD या बैंक ट्रांसफर है तो आप इस फ़ील्ड को खाली छोड़ सकते हैं',
    'payment_method_helper' => 'इस बुकिंग के लिए उपयोग की गई भुगतान विधि का चयन करें',
    'payment_status_helper' => 'भुगतान की वर्तमान स्थिति',

    // Form placeholders
    'first_name_placeholder' => 'प्रथम नाम दर्ज करें',
    'last_name_placeholder' => 'अंतिम नाम दर्ज करें',
    'email_placeholder' => 'ईमेल पता दर्ज करें',
    'phone_placeholder' => 'फोन नंबर दर्ज करें',
    'address_placeholder' => 'पता दर्ज करें',
    'city_placeholder' => 'शहर दर्ज करें',
    'state_placeholder' => 'राज्य/प्रांत दर्ज करें',
    'country_placeholder' => 'देश दर्ज करें',
    'zip_placeholder' => 'ज़िप/पोस्टल कोड दर्ज करें',

    // Misc
    'no_customers_found' => 'कोई ग्राहक नहीं मिला',
    'no_cars_available' => 'चयनित तिथियों के लिए कोई कार उपलब्ध नहीं है',
    'select_car' => 'कार चुनें',
    'print_booking_info' => 'बुकिंग जानकारी प्रिंट करें',
    'printed_on' => 'प्रिंट किया गया',
    'computer_generated_document' => 'यह एक कंप्यूटर-जनित दस्तावेज है और इसमें हस्ताक्षर की आवश्यकता नहीं है।',
    'additional_services' => 'अतिरिक्त सेवाएं',
    'to' => 'से',

    // Completion details
    'completion_details' => 'पूर्णता विवरण',
    'add_completion_details' => 'पूर्णता विवरण जोड़ें',
    'edit_completion_details' => 'पूर्णता विवरण संपादित करें',
    'no_completion_details' => 'अभी तक कोई पूर्णता विवरण नहीं जोड़ा गया है।',
    'completion_details_updated_successfully' => 'पूर्णता विवरण सफलतापूर्वक अपडेट किया गया।',

    'completion_miles' => 'अंतिम माइलेज',
    'completion_kilometers' => 'अंतिम किलोमीटर',
    'miles' => 'माइल',
    'kilometers' => 'किलोमीटर',
    'enter_miles' => 'अंतिम माइलेज दर्ज करें',
    'enter_kilometers' => 'अंतिम किलोमीटर दर्ज करें',
    'completion_miles_help' => 'कार वापस करते समय अंतिम माइलेज रीडिंग दर्ज करें।',
    'completion_kilometers_help' => 'कार वापस करते समय अंतिम किलोमीटर रीडिंग दर्ज करें।',

    'completion_gas_level' => 'ईंधन स्तर',
    'select_gas_level' => 'ईंधन स्तर चुनें',
    'gas_empty' => 'खाली',
    'gas_quarter' => '1/4 टैंक',
    'gas_half' => '1/2 टैंक',
    'gas_three_quarters' => '3/4 टैंक',
    'gas_full' => 'भरा हुआ टैंक',
    'completion_gas_level_help' => 'कार वापस करते समय ईंधन स्तर का चयन करें।',

    'damage_images' => 'क्षति की छवियां',
    'damage_image' => 'क्षति की छवि',
    'damage_images_help' => 'वाहन में मिली किसी भी क्षति की छवियां अपलोड करें (प्रति छवि अधिकतम 5MB)।',
    'existing_images' => 'मौजूदा छवियां',

    'completion_notes' => 'पूर्णता टिप्पणियां',
    'completion_notes_placeholder' => 'वाहन की स्थिति, क्षति, या अन्य अवलोकनों के बारे में कोई भी टिप्पणियां दर्ज करें...',
    'completion_notes_help' => 'वाहन की स्थिति या किराया पूर्णता के बारे में कोई भी अतिरिक्त टिप्पणियां जोड़ें।',

    'completed_at' => 'पूर्ण होने का समय',

    // Coupon fields
    'coupon_code' => 'कूपन कोड',
    'coupon_amount' => 'कूपन छूट राशि',
    'enter_coupon_code' => 'कूपन कोड दर्ज करें',
    'coupon_discount_amount' => 'छूट राशि',
    'applied_coupon' => 'लागू किया गया कूपन',

    // Validation messages
    'validation' => [
        'completion_miles_integer' => 'माइलेज एक वैध संख्या होनी चाहिए।',
        'completion_miles_min' => 'माइलेज कम से कम 0 होना चाहिए।',
        'completion_gas_level_invalid' => 'कृपया एक वैध ईंधन स्तर का चयन करें।',
        'damage_image_invalid' => 'अपलोड की गई फाइल एक वैध छवि होनी चाहिए।',
        'damage_image_max_size' => 'छवि का आकार 5MB से अधिक नहीं होना चाहिए।',
        'completion_notes_max' => 'टिप्पणियां 10,000 अक्षरों से अधिक नहीं होनी चाहिए।',
    ],

    // Vendor booking actions
    'approve_booking' => 'बुकिंग स्वीकृत करें',
    'cancel_booking' => 'बुकिंग रद्द करें',
    'processing' => 'प्रोसेस हो रहा है...',
    'pending_approval_notice' => 'स्वीकृति लंबित',
    'pending_approval_description' => 'यह बुकिंग आपकी स्वीकृति की प्रतीक्षा कर रही है। कृपया विवरण की समीक्षा करें और बुकिंग को स्वीकृत या रद्द करें।',
    'approve_booking_confirmation' => 'क्या आप वाकई इस बुकिंग को स्वीकृत करना चाहते हैं? ग्राहक को सूचित किया जाएगा।',
    'cancel_booking_confirmation' => 'क्या आप वाकई इस बुकिंग को रद्द करना चाहते हैं? यह क्रिया पूर्ववत नहीं की जा सकती।',
    'booking_approved_successfully' => 'बुकिंग सफलतापूर्वक स्वीकृत की गई है।',
    'booking_cancelled_successfully' => 'बुकिंग सफलतापूर्वक रद्द की गई है।',
    'cannot_approve_booking' => 'यह बुकिंग स्वीकृत नहीं की जा सकती। केवल लंबित बुकिंग स्वीकृत की जा सकती हैं।',
    'cannot_cancel_booking' => 'यह बुकिंग रद्द नहीं की जा सकती। पूर्ण या पहले से रद्द की गई बुकिंग रद्द नहीं की जा सकती।',
];

{{ header }}

<div class="bb-main-content">
    <table class="bb-box" cellpadding="0" cellspacing="0">
        <tbody>
        <tr>
            <td class="bb-content bb-pb-0" align="center">
                <table class="bb-icon bb-icon-lg bb-bg-blue" cellspacing="0" cellpadding="0">
                    <tbody>
                        <tr>
                            <td valign="middle" align="center">
                                <img src="{{ 'shopping-cart' | icon_url }}" class="bb-va-middle" width="40" height="40" alt="Icon" />
                            </td>
                        </tr>
                    </tbody>
                </table>
                <h1 class="bb-text-center bb-m-0 bb-mt-md">{{ 'plugins/car-rentals::car-rentals.email_templates.booking_notice_vendor_title' | trans }}</h1>
            </td>
        </tr>
        <tr>
            <td class="bb-content">
                <p>{{ 'plugins/car-rentals::car-rentals.email_templates.booking_notice_vendor_greeting' | trans({'vendor_name': vendor_name}) }}</p>
                <div>{{ 'plugins/car-rentals::car-rentals.email_templates.booking_notice_vendor_message' | trans }}</div>
            </td>
        </tr>
        <tr>
            <td class="bb-content bb-pt-0">
                <table class="bb-row bb-mb-md" cellpadding="0" cellspacing="0">
                    <tbody>
                        <tr>
                            <td class="bb-bb-col">
                                <h4>{{ 'plugins/car-rentals::car-rentals.email_templates.customer_information' | trans }}</h4>
                                <div>{{ 'plugins/car-rentals::car-rentals.email_templates.field_name' | trans }} <strong>{{ customer_name }}</strong></div>
                                {% if customer_phone %}
                                    <div>{{ 'plugins/car-rentals::car-rentals.email_templates.field_phone' | trans }} <strong>{{ customer_phone }}</strong></div>
                                {% endif %}
                                {% if customer_email %}
                                    <div>{{ 'plugins/car-rentals::car-rentals.email_templates.field_email' | trans }} <strong>{{ customer_email }}</strong></div>
                                {% endif %}
                            </td>
                        </tr>
                    </tbody>
                </table>

                <table class="bb-row bb-mb-md" cellpadding="0" cellspacing="0">
                    <tbody>
                    <tr>
                        <td class="bb-bb-col">
                            <h4>{{ 'plugins/car-rentals::car-rentals.email_templates.booking_information' | trans }}</h4>
                            <div>{{ 'plugins/car-rentals::car-rentals.email_templates.field_car_model' | trans }} <strong>{{ car_name }}</strong></div>
                            <div>{{ 'plugins/car-rentals::car-rentals.email_templates.field_start_date' | trans }} <strong>{{ rental_start_date }}</strong></div>
                            <div>{{ 'plugins/car-rentals::car-rentals.email_templates.field_end_date' | trans }} <strong>{{ rental_end_date }}</strong></div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </td>
        </tr>
        <tr>
            <td class="bb-content bb-pt-0">
                {% if note %}
                    <div>{{ 'plugins/car-rentals::car-rentals.email_templates.field_note' | trans }} {{ note }}</div>
                {% endif %}
            </td>
        </tr>
        <tr>
            <td class="bb-content bb-border-top">
                <table class="bb-row bb-mb-md" cellpadding="0" cellspacing="0">
                    <tbody>
                        <tr>
                            <td class="bb-bb-col">
                                <h4 class="bb-m-0">{{ 'plugins/car-rentals::car-rentals.email_templates.field_order_number' | trans }}</h4>
                                <div>{{ booking_code }}</div>
                            </td>
                        </tr>
                    </tbody>
                </table>
                {% if payment_method %}
                    <table class="bb-row bb-mb-md" cellpadding="0" cellspacing="0">
                        <tbody>
                            <tr>
                            <td class="bb-col">
                                <h4 class="bb-m-0">{{ 'plugins/car-rentals::car-rentals.email_templates.field_payment_method' | trans }}</h4>
                                <div>
                                    {{ payment_method }}
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                {% endif %}
            </td>
        </tr>
        </tbody>
    </table>
</div>

{{ footer }}

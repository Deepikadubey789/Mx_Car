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
                <h1 class="bb-text-center bb-m-0 bb-mt-md">{{ 'plugins/car-rentals::car-rentals.email_templates.booking_status_updated' | trans }}</h1>
            </td>
        </tr>
        <tr>
            <td class="bb-content">
                <p>
                    <strong>{{ 'plugins/car-rentals::car-rentals.email_templates.hello_thanks_for_booking' | trans({'site_title': site_title}) }}</strong>
                </p>
                <p>
                    {{ 'plugins/car-rentals::car-rentals.email_templates.booking_status_change_message' | trans }}
                </p>
            </td>
        </tr>
        <tr>
            <td class="bb-content bb-pt-0">
                <table class="bb-row bb-mb-md" cellpadding="0" cellspacing="0">
                    <tbody>
                    <tr>
                        <td class="bb-bb-col">
                            <h4>{{ 'plugins/car-rentals::car-rentals.email_templates.customer_information' | trans }}</h4>
                            <div>{{ 'plugins/car-rentals::car-rentals.email_templates.name' | trans }}: <strong>{{ customer_name }}</strong></div>
                            {% if customer_phone %}
                            <div>{{ 'plugins/car-rentals::car-rentals.email_templates.phone' | trans }}: <strong>{{ customer_phone }}</strong></div>
                            {% endif %}
                            {% if customer_email %}
                            <div>{{ 'plugins/car-rentals::car-rentals.email_templates.email' | trans }}: <strong>{{ customer_email }}</strong></div>
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
                            <div>{{ 'plugins/car-rentals::car-rentals.email_templates.car_model' | trans }}: <strong>{{ car_name }}</strong></div>
                            <div>{{ 'plugins/car-rentals::car-rentals.email_templates.start_date' | trans }}: <strong>{{ rental_start_date }}</strong></div>
                            <div>{{ 'plugins/car-rentals::car-rentals.email_templates.end_date' | trans }}: <strong>{{ rental_end_date }}</strong></div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </td>
        </tr>
        <tr>
            <td class="bb-content bb-pt-0">
                {% if order_note %}
                <div>{{ 'plugins/car-rentals::car-rentals.email_templates.note' | trans }}: {{ order_note }}</div>
                {% endif %}
            </td>
        </tr>
        <tr>
            <td class="bb-content bb-border-top">
                <table class="bb-row bb-mb-md" cellpadding="0" cellspacing="0">
                    <tbody>
                    <tr>
                        <td class="bb-bb-col">
                            <h4 class="bb-m-0">{{ 'plugins/car-rentals::car-rentals.email_templates.order_number' | trans }}</h4>
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
                            <h4 class="bb-m-0">{{ 'plugins/car-rentals::car-rentals.email_templates.payment_method' | trans }}</h4>
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

{{ header }}

<div class="bb-main-content">
    <table class="bb-box" cellpadding="0" cellspacing="0">
        <tbody>
        <tr>
            <td class="bb-content bb-pb-0" align="center">
                <table class="bb-icon bb-icon-lg bb-bg-red" cellspacing="0" cellpadding="0">
                    <tbody>
                        <tr>
                            <td valign="middle" align="center">
                                <img src="{{ 'shopping-cart-x' | icon_url }}" class="bb-va-middle" width="40" height="40" alt="Icon" />
                            </td>
                        </tr>
                    </tbody>
                </table>
                <h1 class="bb-text-center bb-m-0 bb-mt-md">{{ 'plugins/car-rentals::car-rentals.email_templates.car_rejected_title' | trans }}</h1>
            </td>
        </tr>
        <tr>
            <td class="bb-content">
                <p>{{ 'plugins/car-rentals::car-rentals.email_templates.hello' | trans }} <strong>{{ author_name }}</strong>,</p>
                <p>{{ 'plugins/car-rentals::car-rentals.email_templates.car_rejected_message' | trans({'car_name': car_name, 'site_title': site_title}) }}</p>
            </td>
        </tr>
        <tr>
            <td class="bb-content bb-pt-0">
                <table class="bb-row bb-mb-md" cellpadding="0" cellspacing="0">
                    <tbody>
                        <tr>
                            <td class="bb-bb-col">
                                <h4>{{ 'plugins/car-rentals::car-rentals.email_templates.car_information' | trans }}</h4>
                                <div>{{ 'plugins/car-rentals::car-rentals.email_templates.field_car_name' | trans }} <strong>{{ car_name }}</strong></div>
                                <div>{{ 'plugins/car-rentals::car-rentals.email_templates.field_author' | trans }} <strong>{{ author_name }}</strong></div>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <table class="bb-row bb-mb-md" cellpadding="0" cellspacing="0">
                    <tbody>
                        <tr>
                            <td class="bb-bb-col">
                                <h4>{{ 'plugins/car-rentals::car-rentals.email_templates.rejection_details' | trans }}</h4>
                                <div>{{ 'plugins/car-rentals::car-rentals.email_templates.rejection_reason' | trans }} <strong>{{ reason }}</strong></div>
                                <div>{{ 'plugins/car-rentals::car-rentals.email_templates.contact_support' | trans({'site_email': site_email}) }}</div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
        <tr>
            <td class="bb-content bb-border-top">
                <table class="bb-row bb-mb-md" cellpadding="0" cellspacing="0">
                    <tbody>
                        <tr>
                            <td class="bb-bb-col">
                                <h4 class="bb-m-0">{{ 'plugins/car-rentals::car-rentals.email_templates.actions' | trans }}</h4>
                                <div><a href="{{ car_link }}">{{ 'plugins/car-rentals::car-rentals.email_templates.view_car' | trans }}</a></div>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <p>{{ 'plugins/car-rentals::car-rentals.email_templates.regards' | trans }},</p>
                <p><strong>{{ site_title }}</strong></p>
            </td>
        </tr>
        </tbody>
    </table>
</div>

{{ footer }}

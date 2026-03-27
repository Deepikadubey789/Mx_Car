{{ header }}

<div class="bb-main-content">
    <table class="bb-box" cellpadding="0" cellspacing="0">
        <tbody>
        <tr>
            <td class="bb-content bb-pb-0" align="center">
                <table class="bb-icon bb-icon-lg bb-bg-orange" cellspacing="0" cellpadding="0">
                    <tbody>
                        <tr>
                            <td valign="middle" align="center">
                                <img src="{{ 'hourglass' | icon_url }}" class="bb-va-middle" width="40" height="40" alt="Icon" />
                            </td>
                        </tr>
                    </tbody>
                </table>
                <h1 class="bb-text-center bb-m-0 bb-mt-md">{{ 'plugins/car-rentals::car-rentals.email_templates.new_car_pending_title' | trans }}</h1>
            </td>
        </tr>
        <tr>
            <td class="bb-content">
                <p>{{ 'plugins/car-rentals::car-rentals.email_templates.hi_admin' | trans }}</p>
                <p>{{ 'plugins/car-rentals::car-rentals.email_templates.new_car_pending_approval' | trans({'post_author': post_author, 'post_name': post_name}) }}</p>
            </td>
        </tr>
        <tr>
            <td class="bb-content bb-pt-0">
                <table class="bb-row bb-mb-md" cellpadding="0" cellspacing="0">
                    <tbody>
                        <tr>
                            <td class="bb-bb-col">
                                <h4>{{ 'plugins/car-rentals::car-rentals.email_templates.car_information' | trans }}</h4>
                                <div>{{ 'plugins/car-rentals::car-rentals.email_templates.field_car_name' | trans }} <strong>{{ post_name }}</strong></div>
                                <div>{{ 'plugins/car-rentals::car-rentals.email_templates.field_author' | trans }} <strong>{{ post_author }}</strong></div>
                                <div>{{ 'plugins/car-rentals::car-rentals.email_templates.field_status' | trans }} <strong>{{ 'plugins/car-rentals::car-rentals.email_templates.pending_approval' | trans }}</strong></div>
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
                                <div>{{ 'plugins/car-rentals::car-rentals.email_templates.review_car_admin' | trans }}</div>
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

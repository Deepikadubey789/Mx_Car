{{ header }}

<div class="bb-main-content">
    <table class="bb-box" cellpadding="0" cellspacing="0">
        <tbody>
        <tr>
            <td class="bb-content bb-pb-0" align="center">
                <table class="bb-icon bb-icon-lg bb-bg-green" cellspacing="0" cellpadding="0">
                    <tbody>
                        <tr>
                            <td valign="middle" align="center">
                                <img src="{{ 'check' | icon_url }}" class="bb-va-middle" width="40" height="40" alt="Icon" />
                            </td>
                        </tr>
                    </tbody>
                </table>
                <h1 class="bb-text-center bb-m-0 bb-mt-md">{{ 'plugins/car-rentals::car-rentals.email_templates.vendor_upgrade_title' | trans }}</h1>
            </td>
        </tr>
        <tr>
            <td class="bb-content">
                <p>{{ 'plugins/car-rentals::car-rentals.email_templates.hello' | trans }} <strong>{{ customer_name }}</strong>,</p>
                <p>{{ 'plugins/car-rentals::car-rentals.email_templates.vendor_upgrade_congratulations' | trans }}</p>
                <p>{{ 'plugins/car-rentals::car-rentals.email_templates.vendor_upgrade_message' | trans({'site_title': site_title}) }}</p>
            </td>
        </tr>
        <tr>
            <td class="bb-content bb-pt-0">
                <table class="bb-row bb-mb-md" cellpadding="0" cellspacing="0">
                    <tbody>
                        <tr>
                            <td class="bb-bb-col">
                                <h4>{{ 'plugins/car-rentals::car-rentals.email_templates.vendor_benefits_title' | trans }}</h4>
                                <ul>
                                    <li>{{ 'plugins/car-rentals::car-rentals.email_templates.vendor_benefit_1' | trans }}</li>
                                    <li>{{ 'plugins/car-rentals::car-rentals.email_templates.vendor_benefit_2' | trans }}</li>
                                    <li>{{ 'plugins/car-rentals::car-rentals.email_templates.vendor_benefit_3' | trans }}</li>
                                    <li>{{ 'plugins/car-rentals::car-rentals.email_templates.vendor_benefit_4' | trans }}</li>
                                </ul>
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
                                <h4 class="bb-m-0">{{ 'plugins/car-rentals::car-rentals.email_templates.next_steps' | trans }}</h4>
                                <p>{{ 'plugins/car-rentals::car-rentals.email_templates.vendor_next_steps_message' | trans }}</p>
                                <div><a href="{{ dashboard_link }}">{{ 'plugins/car-rentals::car-rentals.email_templates.go_to_dashboard' | trans }}</a></div>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <p>{{ 'plugins/car-rentals::car-rentals.email_templates.vendor_support_message' | trans }}</p>
                <p>{{ 'plugins/car-rentals::car-rentals.email_templates.regards' | trans }},</p>
                <p><strong>{{ site_title }}</strong></p>
            </td>
        </tr>
        </tbody>
    </table>
</div>

{{ footer }}
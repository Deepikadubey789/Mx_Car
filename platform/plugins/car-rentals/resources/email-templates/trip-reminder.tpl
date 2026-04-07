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
                                <img src="{{ 'car' | icon_url }}" class="bb-va-middle" width="40" height="40" alt="Icon" />
                            </td>
                        </tr>
                    </tbody>
                </table>
                <h1 class="bb-text-center bb-m-0 bb-mt-md">Your Trip is Tomorrow!</h1>
            </td>
        </tr>
        <tr>
            <td class="bb-content">
                <p>Dear <strong>{{ customer_name }}</strong>,</p>
                <p>This is a friendly reminder that your car rental trip starts <strong>tomorrow</strong>. Please be ready for pickup!</p>
            </td>
        </tr>
        <tr>
            <td class="bb-content bb-pt-0">
                <table class="bb-row bb-mb-md" cellpadding="0" cellspacing="0">
                    <tbody>
                        <tr>
                            <td class="bb-bb-col">
                                <h4>Booking Details</h4>
                                <div>Booking #: <strong>{{ booking_code }}</strong></div>
                                <div>Car: <strong>{{ car_name }}</strong></div>
                                <div>Pickup Date: <strong>{{ rental_start_date }}</strong></div>
                                <div>Return Date: <strong>{{ rental_end_date }}</strong></div>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <table class="bb-row bb-mb-md" cellpadding="0" cellspacing="0">
                    <tbody>
                        <tr>
                            <td class="bb-bb-col">
                                <h4>Customer Details</h4>
                                <div>Name: <strong>{{ customer_name }}</strong></div>
                                {% if customer_phone %}
                                    <div>Phone: <strong>{{ customer_phone }}</strong></div>
                                {% endif %}
                                {% if customer_email %}
                                    <div>Email: <strong>{{ customer_email }}</strong></div>
                                {% endif %}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
        </tbody>
    </table>
</div>

{{ footer }}

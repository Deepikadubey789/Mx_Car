<!doctype html>
<html {{ html_attributes }}>
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ 'plugins/car-rentals::invoice.heading'|trans }} - #{{ invoice.code }}</title>
    {% if settings.using_custom_font_for_invoice %}
        <link href="https://fonts.googleapis.com/css2?family={{ settings.custom_font_family | urlencode }}:wght@400;500;600;700;900&display=swap" rel="stylesheet">
    {% endif %}
    <style>
        body {
            font-size: 15px;
            font-family: '{{ settings.font_family }}', Arial, sans-serif !important;
        }

        table {
            border-collapse: collapse;
            width: 100%
        }

        table tr td {
            padding: 0
        }

        table tr td:last-child {
            text-align: right
        }

        .bold, strong, b, .total, .stamp {
            font-weight: 700
        }

        .right {
            text-align: right
        }

        .large {
            font-size: 1.75em
        }

        .total {
            color: #fb7578;
        }

        .logo-container {
            margin: 15px 0 30px
        }

        .invoice-info-container {
            font-size: .875em
        }

        .invoice-info-container td {
            padding: 4px 0
        }

        .line-items-container {
            font-size: .875em;
            margin: 40px 0
        }

        .line-items-container th {
            border-bottom: 2px solid #ddd;
            color: #999;
            font-size: .75em;
            padding: 10px 0 15px;
            text-align: left;
            text-transform: uppercase;
        }

        .line-items-container th:last-child {
            text-align: right;
        }

        .line-items-container tbody tr:first-child td {
            padding-top: 25px;
        }

        .line-items-container.has-bottom-border tbody tr:last-child td {
            border-bottom: 2px solid #ddd;
            padding-bottom: 25px;
        }

        .line-items-container th.heading-quantity {
            width: 50px
        }

        .line-items-container th.heading-price {
            text-align: right;
            width: 100px
        }

        .line-items-container th.heading-subtotal {
            width: 100px
        }

        .payment-info {
            font-size: .875em;
            line-height: 1.5;
            width: 38%
        }

        small {
            font-size: 80%
        }

        .stamp {
            border: 2px solid #555;
            color: #555;
            display: inline-block;
            font-size: 18px;
            left: 30%;
            line-height: 1;
            opacity: .5;
            padding: .3rem .75rem;
            position: fixed;
            text-transform: uppercase;
            top: 40%;
            transform: rotate(-14deg)
        }

        .is-failed {
            border-color: #d23;
            color: #d23
        }

        .is-completed {
            border-color: #0a9928;
            color: #0a9928
        }

        /* Compact layout for single page */
        @media print {
            body {
                margin: 0;
                padding: 20px;
            }
            .line-items-container {
                margin: 25px 0;
            }
            .logo-container {
                margin: 10px 0 20px;
            }
        }

        .compact-info {
            font-size: 0.85em;
            line-height: 1.3;
        }

        /* RTL Support */
        body[dir=rtl] {
            direction: rtl;
        }

        body[dir=rtl] .right {
            text-align: left;
        }

        body[dir=rtl] table tr td:last-child {
            text-align: left;
        }

        body[dir=rtl] .line-items-container th.heading-price {
            text-align: left;
        }

        body[dir=rtl] .line-items-container th:last-child {
            text-align: left;
        }

        body[dir=rtl] .line-items-container th {
            text-align: right;
        }

        body[dir=rtl] .invoice-info-container td:last-child {
            text-align: left;
        }

        body[dir=rtl] .invoice-info-container td:first-child {
            text-align: right;
        }

        body[dir=rtl] .note span {
            margin-right: 10px;
            margin-left: 0;
        }

        body[dir=rtl] .note {
            border-right: 3px solid #007bff;
            border-left: none;
        }
    </style>

    {{ invoice_header_filter | raw }}
</head>
<body {{ body_attributes }}>
{% if settings.enable_invoice_stamp %}
    {% if invoice.status == 'canceled' %}
        <span class="stamp is-failed">
            {{ invoice.status }}
        </span>
    {% else %}
        {% if invoice.payment == 'canceled' %}
            <span class="stamp {% if invoice.payment.status == 'completed' %} is-completed {% else %} is-failed {% endif %}">
                {{ invoice.payment.status }}
            </span>
        {% endif %}
    {% endif %}
{% endif %}

<table class="invoice-info-container">
    <tr>
        <td>
            <p>
                <strong>{{ 'plugins/car-rentals::invoice.heading'|trans }}</strong>: #{{ invoice.code }}
            </p>
            {% if booking and booking.booking_number %}
            <p>
                <strong>{{ 'plugins/car-rentals::invoice.booking_number'|trans }}</strong>: {{ booking.booking_number }}
            </p>
            {% endif %}
            <div class="logo-container">
                <img src="{{ logo_full_path }}" style="max-height: 40px;" alt="{{ settings.company_name_for_invoicing }}">
            </div>
        </td>
        <td>
            <p>
                <strong>{{ invoice.created_at|date(settings.date_format) }}</strong>
            </p>
            <div class="logo-container">
                <img src="{{ customer.avatar_url }}" style="max-height: 40px;" alt="{{ invoice.customer_name }}">
            </div>
        </td>
    </tr>
</table>

<table class="invoice-info-container compact-info">
    <tr>
        <td>
            <p>{{ settings.company_name_for_invoicing }}</p>
            <p>{{ settings.company_address_for_invoicing }}</p>
            <p>{{ settings.company_email_for_invoicing }}</p>
            <p>{{ settings.company_phone_for_invoicing }}</p>
        </td>
        <td>
            <p>{{ invoice.customer_name }}</p>
            <p>{{ invoice.customer_email }}</p>
            <p>{{ invoice.customer_phone }}</p>
            {% if booking and booking.car %}
            <p style="margin-top: 10px;"><strong>{{ 'plugins/car-rentals::invoice.rental_start_date'|trans }}:</strong> {{ booking.car.rental_start_date|date(settings.date_format) }}</p>
            <p><strong>{{ 'plugins/car-rentals::invoice.rental_end_date'|trans }}:</strong> {{ booking.car.rental_end_date|date(settings.date_format) }}</p>
            {% endif %}
        </td>
    </tr>
</table>

{% if booking and booking.note %}
<table class="invoice-info-container" style="margin: 15px 0;">
    <tr>
        <td class="note" style="padding: 10px; background-color: #f8f9fa; border-left: 3px solid #007bff;">
            <strong>{{ 'plugins/car-rentals::invoice.note'|trans }}:</strong>
            <span style="margin-left: 10px;">{{ booking.note }}</span>
        </td>
    </tr>
</table>
{% endif %}

<table class="line-items-container">
    <thead>
    <tr>
        <th class="heading-description">{{ 'plugins/car-rentals::invoice.item.name'|trans }}</th>
        <th class="heading-quantity">{{ 'plugins/car-rentals::invoice.item.qty'|trans }}</th>
        <th class="heading-price">{{ 'plugins/car-rentals::invoice.amount'|trans }}</th>
        <th class="heading-subtotal">{{ 'plugins/car-rentals::invoice.total_amount'|trans }}</th>
    </tr>
    </thead>
    <tbody>
    {% for item in invoice.items %}
        <tr class="invoice-item">
            <td>
                <p>{{ item.name }}</p>
                {% if item.description %}
                <small>{{ item.description }}</small>
                {% endif %}
            </td>
            <td>{{ item.qty }}</td>
            <td class="right">{{ item.sub_total|price_format }}</td>
            <td>{{ (item.amount * item.qty)|price_format }}</td>
        </tr>
    {% endfor %}

    {% if (invoice.amount != invoice.sub_total) %}
        <tr>
            <td colspan="3" class="right">
                {{ 'plugins/car-rentals::invoice.sub_total'|trans }}
            </td>
            <td class="bold">
                {{ invoice.sub_total|price_format }}
            </td>
        </tr>
    {% endif %}

    {% if invoice.discount_amount %}
    <tr>
        <td colspan="3" class="right">
            {{ 'plugins/car-rentals::invoice.discount_amount'|trans }}
        </td>
        <td class="bold">
            {{ invoice.discount_amount|price_format }}
        </td>
    </tr>
    {% endif %}

    {% if invoice.tax_amount %}
        <tr>
            <td colspan="3" class="right">
                {{ 'plugins/car-rentals::invoice.tax'|trans }}
            </td>
            <td class="bold">
                {{ invoice.tax_amount|price_format }}
            </td>
        </tr>
    {% endif %}

    {% if invoice.shipping_amount %}
        <tr>
            <td colspan="3" class="right">
                {{ 'plugins/car-rentals::invoice.shipping_fee'|trans }}
            </td>
            <td class="bold">
                {{ invoice.shipping_amount|price_format }}
            </td>
        </tr>
    {% endif %}

    <tr>
        <td colspan="3" class="right">
            {{ 'plugins/car-rentals::invoice.total_amount'|trans }}
        </td>
        <td class="bold">
            {{ invoice.amount|price_format }}
        </td>
    </tr>
    </tbody>
</table>

<table class="line-items-container" style="margin: 30px 0 20px 0;">
    <thead>
    <tr>
        <th style="background-color: #f8f9fa; padding: 15px;">{{ 'plugins/car-rentals::invoice.payment_info'|trans }}</th>
        <th style="background-color: #f8f9fa; text-align: right; padding: 15px;">{{ 'plugins/car-rentals::invoice.total_amount'|trans }}</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td class="payment-info" style="padding: 15px; vertical-align: top; background-color: #f8f9fa; min-width: 400px;">
            {% if payment_method %}
                <div style="margin-bottom: 5px;">
                    {{ 'plugins/car-rentals::invoice.payment_method'|trans }}: <strong>{{ payment_method }}</strong>
                </div>
            {% endif %}
            {% if payment_status %}
                <div style="margin-bottom: 5px;">
                    {{ 'plugins/car-rentals::invoice.payment_status'|trans }}: <strong>{{ payment_status }}</strong>
                </div>
            {% endif %}
            {% if payment_id %}
                <div style="margin-bottom: 5px;">
                    {{ 'plugins/car-rentals::invoice.payment_id'|trans }}: <strong>{{ payment_id }}</strong>
                </div>
            {% endif %}
            {% if payment_description %}
                <div style="margin-bottom: 5px;">
                    {{ 'plugins/car-rentals::invoice.payment_info'|trans }}: <strong>{{ payment_description|raw }}</strong>
                </div>
            {% endif %}
        </td>
        <td class="large total" style="padding: 15px; vertical-align: middle; text-align: right; background-color: #f8f9fa;">{{ invoice.amount|price_format }}</td>
    </tr>
    </tbody>
</table>
{{ car_rentals_invoice_footer | raw }}
</body>
</html>

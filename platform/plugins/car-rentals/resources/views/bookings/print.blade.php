<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" @if (BaseHelper::isRtlEnabled()) dir="rtl" @endif>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('Booking Information') }} - {{ $booking->booking_number }}</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            background: #fff;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 15px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #007bff;
            padding-bottom: 15px;
        }

        .header h1 {
            color: #007bff;
            font-size: 24px;
            margin-bottom: 8px;
        }

        .header p {
            color: #666;
            font-size: 14px;
        }

        .section {
            margin-bottom: 18px;
        }

        .section-title {
            background: #f8f9fa;
            padding: 8px 12px;
            border-left: 4px solid #007bff;
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 12px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 10px;
            margin-bottom: 15px;
        }

        .info-item {
            display: flex;
            padding: 6px 0;
            border-bottom: 1px solid #eee;
        }

        .info-label {
            font-weight: bold;
            min-width: 100px;
            color: #555;
            font-size: 11px;
        }

        .info-value {
            flex: 1;
            color: #333;
            font-size: 11px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        .table th,
        .table td {
            padding: 8px;
            text-align: left;
            border: 1px solid #ddd;
            font-size: 11px;
        }

        .table th {
            background: #f8f9fa;
            font-weight: bold;
            color: #555;
        }

        .table tr:nth-child(even) {
            background: #f9f9f9;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .car-image {
            max-width: 80px;
            height: auto;
            border-radius: 4px;
        }

        .status {
            padding: 3px 6px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
        }

        .status.completed {
            background: #d4edda;
            color: #155724;
        }

        .status.pending {
            background: #fff3cd;
            color: #856404;
        }

        .status.cancelled {
            background: #f8d7da;
            color: #721c24;
        }

        .total-section {
            background: #f8f9fa;
            padding: 12px;
            border-radius: 4px;
            margin-top: 15px;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 4px 0;
            font-size: 11px;
        }

        .total-row.final {
            border-top: 2px solid #007bff;
            padding-top: 8px;
            margin-top: 8px;
            font-weight: bold;
            font-size: 13px;
        }

        .print-info {
            margin-top: 20px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
            text-align: center;
            color: #666;
            font-size: 10px;
        }

        /* Compact layout for better page fitting */
        .compact-layout {
            page-break-inside: avoid;
        }

        .no-break {
            page-break-inside: avoid;
        }

        @media print {
            body {
                font-size: 11px;
                line-height: 1.3;
            }

            .container {
                padding: 0;
                max-width: none;
            }

            .header {
                margin-bottom: 15px;
                padding-bottom: 10px;
            }

            .header h1 {
                font-size: 20px;
                margin-bottom: 5px;
            }

            .section {
                margin-bottom: 12px;
                page-break-inside: avoid;
            }

            .section-title {
                padding: 6px 10px;
                font-size: 12px;
                margin-bottom: 8px;
            }

            .info-grid {
                gap: 8px;
                margin-bottom: 10px;
            }

            .info-item {
                padding: 4px 0;
            }

            .table th,
            .table td {
                padding: 6px;
                font-size: 10px;
            }

            .car-image {
                max-width: 60px;
            }

            .total-section {
                padding: 10px;
                margin-top: 10px;
            }

            .print-info {
                margin-top: 15px;
                padding-top: 10px;
                page-break-inside: avoid;
            }
        }

        @media (max-width: 768px) {
            .info-grid {
                grid-template-columns: 1fr;
            }

            .table {
                font-size: 10px;
            }

            .table th,
            .table td {
                padding: 6px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>{{ __('Booking Information') }}</h1>
            <p>{{ __('Booking Number') }}: <strong>{{ $booking->booking_number }}</strong></p>
        </div>

        <!-- Customer Information -->
        <div class="section no-break">
            <div class="section-title">{{ __('Customer Information') }}</div>
            <div class="info-grid">
                <div class="info-item">
                    <span class="info-label">{{ __('Full Name') }}:</span>
                    <span class="info-value">{{ $booking->customer_name }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">{{ __('Email') }}:</span>
                    <span class="info-value">{{ $booking->customer_email }}</span>
                </div>
                @if($booking->customer_phone)
                <div class="info-item">
                    <span class="info-label">{{ __('Phone') }}:</span>
                    <span class="info-value">{{ $booking->customer_phone }}</span>
                </div>
                @endif
                <div class="info-item">
                    <span class="info-label">{{ __('Booking Date') }}:</span>
                    <span class="info-value">{{ $booking->created_at->format('F d, Y') }}</span>
                </div>
            </div>
        </div>

        <!-- Booking Details -->
        <div class="section no-break">
            <div class="section-title">{{ trans('plugins/car-rentals::booking.booking_details') }}</div>
            <div class="info-grid">
                <div class="info-item">
                    <span class="info-label">{{ __('Rental Start Date') }}:</span>
                    <span class="info-value">{{ $booking->car ? $booking->car->rental_start_date_formatted : __('N/A') }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">{{ __('Rental End Date') }}:</span>
                    <span class="info-value">{{ $booking->car ? $booking->car->rental_end_date_formatted : __('N/A') }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">{{ __('Status') }}:</span>
                    <span class="info-value">
                        <span class="status {{ strtolower($booking->status->getValue()) }}">
                            {{ $booking->status->label() }}
                        </span>
                    </span>
                </div>
                @if($booking->note)
                <div class="info-item">
                    <span class="info-label">{{ __('Note') }}:</span>
                    <span class="info-value">{{ $booking->note }}</span>
                </div>
                @endif
            </div>
        </div>

        <!-- Car Information -->
        @if($booking->car)
        <div class="section">
            <div class="section-title">{{ __('Car Information') }}</div>
            <table class="table">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 100px;">{{ __('Image') }}</th>
                        <th>{{ __('Car Name') }}</th>
                        <th class="text-center">{{ trans('plugins/car-rentals::booking.rental_period') }}</th>
                        <th class="text-right">{{ __('Price') }}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="text-center">
                            @if($booking->car->car)
                                <img src="{{ RvMedia::getImageUrl($booking->car->car->image, 'thumb', false, RvMedia::getDefaultImage()) }}"
                                     alt="{{ $booking->car->car_name }}" class="car-image">
                            @else
                                <img src="{{ RvMedia::getImageUrl($booking->car->car_image, 'thumb', false, RvMedia::getDefaultImage()) }}"
                                     alt="{{ $booking->car->car_name }}" class="car-image">
                            @endif
                        </td>
                        <td>{{ $booking->car->car_name }}</td>
                        <td class="text-center">
                            {{ $booking->car->rental_start_date_formatted }}
                            <small>{{ trans('plugins/car-rentals::booking.to') }}</small>
                            {{ $booking->car->rental_end_date_formatted }}
                        </td>
                        <td class="text-right"><strong>{{ format_price($booking->car->price, $booking->currency_id) }}</strong></td>
                    </tr>
                </tbody>
            </table>
        </div>
        @endif

        <!-- Services (if any) -->
        @if($booking->services && $booking->services->count() > 0)
        <div class="section">
            <div class="section-title">{{ trans('plugins/car-rentals::booking.additional_services') }}</div>
            <table class="table">
                <thead>
                    <tr>
                        <th>{{ __('Service Name') }}</th>
                        <th class="text-right">{{ __('Price') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($booking->services as $service)
                    <tr>
                        <td>{{ $service->name }}</td>
                        <td class="text-right"><strong>{{ format_price($service->price) }}</strong></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        <!-- Payment Information -->
        @if(is_plugin_active('payment') && $booking->payment)
        <div class="section">
            <div class="section-title">{{ __('Payment Information') }}</div>
            <div class="info-grid">
                <div class="info-item">
                    <span class="info-label">{{ __('Payment Method') }}:</span>
                    <span class="info-value">{{ $booking->payment->payment_channel->label() }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">{{ __('Payment Status') }}:</span>
                    <span class="info-value">
                        <span class="status {{ strtolower($booking->payment->status->getValue()) }}">
                            {{ $booking->payment->status->label() }}
                        </span>
                    </span>
                </div>
                @if($booking->payment->charge_id)
                <div class="info-item">
                    <span class="info-label">{{ __('Payment ID') }}:</span>
                    <span class="info-value">{{ $booking->payment->charge_id }}</span>
                </div>
                @endif
            </div>
        </div>
        @endif

        <!-- Total Summary -->
        <div class="section no-break">
            <div class="section-title">{{ trans('plugins/car-rentals::booking.booking_summary') }}</div>
            <div class="total-section">
                <div class="total-row">
                    <span>{{ __('Sub Total') }}:</span>
                    <span>{{ format_price($booking->sub_total) }}</span>
                </div>
                @if($booking->coupon_amount > 0)
                <div class="total-row">
                    <span>{{ __('Discount Amount') }}:</span>
                    <span>-{{ format_price($booking->coupon_amount) }}</span>
                </div>
                @endif
                @if($booking->tax_amount > 0)
                <div class="total-row">
                    <span>{{ __('Tax Amount') }}:</span>
                    <span>{{ format_price($booking->tax_amount) }}</span>
                </div>
                @endif
                <div class="total-row final">
                    <span>{{ __('Total Amount') }}:</span>
                    <span>{{ format_price($booking->amount) }}</span>
                </div>
            </div>
        </div>

        <!-- Print Information -->
        <div class="print-info">
            <p>{{ trans('plugins/car-rentals::booking.printed_on') }}: {{ \Carbon\Carbon::now()->format('F d, Y') }}</p>
            <p>{{ trans('plugins/car-rentals::booking.computer_generated_document') }}</p>
        </div>
    </div>

    <script>
        // Auto-print when page loads
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>

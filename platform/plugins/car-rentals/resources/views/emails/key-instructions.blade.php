<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; color: #333; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; }
        .header { background: #f59e0b; color: white; padding: 20px; border-radius: 8px 8px 0 0; }
        .body { background: #f9f9f9; padding: 25px; border: 1px solid #ddd; }
        .instructions-box { background: #fff3cd; border: 1px solid #f59e0b; border-radius: 6px; padding: 15px; margin: 15px 0; }
        .footer { background: #333; color: #aaa; padding: 15px; text-align: center; font-size: 12px; border-radius: 0 0 8px 8px; }
        .booking-info { background: white; border-radius: 6px; padding: 15px; margin: 15px 0; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h2>🔑 Your Car Pickup Instructions</h2>
        <p style="margin:0">Booking #{{ $booking->booking_number }}</p>
    </div>
    <div class="body">
        <p>Dear <strong>{{ $booking->customer_name }}</strong>,</p>
        <p>Your booking is confirmed! Here are your pickup instructions:</p>

        <div class="booking-info">
            <strong>📅 Pickup Date:</strong> {{ $booking->start_date?->format('M d, Y') }}<br>
            <strong>🚗 Car:</strong> {{ $booking->car->car->name ?? 'N/A' }}<br>
            <strong>📍 Location:</strong> {{ $booking->car->car->address ?? 'N/A' }}
        </div>

        <div class="instructions-box">
            <strong>🔑 Key Instructions:</strong><br><br>
            {!! nl2br(e($instructions)) !!}
        </div>

        <p>If you have any questions, please contact us immediately.</p>
        <p>Thank you for choosing us!</p>
    </div>
    <div class="footer">
        This is an automated email. Please do not reply directly to this email.
    </div>
</div>
</body>
</html>
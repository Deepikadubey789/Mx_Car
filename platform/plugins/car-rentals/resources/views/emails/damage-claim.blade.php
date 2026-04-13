<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #d9534f; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; background: #f9f9f9; }
        .amount { font-size: 24px; color: #d9534f; font-weight: bold; }
        .btn { display: inline-block; padding: 10px 20px; margin: 10px 5px; border-radius: 5px; text-decoration: none; color: white; }
        .btn-accept { background: #5cb85c; }
        .btn-dispute { background: #d9534f; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Damage Claim Raised</h2>
        </div>
        <div class="content">
            <p>Dear {{ $booking->customer_name }},</p>
            <p>A damage claim has been raised for your booking <strong>#{{ $booking->booking_number }}</strong>.</p>
            <p>Damage Amount: <span class="amount">${{ number_format($booking->damage_amount, 2) }}</span></p>
            @if($booking->completion_notes)
                <p>Notes: {{ $booking->completion_notes }}</p>
            @endif
            <p>Please review and respond:</p>
            <a href="{{ $acceptUrl }}" class="btn btn-accept">Accept & Pay</a>
            <a href="{{ $disputeUrl }}" class="btn btn-dispute">Dispute Claim</a>
            <p style="margin-top: 20px; font-size: 12px; color: #999;">
                If buttons don't work, copy these links:<br>
                Accept: {{ $acceptUrl }}<br>
                Dispute: {{ $disputeUrl }}
            </p>
        </div>
    </div>
</body>
</html>
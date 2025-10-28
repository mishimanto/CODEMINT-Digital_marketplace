<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; background:#f4f4f4; margin:0; padding:0; }
        .container { max-width: 600px; margin: 20px auto; background:#fff; border-radius:8px; overflow:hidden; box-shadow: 0 2px 6px rgba(0,0,0,0.1); }
        .header { background: #4CAF50; color: #fff; text-align: center; padding: 20px; }
        .content { padding: 20px; color: #333; }
        .footer { background: #f4f4f4; text-align: center; padding: 15px; font-size: 12px; color: #777; }
        .btn { display: inline-block; padding: 10px 20px; margin-top: 15px; background:#4CAF50; color:#fff; text-decoration:none; border-radius:4px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Order Confirmed</h2>
        </div>
        <div class="content">
            <p>Hi {{ $order->user->name }},</p>
            <p>Thank you for your order! Your payment was successful. Please find your invoice attached.</p>
            <p><strong>Order ID:</strong> {{ $order->order_id }}</p>
            <p><strong>Total Amount:</strong> {{ $setting->currency_icon }}{{ number_format($order->total_amount, 2) }}</p>

            <a href="{{ url('/') }}" class="btn">Visit Our Store</a>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} {{ $setting->app_name ?? 'Our Store' }}. All rights reserved.
        </div>
    </div>
</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Invoice #{{ $order->order_id }}</title>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap');
    body {
      font-family: 'Roboto', sans-serif;
      background: #f4f6f8;
      margin: 0;
      padding: 20px;
      color: #333;
    }
    .invoice {
      background: #fff;
      max-width: 800px;
      margin: auto;
      padding: 30px;
      border-radius: 8px;
      box-shadow: 0 4px 20px rgba(0,0,0,0.05);
    }
    .invoice-header {
      display: flex;
      justify-content: space-between;
      border-bottom: 2px solid #e1e4e8;
      padding-bottom: 20px;
      margin-bottom: 20px;
    }
    .logo h1 {
      margin: 0;
      font-size: 24px;
      color: #2F80ED;
    }
    .invoice-info {
      text-align: right;
    }
    .invoice-info div {
      margin: 2px 0;
    }
    .invoice-info .label {
      font-weight: 500;
      color: #555;
      font-size: 14px;
    }
    .invoice-info .value {
      font-weight: 700;
      font-size: 16px;
      color: #111;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 30px;
    }
    table th, table td {
      text-align: left;
      padding: 12px;
    }
    table th {
      background: #f1f3f5;
      font-weight: 500;
      font-size: 14px;
      border-bottom: 2px solid #e1e4e8;
    }
    table tr:nth-child(even) td {
      background: #fafbfc;
    }
    .total-row td {
      font-weight: 700;
      background: #f1f3f5;
      border-top: 1px solid black;
    }
    .total-row td:last-child {
      text-align: ;
    }
    .thankyou {
      text-align: center;
      margin-top: 20px;
      font-size: 16px;
      color: #2F80ED;
    }
    .footer {
  position: fixed;
  bottom: 0;
  left: 0;
  width: 100%;
  text-align: center;
  font-size: 12px;
  color: #888;
  background: #fff; /* যাতে নিচে ব্যাকগ্রাউন্ড না মিশে যায় */
  padding: 10px 0;
  border-top: 1px solid #e1e4e8;
}

  </style>
</head>
<body>
  <div class="invoice">
    <div class="invoice-header">
      <div class="logo">
        <h1>{{ $setting->app_name ?? 'CODEMINT' }}</h1>
      </div>
      <div class="invoice-info">
        <div><span class="label">Invoice #:</span> <span class="value">{{ $order->order_id }}</span></div>
        <div><span class="label">Date:</span> <span class="value">{{ $order->created_at->format('d M Y') }}</span></div>
      </div>
    </div>

    <div class="billing-info" style="margin-bottom: 80px;">
      <div style="float: left;">
        <div><span class="label"><h4>Billed To:</h4></span></div>
        <div>Name: {{ $order->user->name }}</div>
        <div>Phone: {{ $order->user->email }}</div>
      </div>
      <div style="clear: both;"></div>
    </div>

    <table>
      <thead>
        <tr>
          <th>#</th>
          <th>Item Description</th>
          <th>Qty</th>
          <th>Unit Price</th>
          <th>Discount</th>
          <th>Total</th>
        </tr>
      </thead>
      <tbody>
        @foreach($order->orderItems as $index => $item)
        <tr>
          <td>{{ $index + 1 }}</td>
          <td>{{ optional($item->product->productlangfrontend)->name ?? 'Product' }}</td>
          <td>{{ $item->qty }}</td>
          <td>{{ $setting->currency_icon }}{{ number_format($item->price, 2) }}</td>
          <td>{{ $setting->currency_icon }}{{ number_format($item->discount ?? 0, 2) }}</td>
          <td>{{ $setting->currency_icon }}{{ number_format(($item->price * $item->qty) - ($item->discount ?? 0), 2) }}</td>
        </tr>
        @endforeach

        <tr class="total-row">
          <td colspan="5" style="text-align:right; font-weight:bold;">Grand Total</td>
          <td style="font-weight:bold;">
            {{ $setting->currency_icon }}{{ number_format($order->total_amount, 2) }}
          </td>
        </tr>
      </tbody>
    </table>

    <div class="thankyou">Thank you for your payment!</div>
    <div class="footer">© {{ date('Y') }} {{ $setting->app_name ?? 'CODEMINT' }}. All rights reserved.</div>
  </div>
</body>
</html>

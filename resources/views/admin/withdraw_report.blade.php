<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Withdraw Report #{{ $withdraw->id ?? 'N/A' }}</title>
    <style>
        @page { margin: 40px 30px; }
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
            line-height: 1.5;
        }
        .header {
            text-align: center;
            margin-bottom: 25px;
            padding-bottom: 10px;
            border-bottom: 2px solid #007BFF;
        }
        .header h2 {
            margin: 0;
            font-size: 20px;
            color: #007BFF;
        }
        .header p {
            margin: 2px 0;
            font-size: 11px;
            color: #666;
        }
        .section {
            margin-bottom: 20px;
        }
        .section h3 {
            background-color: #f2f2f2;
            padding: 6px 10px;
            font-size: 14px;
            margin: 0 0 8px 0;
            border-left: 4px solid #007BFF;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 5px;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
            font-size: 12px;
        }
        th {
            background: #f9f9f9;
            font-weight: bold;
        }
        .small {
            font-size: 11px;
            color: #666;
        }
        .footer {
            position: fixed;
            bottom: 0px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 11px;
            color: #777;
            border-top: 1px solid #eee;
            padding-top: 8px;
        }

        .thankyou {
            text-align: center;
            margin-top: 50px;
            font-size: 14px;
            font-weight: bold;
            color: #28a745;
        }
    </style>
</head>
<body>

    <!-- Header Section -->
    <div class="header">
        <h2>{{ $setting->site_title ?? 'CODEMINT' }}</h2>
        <p>{{ $setting->site_address ?? '' }}</p>
        <h3 style="margin-top:8px;">Withdraw Report</h3>
        <p class="small">Report ID: #{{ $withdraw->id ?? 'N/A' }}</p>
    </div>

    <!-- User Info Section -->
    <div class="section">
        <h3>User Information</h3>
        <table>
            <tr>
                <th>Name</th>
                <td>{{ $user->name ?? $user->username ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Email</th>
                <td>{{ $user->email ?? 'N/A' }}</td>
            </tr>
        </table>
    </div>

    <!-- Withdraw Details Section -->
    <div class="section">
        <h3>Withdraw Details</h3>
        <table>
            <tr>
                <th>Total Amount</th>
                <td>{{ $setting->currency_icon ?? '' }} {{ $withdraw->total_amount ?? '0.00' }}</td>
            </tr>
            <tr>
                <th>Withdraw Amount</th>
                <td>{{ $setting->currency_icon ?? '' }} {{ $withdraw->withdraw_amount ?? '0.00' }}</td>
            </tr>
            <tr>
                <th>Charge</th>
                <td>{{ $setting->currency_icon ?? '' }} {{ ($withdraw->total_amount - $withdraw->withdraw_amount) ?? '0.00' }}</td>
            </tr>
            <tr>
                <th>Method</th>
                <td>{{ $withdraw->method->name ?? $withdraw->method ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Approved Date</th>
                <td>{{ $withdraw->approved_date ?? now() }}</td>
            </tr>
            <tr>
                <th>Status</th>
                <td>
                    @if($withdraw->status == 1)
                        <span style="color:green;font-weight:bold;">Approved</span>
                    @else
                        <span style="color:orange;font-weight:bold;">Pending</span>
                    @endif
                </td>
            </tr>
        </table>
    </div>

    <!-- Notes Section -->
    <div class="section">
        <p><b>Notes:</b> This is an auto-generated withdraw approval report. Please keep it for your records.</p>
    </div>

    <!-- Footer -->
    <div class="thankyou">Thank you for your trust!</div>
    <div class="footer">&copy; {{ date('Y') }} {{ $setting->app_name ?? 'CODEMINT' }}. All rights reserved.</div>

</body>
</html>

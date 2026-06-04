<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Receipt - Order #{{ $order->id }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #333; }
        .header { border-bottom: 2px solid #333; padding-bottom: 15px; margin-bottom: 20px; }
        .header h2 { margin: 0; }
        .header .right { text-align: right; }
        .bill-to { margin-bottom: 20px; }
        .bill-to .label { font-size: 10px; color: #666; font-weight: bold; text-transform: uppercase; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th { background: #f5f5f5; text-align: left; padding: 8px 6px; font-size: 11px; }
        td { padding: 8px 6px; border-bottom: 1px solid #ddd; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .total-row td { font-weight: bold; font-size: 14px; border-top: 2px solid #333; border-bottom: none; }
        .footer { text-align: center; font-size: 10px; color: #999; border-top: 1px solid #ddd; padding-top: 15px; margin-top: 30px; }
        .float-left { float: left; }
        .float-right { float: right; }
        .clearfix::after { content: ""; display: table; clear: both; }
    </style>
</head>
<body>
    <div class="header clearfix">
        <div class="float-left">
            <h2>SmartTrade Africa Ltd</h2>
            <p style="margin:0;font-size:11px;color:#666;">Receipt</p>
        </div>
        <div class="float-right right">
            <p style="margin:0;"><strong>Order #{{ $order->id }}</strong></p>
            <p style="margin:0;font-size:11px;color:#666;">{{ $order->created_at->format('M d, Y h:i A') }}</p>
        </div>
    </div>

    <div class="bill-to clearfix">
        <div class="float-left">
            <p class="label">Bill To</p>
            <p style="margin:0;">{{ $order->user->first_name }} {{ $order->user->last_name }}</p>
            <p style="margin:0;font-size:11px;color:#666;">{{ $order->user->email }}</p>
            <p style="margin:0;font-size:11px;color:#666;">{{ $order->user->phone ?? '' }}</p>
        </div>
        <div class="float-right text-right">
            <p class="label">Payment</p>
            <p style="margin:0;text-transform:capitalize;">{{ $order->payment?->gateway ?? 'N/A' }}</p>
            <p style="margin:0;font-size:11px;color:#666;">Ref: {{ $order->payment?->gateway_reference ?? 'N/A' }}</p>
            <p style="margin:0;font-size:11px;color:#666;">Status: Paid</p>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Item</th>
                <th class="text-center">Qty</th>
                <th class="text-right">Price</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
                <tr>
                    <td>{{ $item->product->name }}</td>
                    <td class="text-center">{{ $item->quantity }}</td>
                    <td class="text-right">Tsh {{ number_format($item->price, 0) }}</td>
                    <td class="text-right">Tsh {{ number_format($item->price * $item->quantity, 0) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="3" class="text-right">Total</td>
                <td class="text-right">Tsh {{ number_format($order->total_price, 0) }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        <p style="margin:0;">Thank you for your purchase!</p>
        <p style="margin:0;">SmartTrade Africa Ltd</p>
    </div>
</body>
</html>

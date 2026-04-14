<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Quotation — {{ $request->ref_number }}</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 25px; color: #000; font-size: 13px; }
        .header {
            border-bottom: 3px solid #000;
            padding-bottom: 18px;
            margin-bottom: 25px;
            overflow: hidden;
        }
        .company {
            float: right; text-align: right; color: #000;
            font-size: 22px; font-weight: bold;
        }
        .doc-title {
            font-size: 30px; font-weight: bold; color: #000;
            margin-top: 10px; letter-spacing: 1px;
        }
        .meta { width: 100%; margin-bottom: 25px; }
        .meta td { vertical-align: top; padding: 4px 0; }
        .meta .label { color: #555; font-weight: bold; }
        table.items {
            width: 100%; border-collapse: collapse; margin-bottom: 25px;
        }
        table.items th, table.items td {
            border: 1px solid #000; padding: 10px; text-align: left;
        }
        table.items th {
            background: #000; color: #fff; font-weight: bold;
        }
        .amount { text-align: right; font-weight: bold; }
        .total-row {
            font-size: 16px; font-weight: bold;
            background: #f4f4f4; color: #000;
        }
        .footer {
            margin-top: 35px; padding-top: 18px;
            border-top: 1px solid #000;
            text-align: center; color: #555; font-size: 11px;
        }
        .notes {
            background: #f4f4f4; padding: 12px 16px;
            border-left: 4px solid #000;
            margin-bottom: 20px; font-size: 12px;
            color: #000;
        }
        .badge {
            padding: 3px 10px; border-radius: 3px;
            font-size: 11px; font-weight: bold;
            background: #000; color: #fff;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="company">
            <img src="{{ public_path('common/img/goWOLO_logo_Black.png') }}" alt="GoWolo Global" style="height:55px;">
            <div style="font-size:11px;color:#888;font-weight:normal;margin-top:4px;">Customization Services</div>
        </div>
        <div class="doc-title">QUOTATION</div>
        <div><span class="badge">PENDING PAYMENT</span></div>
    </div>

    <table class="meta">
        <tr>
            <td width="50%">
                <div class="label">Bill To:</div>
                <strong>{{ $request->first_name }} {{ $request->last_name }}</strong><br>
                {{ $request->email }}<br>
                {{ $request->phone }}<br>
                {{ $request->company_name }}
            </td>
            <td width="50%" style="text-align:right;">
                <div><span class="label">Quotation #:</span> QUO-{{ $request->ref_number }}</div>
                <div><span class="label">Reference:</span> {{ $request->ref_number }}</div>
                <div><span class="label">Date:</span> {{ now()->format('M d, Y') }}</div>
                <div><span class="label">Valid Until:</span> {{ now()->addDays(30)->format('M d, Y') }}</div>
            </td>
        </tr>
    </table>

    <div class="notes">
        <strong>Note:</strong> This is a quotation for the customization services requested.
        The amount is payable before work begins. Quotation is valid for 30 days from the date above.
    </div>

    <table class="items">
        <thead>
            <tr>
                <th>Description</th>
                <th width="100" style="text-align:center;">Quantity</th>
                <th width="120" class="amount">Amount</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <strong>{{ ucwords(str_replace('_', ' ', $request->request_type ?? 'Customization')) }} Services</strong><br>
                    <small style="color:#666;">
                        @php
                            $items = [];
                            if ($request->req_logo) $items[] = 'Logo';
                            if ($request->req_icon) $items[] = 'Web Icon';
                            if ($request->req_app_background) $items[] = 'App Background';
                            if ($request->req_landing_page) $items[] = 'Landing Page';
                            if ($request->req_others) $items[] = 'Others';
                        @endphp
                        @if(count($items))
                            Includes: {{ implode(', ', $items) }}
                        @elseif($request->request_description)
                            {{ \Illuminate\Support\Str::limit($request->request_description, 150) }}
                        @endif
                    </small>
                </td>
                <td style="text-align:center;">1</td>
                <td class="amount">${{ number_format($request->pay_amount, 2) }}</td>
            </tr>
            <tr>
                <td colspan="2" class="amount">Subtotal</td>
                <td class="amount">${{ number_format($request->pay_amount, 2) }}</td>
            </tr>
            <tr class="total-row">
                <td colspan="2" class="amount">TOTAL</td>
                <td class="amount">${{ number_format($request->pay_amount, 2) }} USD</td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        This quotation was generated on {{ now()->format('M d, Y H:i') }} from the GoWolo Customization Portal.<br>
        For questions, please contact us through the portal chat.
    </div>
</body>
</html>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $invoice->invoice_type === 'gst_invoice' ? 'TAX INVOICE' : 'PROFORMA INVOICE' }} | {{ $invoice->invoice_number }}</title>
    <style>
        @page {
            margin: 12px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            color: #000;
            font-size: 11px;
            line-height: 1.5;
            background: #fff;
        }

        .invoice-box {
            max-width: 750px;
            margin: 0 auto;
            border: 2px solid #000;
            padding: 0;
        }

        .content {
            padding: 25px 30px;
        }

        /* ── HEADER WITH LOGO LEFT + COMPANY RIGHT ── */
        .header {
            display: table;
            width: 100%;
            table-layout: fixed;
        }

        .header .logo-cell {
            width: 200px;
        }

        .header .logo-img {
            height: 120px;
            width: auto;
            max-width: 100%;
            object-fit: contain;
        }

        .header .company-cell {
            display: table-cell;

            vertical-align: middle;

            text-align: center;

            width: 100%;
        }

        .title-bar {
            text-align: center;

            padding: 0;

            margin-bottom: 8px;

            font-size: 11px;

            font-weight: 700;

            letter-spacing: 0.5px;

            text-transform: uppercase;
        }

        .invoice-info {
            display: table;
            width: 100%;
            border: 1px solid #000;
            margin-bottom: 12px;
            table-layout: fixed;
        }

        .invoice-info .left,
        .invoice-info .right {
            display: table-cell;
            vertical-align: top;
            padding: 8px;
        }

        .invoice-info .left {
            width: 40%;
            border-right: 1px solid #000;
        }

        .invoice-info .right {
            width: 60%;
        }

        .invoice-info .left .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3px;
            font-size: 9px;
            line-height: 1;
        }

        .invoice-info .left h4 {
            font-size: 9px;
            margin-bottom: 6px;
            padding-bottom: 3px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2px;
            font-size: 10px;
        }

        .info-row span:first-child {
            font-weight: 700;
        }

        .invoice-info .right {
            line-height: 1.1;
        }

        .invoice-info .right h4 {
            margin-bottom: 5px;
        }

        .recipient-name {
            font-size: 16px;
            /* bada sirf client name */
            font-weight: 700;
            margin-bottom: 2px;
            line-height: 1;
        }

        .recipient-details {
            font-size: 11px;
            line-height: 1.1;
        }

        .recipient-phone {
            margin-top: 2px;
            font-size: 11px;
            line-height: 1;
        }

        .gstin-tag {
            margin-top: 2px;
            font-size: 11px;
            line-height: 1;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
        }

        .items-table th,
        .items-table td {
            border: 1px solid #000;
            padding: 6px 8px;
            font-size: 10px;
            text-align: center;
        }

        .items-table th {
            background: #000;
            color: #fff;
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .items-table .total-row {
            font-weight: 700;
            border-top: 2px solid #000;
            border-bottom: 2px solid #000;
        }

        .text-left {
            text-align: left !important;
        }

        .text-right {
            text-align: right !important;
        }

        /* TOTALS */

        .totals-wrapper {
            text-align: right;
            margin: 10px 0 12px;
        }

        .totals-section {
            display: inline-block;

            width: 290px;

            border-top: none;

            padding-top: 6px;
        }

        .totals-table {
            width: 100%;
            border-collapse: collapse;
        }

        .totals-table td {
            padding: 4px 4px;

            font-size: 9px;

            line-height: 1.2;
        }

        .totals-table td:first-child {
            text-align: right;

            color: #333;
        }

        .totals-table td:last-child {
            text-align: right;

            font-weight: 700;

            width: 120px;
        }

        .totals-table .grand-row td {
            border-top: 2px solid #000;

            padding-top: 6px;

            font-size: 12px;

            font-weight: 900;
        }



        /* AMOUNT IN WORDS */

        .amount-words {
            border: 1px solid #000;

            border-left: 5px solid #000;

            padding: 8px 12px;

            margin-top: 8px;

            font-size: 10px;

            line-height: 1.4;

            font-style: normal;

            font-weight: 500;
        }

        .amount-words strong {
            display: block;

            margin-bottom: 2px;

            font-size: 9px;

            text-transform: uppercase;
        }

        .alert-box {
            border: 1px solid #000;
            padding: 8px 14px;
            margin-bottom: 12px;
            font-size: 10px;
        }

        .bottom-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            padding-top: 14px;
            border-top: 1px solid #000;
        }

        .bottom-left {
            font-size: 9px;
        }

        .bottom-right {
            text-align: right;
        }

        .sig-block {
            display: inline-block;
            text-align: center;
        }

        .sig-block .sig-img {
            width: 140px;
            height: 60px;

            object-fit: contain;

            display: block;

            margin: 0 auto 20px auto;
        }

        .sig-block .sig-line {
            width: 160px;
            border-top: 1px solid #000;
            margin: 0 auto 4px auto;
        }

        .sig-block .sig-label {
            font-size: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .sig-block .sig-company {
            font-size: 10px;
            font-weight: 700;
            margin-bottom: 2px;
        }

        .footer-line {
            margin-top: 18px;
            padding-top: 10px;
            border-top: 1px solid #000;
            text-align: center;
            font-size: 8px;
        }

        @media print {
            body {
                padding: 0;
            }

            .invoice-box {
                border: none;
            }
        }
    </style>
</head>

<body>

    <div class="invoice-box">
        <div class="title-bar">
            {{ $invoice->invoice_type === 'gst_invoice' ? 'TAX INVOICE' : 'PROFORMA INVOICE' }}
        </div>
        <div class="content">

            {{-- HEADER: Logo LEFT + Company RIGHT --}}
            <div class="header">
                <div class="logo-cell">
                    @if($invoice->company->logo_path)
                    <img src="{{ public_path('storage/' . $invoice->company->logo_path) }}" class="logo-img">
                    @endif
                </div>
                <div class="company-cell">
                    <h1>{{ $invoice->company->name ?? config('app.name') }}</h1>
                    <p>{{ $invoice->company->address_line_1 ?? '' }}{{ $invoice->company->city ? ', ' . $invoice->company->city : '' }}{{ $invoice->company->pincode ? ' - ' . $invoice->company->pincode : '' }}</p>
                    <p>{{ $invoice->company->state ?? '' }}, {{ $invoice->company->country ?? 'India' }}</p>
                    @if($invoice->company->phone || $invoice->company->email)
                    <p>{{ $invoice->company->phone ?? '' }}{{ $invoice->company->phone && $invoice->company->email ? ' | ' : '' }}{{ $invoice->company->email ?? '' }}</p>
                    @endif
                    @if($invoice->company->gstin)
                    <p class="gstin">GSTIN: {{ $invoice->company->gstin }}</p>
                    @endif
                </div>
            </div>

            {{-- INVOICE DETAILS LEFT + RECIPIENT RIGHT --}}
            <div class="invoice-info">
                <div class="left">
                    <h4>Invoice Details</h4>
                    <div class="info-row"><span>Invoice No: </span><span>{{ $invoice->invoice_number }}</span></div>
                    <div class="info-row"><span>Date: </span><span>{{ $invoice->invoice_date->format('d-m-Y') }}</span></div>
                    @if($invoice->due_date)
                    <div class="info-row"><span>Due Date: </span><span>{{ $invoice->due_date->format('d-m-Y') }}</span></div>
                    @endif
                    @if($invoice->reference_number)
                    <div class="info-row"><span>Reference: </span><span>{{ $invoice->reference_number }}</span></div>
                    @endif
                    <div class="info-row"><span>Place of Supply: </span><span>{{ $invoice->place_of_supply ?? '—' }}</span></div>
                    @if($invoice->payment_terms)
                    <div class="info-row"><span>Payment Terms: </span><span>{{ $invoice->payment_terms }}</span></div>
                    @endif
                </div>
                <div class="right">
                    <h4>Recipient (Bill To)</h4>

                    <div class="recipient-name">
                        {{ $invoice->client->name ?? 'N/A' }}
                    </div>

                    <div class="recipient-details">
                        @if($invoice->client->address_line_1)
                        {{ $invoice->client->address_line_1 }}<br>
                        @endif

                        {{ $invoice->client->city ?? '' }},
                        {{ $invoice->client->state_name ?? $invoice->client->state ?? '' }}
                        {{ $invoice->client->pincode ?? '' }}
                    </div>

                    @if($invoice->client->phone)
                    <div class="recipient-phone">
                        M: {{ $invoice->client->phone }}
                    </div>
                    @endif

                    @if($invoice->client->gstin)
                    <div class="gstin-tag">
                        GSTIN: {{ $invoice->client->gstin }}
                    </div>
                    @endif
                </div>
            </div>

            {{-- ITEMS TABLE --}}
            <table class="items-table">
                <thead>
                    <tr>
                        <th style="width:4%;">#</th>
                        <th style="width:34%;" class="text-left">Description</th>
                        <th style="width:10%;">HSN/SAC</th>
                        <th style="width:7%;">Qty</th>
                        <th style="width:7%;">Unit</th>
                        <th style="width:12%;">Rate(Rs)</th>
                        <th style="width:7%;">GST%</th>
                        <th style="width:14%;">Amount(Rs)</th>
                    </tr>
                </thead>
                <tbody>
                    @php $i = 1; $totalQty = 0; @endphp
                    @foreach($invoice->items as $item)
                    @php $totalQty += $item->quantity; @endphp
                    <tr>
                        <td>{{ $i++ }}</td>
                        <td class="text-left"><strong>{{ $item->name }}</strong></td>
                        <td>{{ $item->hsn_sac_code ?? '—' }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>{{ $item->unit ?? 'Nos' }}</td>
                        <td class="text-right">{{ \App\Helpers\NumberToWords::indianFormat($item->unit_price) }}</td>
                        <td>{{ $item->gst_rate }}%</td>
                        <td class="text-right">
                            {{ \App\Helpers\NumberToWords::indianFormat(
($item->line_total > 0
    ? $item->line_total
    : ($item->total_amount > 0
        ? $item->total_amount
        : ($item->quantity * $item->unit_price)))
) }}
                        </td>
                    </tr>
                    @endforeach
                    @if($invoice->items->count() > 1)
                    <tr class="total-row">
                        <td colspan="3" class="text-right">TOTAL</td>
                        <td>{{ $totalQty }}</td>
                        <td colspan="2">—</td>
                        <td>—</td>
                        <td class="text-right">{{ \App\Helpers\NumberToWords::indianFormat($invoice->subtotal) }}</td>
                    </tr>
                    @endif
                </tbody>
            </table>

            {{-- TOTALS RIGHT ALIGNED --}}
            <div class="totals-wrapper">
                <div class="totals-section">
                    <table class="totals-table">
                        <tr>
                            <td>Subtotal</td>
                            <td>Rs. {{ \App\Helpers\NumberToWords::indianFormat($invoice->subtotal) }}</td>
                        </tr>
                        @if($invoice->discount_amount > 0)
                        <tr>
                            <td>Discount</td>
                            <td>-Rs. {{ \App\Helpers\NumberToWords::indianFormat($invoice->discount_amount) }}</td>
                        </tr>
                        @endif
                        @if($invoice->invoice_type === 'gst_invoice')
                        @if($invoice->place_of_supply === 'export')
                        <tr>
                            <td colspan="2" style="text-align:center;font-weight:700;">Export — No GST</td>
                        </tr>
                        @elseif($invoice->igst_amount > 0)
                        <tr>
                            <td>IGST @ {{ $invoice->igst_rate ?? $invoice->gst_rate }}%</td>
                            <td>Rs. {{ \App\Helpers\NumberToWords::indianFormat($invoice->igst_amount) }}</td>
                        </tr>
                        @else
                        @if($invoice->cgst_amount > 0)
                        <tr>
                            <td>CGST @ {{ $invoice->cgst_rate ?? ($invoice->gst_rate/2) }}%</td>
                            <td>Rs. {{ \App\Helpers\NumberToWords::indianFormat($invoice->cgst_amount) }}</td>
                        </tr>
                        @endif
                        @if($invoice->sgst_amount > 0)
                        <tr>
                            <td>SGST @ {{ $invoice->sgst_rate ?? ($invoice->gst_rate/2) }}%</td>
                            <td>Rs. {{ \App\Helpers\NumberToWords::indianFormat($invoice->sgst_amount) }}</td>
                        </tr>
                        @endif
                        @endif
                        @endif
                        @if($invoice->shipping_charges > 0)
                        <tr>
                            <td>Shipping</td>
                            <td>Rs. {{ \App\Helpers\NumberToWords::indianFormat($invoice->shipping_charges) }}</td>
                        </tr>
                        @endif
                        <tr class="grand-row">
                            <td>GRAND TOTAL</td>
                            <td>Rs. {{ \App\Helpers\NumberToWords::indianFormat(round($invoice->grand_total)) }}</td>
                        </tr>
                        @if($invoice->balance_due > 0 && $invoice->status !== 'draft')
                        <tr>
                            <td style="font-weight:700;">Balance Due</td>
                            <td style="font-weight:700;">Rs. {{ \App\Helpers\NumberToWords::indianFormat($invoice->balance_due) }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>

            <div class="amount-words">
                Amount in Words: {{ ucwords(\App\Helpers\NumberToWords::convert($invoice->grand_total)) }} Only
            </div>

            <div class="bottom-section">
                <div class="bottom-left">
                    @if($invoice->terms_and_conditions)
                    <strong>Terms:</strong> {{ $invoice->terms_and_conditions }}<br><br>
                    @endif
                    @if($invoice->notes)<strong>Notes:</strong> {{ $invoice->notes }}<br><br>@endif
                    <p>Certified that the particulars given above are true and correct.</p>
                </div>
                <div class="bottom-right">
                    <div class="sig-block">
                        <div class="sig-company">For {{ $invoice->company->name ?? config('app.name') }}</div>
                        @if($invoice->company->signature_path)
                        <img src="{{ public_path('storage/' . $invoice->company->signature_path) }}" class="sig-img">
                        @endif
                        <div class="sig-line"></div>
                        <div class="sig-label">Authorised Signatory</div>
                    </div>
                </div>
            </div>

            <div class="footer-line">
                This is a computer-generated {{ $invoice->invoice_type === 'gst_invoice' ? 'Tax Invoice' : 'Proforma Invoice' }}<br>
                GSTIN: {{ $invoice->company->gstin ?? 'N/A' }} | PAN: {{ $invoice->company->pan ?? 'N/A' }}
            </div>

        </div>
    </div>

</body>

</html>
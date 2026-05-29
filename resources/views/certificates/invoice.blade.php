<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        @page { size: A4 portrait; margin: 10mm; }
        body { font-family: Helvetica, sans-serif; font-size: 9pt; margin: 0; padding: 0; }
        table { border-collapse: collapse; width: 100%; }
        .section-header { background:#1a3a6b; color:white; padding:2mm 4mm; font-weight:bold; font-size:8pt; }
        .cell-border { border:1px solid #999; }
    </style>
</head>
<body>
    <table style="width:100%;border-collapse:collapse;margin-bottom:10mm">
        <tr>
            <td style="width:50%;vertical-align:top;padding:5mm">
                <div style="font-size:7pt"><strong>{{ $company['name'] ?? 'Company Name' }}</strong></div>
                <div style="font-size:6pt">{{ $company['address'] ?? '' }}</div>
                <div style="font-size:6pt">{{ $company['phone'] ?? '' }}</div>
            </td>
            <td style="width:50%;text-align:right;padding:5mm">
                <div style="font-size:18pt;font-weight:bold">INVOICE</div>
            </td>
        </tr>
    </table>
    
    <table style="width:100%;border-collapse:collapse;margin-bottom:5mm;font-size:8pt">
        <tr>
            <td style="padding:2mm"><strong>Invoice No:</strong> {{ $invoiceNo ?? '' }}</td>
            <td style="padding:2mm"><strong>Date:</strong> {{ $date ?? '' }}</td>
            <td style="padding:2mm"><strong>Due Date:</strong> {{ $dueDate ?? '' }}</td>
        </tr>
    </table>
    
    <div style="margin:5mm 0;font-weight:bold;font-size:9pt">Bill To</div>
    <div style="border:1px solid #999;padding:3mm;font-size:8pt;margin-bottom:5mm">
        <div><strong>{{ $client['name'] ?? '' }}</strong></div>
        <div>{{ $jobAddress['line1'] ?? '' }}</div>
        <div>{{ $jobAddress['line2'] ?? '' }}</div>
        <div>{{ $jobAddress['city'] ?? '' }} {{ $jobAddress['postcode'] ?? '' }}</div>
    </div>
    
    <div style="margin:5mm 0;font-weight:bold;font-size:9pt">Invoice Items</div>
    <table style="width:100%;border-collapse:collapse;font-size:8pt;margin-bottom:5mm">
        <tr>
            <td class="section-header" style="width:50%">Description</td>
            <td class="section-header" style="width:15%">Units</td>
            <td class="section-header" style="width:15%">Price</td>
            <td class="section-header" style="width:10%">VAT%</td>
            <td class="section-header" style="width:10%;text-align:right">Total</td>
        </tr>
        @for($i = 0; $i < 5; $i++)
            <tr>
                <td class="cell-border" style="padding:2mm">{{ $items[$i]['description'] ?? '' }}</td>
                <td class="cell-border" style="padding:2mm;text-align:center">{{ $items[$i]['units'] ?? '' }}</td>
                <td class="cell-border" style="padding:2mm;text-align:right">{{ $items[$i]['price'] ?? '' }}</td>
                <td class="cell-border" style="padding:2mm;text-align:center">{{ $items[$i]['vat'] ?? '20' }}</td>
                <td class="cell-border" style="padding:2mm;text-align:right">{{ $items[$i]['total'] ?? '' }}</td>
            </tr>
        @endfor
    </table>
    
    <table style="width:100%;border-collapse:collapse;font-size:9pt;margin-bottom:10mm">
        <tr>
            <td style="width:60%;text-align:right;padding:2mm"><strong>Subtotal:</strong></td>
            <td style="width:40%;padding:2mm;text-align:right">{{ $subtotal ?? '0.00' }}</td>
        </tr>
        <tr>
            <td style="width:60%;text-align:right;padding:2mm"><strong>VAT:</strong></td>
            <td style="width:40%;padding:2mm;text-align:right">{{ $vat ?? '0.00' }}</td>
        </tr>
        <tr>
            <td style="width:60%;text-align:right;padding:2mm;background:#1a3a6b;color:white"><strong>TOTAL:</strong></td>
            <td style="width:40%;padding:2mm;text-align:right;background:#1a3a6b;color:white"><strong>{{ $total ?? '0.00' }}</strong></td>
        </tr>
    </table>
    
    <div style="margin:5mm 0;font-size:8pt">
        <strong>Payment Terms:</strong> {{ $paymentTerms ?? 'Net 30 days' }}
    </div>
    
    <div style="margin:5mm 0;font-size:8pt">
        <strong>Bank Details:</strong><br>
        Account: {{ $bankDetails['account'] ?? '' }}<br>
        Sort Code: {{ $bankDetails['sortCode'] ?? '' }}<br>
        IBAN: {{ $bankDetails['iban'] ?? '' }}
    </div>
    
    <div style="margin-top:20mm;font-size:9pt;font-style:italic;text-align:center">
        Thank you for your business!
    </div>
</body>
</html>

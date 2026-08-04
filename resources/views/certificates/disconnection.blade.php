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
    @include('certificates.partials._header', ['title' => 'Gas Disconnection Notice', 'certNo' => $certNo ?? ''])
    
    @include('certificates.partials._three-col-info', [
        'installer' => $installer ?? [],
        'jobAddress' => $jobAddress ?? [],
        'client' => $client ?? []
    ])
    
    <div style="margin:5mm 0;font-weight:bold;font-size:9pt">Appliance Disconnected</div>
    <table style="width:100%;border-collapse:collapse;font-size:8pt;margin-bottom:5mm">
        <tr>
            <td class="cell-border" style="padding:2mm;width:25%"><strong>Location:</strong> {{ $appliance['location'] ?? '' }}</td>
            <td class="cell-border" style="padding:2mm;width:25%;border-left:none"><strong>Make:</strong> {{ $appliance['make'] ?? '' }}</td>
            <td class="cell-border" style="padding:2mm;width:25%;border-left:none"><strong>Model:</strong> {{ $appliance['model'] ?? '' }}</td>
            <td class="cell-border" style="padding:2mm;width:25%;border-left:none"><strong>Serial:</strong> {{ $appliance['serial'] ?? '' }}</td>
        </tr>
    </table>
    
    <div style="margin:5mm 0;font-weight:bold;font-size:9pt">Reason for Disconnection</div>
    <div style="border:1px solid #999;padding:3mm;height:20mm;font-size:8pt;margin-bottom:5mm">
        {{ $reason ?? '' }}
    </div>
    
    <div style="margin:5mm 0;font-weight:bold;font-size:9pt">Action Required for Reconnection</div>
    <div style="border:1px solid #999;padding:3mm;height:20mm;font-size:8pt;margin-bottom:5mm">
        {{ $actionRequired ?? '' }}
    </div>
    
    @include('certificates.partials._danger-box', ['classification' => 'DO NOT USE - DISCONNECTED'])
    
    <div style="margin-top:20mm">
        @include('certificates.partials._sig-block', [
            'issuedBy' => $issuedBy ?? '',
            'receivedBy' => $receivedBy ?? '',
            'date' => $date ?? ''
        ])
    </div>
    
    @include('certificates.partials._footer', ['gasEmergency' => true])
</body>
</html>

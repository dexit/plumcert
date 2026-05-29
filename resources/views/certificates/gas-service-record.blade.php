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
    @include('certificates.partials._header', ['title' => 'Gas Service Record', 'certNo' => $certNo ?? ''])
    
    @include('certificates.partials._three-col-info', [
        'installer' => $installer ?? [],
        'jobAddress' => $jobAddress ?? [],
        'client' => $client ?? []
    ])
    
    <div style="margin:5mm 0;font-weight:bold;font-size:9pt">Appliance Details</div>
    <table style="width:100%;border-collapse:collapse;font-size:8pt;margin-bottom:5mm">
        <tr>
            <td class="cell-border" style="padding:2mm;width:25%"><strong>Type:</strong> {{ $appliance['type'] ?? '' }}</td>
            <td class="cell-border" style="padding:2mm;width:25%"><strong>Make:</strong> {{ $appliance['make'] ?? '' }}</td>
            <td class="cell-border" style="padding:2mm;width:25%"><strong>Model:</strong> {{ $appliance['model'] ?? '' }}</td>
            <td class="cell-border" style="padding:2mm;width:25%"><strong>Serial:</strong> {{ $appliance['serial'] ?? '' }}</td>
        </tr>
    </table>
    
    <div style="margin:5mm 0;font-weight:bold;font-size:9pt">Service Checks</div>
    <table style="width:100%;border-collapse:collapse;font-size:8pt;margin-bottom:5mm">
        <tr>
            <td style="padding:2mm;width:50%">
                <div><input type="checkbox" style="margin:0"> Pipework soundness</div>
                <div><input type="checkbox" style="margin:0"> Burner pressure correct</div>
                <div><input type="checkbox" style="margin:0"> Safety devices operate</div>
                <div><input type="checkbox" style="margin:0"> Heat exchanger cleaned</div>
                <div><input type="checkbox" style="margin:0"> Burner cleaned</div>
                <div><input type="checkbox" style="margin:0"> Controls function properly</div>
            </td>
            <td style="padding:2mm;width:50%;border-left:1px solid #999">
                <div><input type="checkbox" style="margin:0"> Flue/combustion products</div>
                <div><input type="checkbox" style="margin:0"> Ventilation adequate</div>
                <div><input type="checkbox" style="margin:0"> Ignition system works</div>
                <div><input type="checkbox" style="margin:0"> Flue integrity sound</div>
                <div><input type="checkbox" style="margin:0"> CO/CO2 test completed</div>
                <div><input type="checkbox" style="margin:0"> Gas tightness confirmed</div>
            </td>
        </tr>
    </table>
    
    <div style="margin:5mm 0;font-weight:bold;font-size:9pt">Combustion Analysis</div>
    <table style="width:100%;border-collapse:collapse;font-size:8pt;margin-bottom:5mm">
        <tr>
            <td style="padding:2mm;width:25%"><strong>CO ppm:</strong> {{ $combustion['co'] ?? '' }}</td>
            <td style="padding:2mm;width:25%"><strong>CO2%:</strong> {{ $combustion['co2'] ?? '' }}</td>
            <td style="padding:2mm;width:25%"><strong>Ratio:</strong> {{ $combustion['ratio'] ?? '' }}</td>
            <td style="padding:2mm;width:25%"><strong>Efficiency %:</strong> {{ $combustion['efficiency'] ?? '' }}</td>
        </tr>
    </table>
    
    <div style="margin:5mm 0;font-weight:bold;font-size:9pt">Service Comments</div>
    <div style="border:1px solid #999;padding:3mm;height:30mm;font-size:8pt;margin-bottom:5mm">
        {{ $comments ?? '' }}
    </div>
    
    <table style="width:100%;border-collapse:collapse;font-size:8pt">
        <tr>
            <td style="padding:2mm"><strong>Next Service Due:</strong> {{ $nextServiceDue ?? '' }}</td>
        </tr>
    </table>
    
    @include('certificates.partials._sig-block', [
        'issuedBy' => $issuedBy ?? '',
        'receivedBy' => $receivedBy ?? '',
        'date' => $date ?? ''
    ])
    
    @include('certificates.partials._footer', ['gasEmergency' => false])
</body>
</html>

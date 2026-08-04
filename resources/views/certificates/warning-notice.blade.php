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
    @include('certificates.partials._header', ['title' => 'Gas Warning Notice', 'certNo' => $certNo ?? ''])
    
    @include('certificates.partials._three-col-info', [
        'installer' => $installer ?? [],
        'jobAddress' => $jobAddress ?? [],
        'client' => $client ?? []
    ])
    
    <div style="margin:5mm 0;font-weight:bold;font-size:9pt">Appliance Details</div>
    <table style="width:100%;border-collapse:collapse;font-size:8pt;margin-bottom:5mm">
        <tr>
            <td class="cell-border" style="padding:2mm;width:20%"><strong>Location:</strong> {{ $appliance['location'] ?? '' }}</td>
            <td class="cell-border" style="padding:2mm;width:20%"><strong>Make:</strong> {{ $appliance['make'] ?? '' }}</td>
            <td class="cell-border" style="padding:2mm;width:20%"><strong>Type:</strong> {{ $appliance['type'] ?? '' }}</td>
            <td class="cell-border" style="padding:2mm;width:20%"><strong>Model:</strong> {{ $appliance['model'] ?? '' }}</td>
            <td class="cell-border" style="padding:2mm;width:20%"><strong>Serial:</strong> {{ $appliance['serial'] ?? '' }}</td>
        </tr>
    </table>
    
    <div style="margin:5mm 0;font-weight:bold;font-size:9pt">Issue Classification</div>
    <table style="width:100%;border-collapse:collapse;font-size:8pt;margin-bottom:5mm">
        <tr>
            <td style="padding:2mm;width:50%"><input type="checkbox" style="margin:0"> Gas Escape</td>
            <td style="padding:2mm;width:50%"><input type="checkbox" style="margin:0"> Pipework</td>
        </tr>
        <tr>
            <td style="padding:2mm"><input type="checkbox" style="margin:0"> Ventilation</td>
            <td style="padding:2mm"><input type="checkbox" style="margin:0"> Meter</td>
        </tr>
        <tr>
            <td style="padding:2mm"><input type="checkbox" style="margin:0"> Chimney/Flue</td>
            <td style="padding:2mm"><input type="checkbox" style="margin:0"> Other</td>
        </tr>
    </table>
    
    @include('certificates.partials._danger-box', ['classification' => 'IMMEDIATELY DANGEROUS'])
    
    <div style="margin:5mm 0;font-weight:bold;font-size:9pt">Details of Faults</div>
    <div style="border:1px solid #999;padding:3mm;height:25mm;font-size:8pt;margin-bottom:5mm">
        {{ $faultDetails ?? '' }}
    </div>
    
    <table style="width:100%;border-collapse:collapse;font-size:8pt;margin-bottom:5mm">
        <tr>
            <td style="width:50%;vertical-align:top;padding-right:3mm">
                <div style="font-weight:bold;margin-bottom:2mm">Actions Taken</div>
                <div style="border:1px solid #999;padding:2mm;height:20mm">{{ $actionsTaken ?? '' }}</div>
            </td>
            <td style="width:50%;vertical-align:top;padding-left:3mm">
                <div style="font-weight:bold;margin-bottom:2mm">Actions Required</div>
                <div style="border:1px solid #999;padding:2mm;height:20mm">{{ $actionsRequired ?? '' }}</div>
            </td>
        </tr>
    </table>
    
    <div style="margin:5mm 0;font-weight:bold;font-size:9pt">RIDDOR Reporting</div>
    <table style="width:100%;border-collapse:collapse;font-size:8pt;margin-bottom:8mm">
        <tr>
            <td style="padding:2mm;width:50%"><input type="checkbox" style="margin:0"> 11(1) - Serious</td>
            <td style="padding:2mm;width:50%"><input type="checkbox" style="margin:0"> 11(2) - Over 7 days</td>
        </tr>
    </table>
    
    <div style="font-size:7pt;margin-bottom:5mm">
        This warning notice must be given to the owner/occupier and retained on file. The gas supply may need to be isolated to prevent danger.
    </div>
    
    @include('certificates.partials._sig-block', [
        'issuedBy' => $issuedBy ?? '',
        'receivedBy' => $receivedBy ?? '',
        'date' => $date ?? ''
    ])
    
    @include('certificates.partials._footer', ['gasEmergency' => true])
</body>
</html>

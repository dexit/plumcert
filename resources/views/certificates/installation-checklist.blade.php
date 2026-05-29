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
    @include('certificates.partials._header', ['title' => 'Installation / Commissioning Checklist', 'certNo' => $certNo ?? ''])
    
    @include('certificates.partials._three-col-info', [
        'installer' => $installer ?? [],
        'jobAddress' => $jobAddress ?? [],
        'client' => $client ?? []
    ])
    
    <div style="margin:5mm 0;font-weight:bold;font-size:9pt">Appliance Details</div>
    <table style="width:100%;border-collapse:collapse;font-size:8pt;margin-bottom:5mm">
        <tr>
            <td class="cell-border" style="padding:2mm;width:20%"><strong>Type:</strong> {{ $appliance['type'] ?? '' }}</td>
            <td class="cell-border" style="padding:2mm;width:20%"><strong>Make:</strong> {{ $appliance['make'] ?? '' }}</td>
            <td class="cell-border" style="padding:2mm;width:20%"><strong>Model:</strong> {{ $appliance['model'] ?? '' }}</td>
            <td class="cell-border" style="padding:2mm;width:20%"><strong>Serial:</strong> {{ $appliance['serial'] ?? '' }}</td>
            <td class="cell-border" style="padding:2mm;width:20%"><strong>GC No:</strong> {{ $appliance['gcNumber'] ?? '' }}</td>
        </tr>
        <tr>
            <td class="cell-border" style="padding:2mm;width:50%;border-top:none"><strong>Landlord Equipment:</strong> {{ $appliance['landlordEquip'] ?? '' }}</td>
            <td class="cell-border" style="padding:2mm;width:50%;border-top:none;border-left:none" colspan="4"><strong>Flue Type:</strong> {{ $appliance['flueType'] ?? '' }}</td>
        </tr>
    </table>
    
    <table style="width:100%;border-collapse:collapse;font-size:8pt;margin-bottom:5mm">
        <tr>
            <td class="section-header" style="width:50%">Safety Checks</td>
            <td class="section-header" style="width:50%">Combustion Readings</td>
        </tr>
        <tr>
            <td style="padding:2mm;vertical-align:top">
                <div style="font-size:7pt">Heat input kW: {{ $safety['heatInput'] ?? '' }}</div>
                <div style="font-size:7pt">Running set point: {{ $safety['setPoint'] ?? '' }}</div>
                <div style="font-size:7pt"><input type="checkbox" style="margin:0"> Safety devices OK</div>
                <div style="font-size:7pt"><input type="checkbox" style="margin:0"> Ventilation adequate</div>
            </td>
            <td style="padding:2mm;vertical-align:top">
                <div style="font-size:7pt">CO ppm (High): {{ $combustion['coHigh'] ?? '' }}</div>
                <div style="font-size:7pt">CO ppm (Low): {{ $combustion['coLow'] ?? '' }}</div>
                <div style="font-size:7pt">CO2% (High): {{ $combustion['co2High'] ?? '' }}</div>
                <div style="font-size:7pt">CO2% (Low): {{ $combustion['co2Low'] ?? '' }}</div>
                <div style="font-size:7pt">Ratio (High): {{ $combustion['ratioHigh'] ?? '' }}</div>
                <div style="font-size:7pt">Ratio (Low): {{ $combustion['ratioLow'] ?? '' }}</div>
            </td>
        </tr>
    </table>
    
    <div style="margin:5mm 0;font-weight:bold;font-size:9pt">Gas Checks</div>
    <table style="width:100%;border-collapse:collapse;font-size:8pt;margin-bottom:5mm">
        <tr>
            <td style="padding:2mm;width:25%"><strong>Gas Rate:</strong> {{ $gasChecks['gasRate'] ?? '' }} M³/h</td>
            <td style="padding:2mm;width:25%"><input type="checkbox" style="margin:0"> Tightness test OK</td>
            <td style="padding:2mm;width:25%"><input type="checkbox" style="margin:0"> Equipotential bonding</td>
            <td style="padding:2mm;width:25%"><strong>Standing Pressure:</strong> {{ $gasChecks['standingPressure'] ?? '' }} mbar</td>
        </tr>
        <tr>
            <td style="padding:2mm"><strong>Working Pressure (Meter):</strong> {{ $gasChecks['workingPressureMeter'] ?? '' }}</td>
            <td style="padding:2mm"><strong>Working Pressure (Appliance):</strong> {{ $gasChecks['workingPressureAppliance'] ?? '' }}</td>
            <td colspan="2" style="padding:2mm"><input type="checkbox" style="margin:0"> Pressure test satisfactory</td>
        </tr>
    </table>
    
    <div style="margin:5mm 0;font-weight:bold;font-size:9pt">Engineer's Comments</div>
    <div style="border:1px solid #999;padding:3mm;height:20mm;font-size:8pt;margin-bottom:5mm">
        {{ $comments ?? '' }}
    </div>
    
    <table style="width:100%;border-collapse:collapse;font-size:8pt">
        <tr>
            <td style="padding:2mm;width:50%"><strong>Next Service Due:</strong> {{ $nextServiceDue ?? '' }}</td>
            <td style="padding:2mm;width:50%"><strong>Commissioning Date:</strong> {{ $commissioningDate ?? '' }}</td>
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

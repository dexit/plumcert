<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        @page { size: A4 landscape; margin: 10mm; }
        body { font-family: Helvetica, sans-serif; font-size: 9pt; margin: 0; padding: 0; }
        table { border-collapse: collapse; width: 100%; }
        .section-header { background:#1a3a6b; color:white; padding:3px 6px; font-weight:bold; font-size:8pt; }
        .label { color:#555; font-size:7pt; }
        .value { font-size:9pt; }
        .cell-border { border:1px solid #999; }
    </style>
</head>
<body>
    @include('certificates.partials._header', ['title' => 'Homeowner Gas Safety Record', 'certNo' => $certNo ?? ''])
    
    @include('certificates.partials._three-col-info', [
        'installer' => $installer ?? [],
        'jobAddress' => $jobAddress ?? [],
        'client' => $client ?? []
    ])
    
    <div style="margin:8mm 0;font-weight:bold;font-size:9pt">Appliances Inspected</div>
    <table style="width:100%;border-collapse:collapse;font-size:7pt;margin-bottom:8mm">
        <tr>
            <td class="section-header" style="width:5%">Location</td>
            <td class="section-header" style="width:5%">Type</td>
            <td class="section-header" style="width:5%">Make</td>
            <td class="section-header" style="width:5%">Model</td>
            <td class="section-header" style="width:4%">Flue</td>
            <td class="section-header" style="width:3%">Insp</td>
            <td class="section-header" style="width:4%">Op Press</td>
            <td class="section-header" style="width:4%">Heat Input</td>
            <td class="section-header" style="width:4%">HC CO</td>
            <td class="section-header" style="width:4%">HC CO2</td>
            <td class="section-header" style="width:4%">LC CO</td>
            <td class="section-header" style="width:4%">LC CO2</td>
            <td class="section-header" style="width:3%">Safe</td>
            <td class="section-header" style="width:3%">Vent</td>
            <td class="section-header" style="width:3%">FluVis</td>
            <td class="section-header" style="width:3%">FluPerf</td>
            <td class="section-header" style="width:3%">Srv</td>
        </tr>
        @for($i = 0; $i < 6; $i++)
            <tr>
                <td class="cell-border" style="padding:2mm">{{ $appliances[$i]['location'] ?? '' }}</td>
                <td class="cell-border" style="padding:2mm">{{ $appliances[$i]['type'] ?? '' }}</td>
                <td class="cell-border" style="padding:2mm">{{ $appliances[$i]['make'] ?? '' }}</td>
                <td class="cell-border" style="padding:2mm">{{ $appliances[$i]['model'] ?? '' }}</td>
                <td class="cell-border" style="padding:2mm;text-align:center">{{ $appliances[$i]['flue'] ?? '' }}</td>
                <td class="cell-border" style="padding:2mm;text-align:center">{{ $appliances[$i]['inspected'] ?? '' }}</td>
                <td class="cell-border" style="padding:2mm">{{ $appliances[$i]['opPressure'] ?? '' }}</td>
                <td class="cell-border" style="padding:2mm">{{ $appliances[$i]['heatInput'] ?? '' }}</td>
                <td class="cell-border" style="padding:2mm">{{ $appliances[$i]['hcCo'] ?? '' }}</td>
                <td class="cell-border" style="padding:2mm">{{ $appliances[$i]['hcCo2'] ?? '' }}</td>
                <td class="cell-border" style="padding:2mm">{{ $appliances[$i]['lcCo'] ?? '' }}</td>
                <td class="cell-border" style="padding:2mm">{{ $appliances[$i]['lcCo2'] ?? '' }}</td>
                <td class="cell-border" style="padding:2mm;text-align:center">{{ $appliances[$i]['safe'] ?? '' }}</td>
                <td class="cell-border" style="padding:2mm;text-align:center">{{ $appliances[$i]['vent'] ?? '' }}</td>
                <td class="cell-border" style="padding:2mm;text-align:center">{{ $appliances[$i]['fluVis'] ?? '' }}</td>
                <td class="cell-border" style="padding:2mm;text-align:center">{{ $appliances[$i]['fluPerf'] ?? '' }}</td>
                <td class="cell-border" style="padding:2mm;text-align:center">{{ $appliances[$i]['srv'] ?? '' }}</td>
            </tr>
        @endfor
    </table>
    
    <div style="margin:8mm 0;font-weight:bold;font-size:9pt">Defects Found</div>
    <table style="width:100%;border-collapse:collapse;font-size:7pt;margin-bottom:8mm">
        <tr>
            <td class="section-header" style="width:20%">Appliance</td>
            <td class="section-header" style="width:30%">Defect</td>
            <td class="section-header" style="width:15%">Classification</td>
            <td class="section-header" style="width:35%">Action Required</td>
        </tr>
        @for($i = 0; $i < 6; $i++)
            <tr>
                <td class="cell-border" style="padding:2mm">{{ $defects[$i]['appliance'] ?? '' }}</td>
                <td class="cell-border" style="padding:2mm">{{ $defects[$i]['defect'] ?? '' }}</td>
                <td class="cell-border" style="padding:2mm">{{ $defects[$i]['classification'] ?? '' }}</td>
                <td class="cell-border" style="padding:2mm">{{ $defects[$i]['action'] ?? '' }}</td>
            </tr>
        @endfor
    </table>
    
    <table style="width:100%;border-collapse:collapse;margin-bottom:8mm">
        <tr>
            <td style="width:50%;vertical-align:top;padding-right:5mm">
                <div style="font-weight:bold;margin-bottom:3mm;font-size:9pt">Safety Checks</div>
                <table style="width:100%;border-collapse:collapse;font-size:7pt">
                    <tr>
                        <td style="border:1px solid #999;padding:2mm">
                            <input type="checkbox" style="margin:0"> Pipework soundness
                        </td>
                    </tr>
                    <tr>
                        <td style="border:1px solid #999;padding:2mm">
                            <input type="checkbox" style="margin:0"> Burner condition
                        </td>
                    </tr>
                    <tr>
                        <td style="border:1px solid #999;padding:2mm">
                            <input type="checkbox" style="margin:0"> Gas controls
                        </td>
                    </tr>
                    <tr>
                        <td style="border:1px solid #999;padding:2mm">
                            <input type="checkbox" style="margin:0"> Flue integrity
                        </td>
                    </tr>
                    <tr>
                        <td style="border:1px solid #999;padding:2mm">
                            <input type="checkbox" style="margin:0"> Ventilation adequate
                        </td>
                    </tr>
                </table>
            </td>
            <td style="width:50%;vertical-align:top;padding-left:5mm">
                <div style="font-weight:bold;margin-bottom:3mm;font-size:9pt">General Comments</div>
                <div style="border:1px solid #999;padding:3mm;height:30mm;font-size:7pt;vertical-align:top">
                    {{ $comments ?? '' }}
                </div>
                <div style="margin-top:3mm;font-weight:bold;font-size:8pt">CO/Smoke Alarms:</div>
                <div style="font-size:7pt">{{ $alarmStatus ?? 'Not tested' }}</div>
            </td>
        </tr>
    </table>
    
    @include('certificates.partials._sig-block', [
        'issuedBy' => $issuedBy ?? '',
        'receivedBy' => $receivedBy ?? '',
        'date' => $date ?? ''
    ])
    
    @include('certificates.partials._footer', ['gasEmergency' => true])
</body>
</html>

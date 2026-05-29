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
    @include('certificates.partials._header', ['title' => 'Minor Works Certificate', 'certNo' => $certNo ?? ''])
    
    @include('certificates.partials._three-col-info', [
        'installer' => $installer ?? [],
        'jobAddress' => $jobAddress ?? [],
        'client' => $client ?? []
    ])
    
    <div style="margin:5mm 0;font-weight:bold;font-size:9pt">Works Description</div>
    <div style="border:1px solid #999;padding:3mm;height:25mm;font-size:8pt;margin-bottom:5mm">
        {{ $worksDescription ?? '' }}
    </div>
    
    <div style="margin:5mm 0;font-weight:bold;font-size:9pt">Test Results</div>
    <table style="width:100%;border-collapse:collapse;font-size:8pt;margin-bottom:5mm">
        <tr>
            <td class="cell-border" style="padding:2mm;width:25%"><strong>Continuity:</strong> {{ $testResults['continuity'] ?? '' }}</td>
            <td class="cell-border" style="padding:2mm;width:25%;border-left:none"><strong>Insulation Ω:</strong> {{ $testResults['insulation'] ?? '' }}</td>
            <td class="cell-border" style="padding:2mm;width:25%;border-left:none"><strong>RCD Operation:</strong> {{ $testResults['rcdOperation'] ?? '' }}ms</td>
            <td class="cell-border" style="padding:2mm;width:25%;border-left:none"><strong>Polarity:</strong> {{ $testResults['polarity'] ?? '' }}</td>
        </tr>
    </table>
    
    <div style="margin:5mm 0">
        <strong style="font-size:9pt">Compliance:</strong>
        <div style="border:2px solid #000;padding:3mm;margin-top:2mm;font-size:8pt">
            <input type="checkbox" style="margin:0"> Works comply with BS 7671:2018
        </div>
    </div>
    
    <div style="margin-top:20mm">
        @include('certificates.partials._sig-block', [
            'issuedBy' => $issuedBy ?? '',
            'receivedBy' => $receivedBy ?? '',
            'date' => $date ?? ''
        ])
    </div>
    
    @include('certificates.partials._footer', ['gasEmergency' => false])
</body>
</html>

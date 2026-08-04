@props(['installer'=>[], 'jobAddress'=>[], 'client'=>[]])

<table style="width:100%;border-collapse:collapse;margin-bottom:8mm;font-size:8pt">
    <tr>
        <td style="background:#1a3a6b;color:white;padding:2mm;width:33.3%;font-weight:bold">INSTALLER</td>
        <td style="background:#1a3a6b;color:white;padding:2mm;width:33.3%;font-weight:bold">JOB ADDRESS</td>
        <td style="background:#1a3a6b;color:white;padding:2mm;width:33.3%;font-weight:bold">CLIENT</td>
    </tr>
    <tr>
        <td style="padding:3mm;vertical-align:top;border:1px solid #ccc;font-size:7pt">
            <div><span style="color:#555">Name:</span> {{ $installer['name'] ?? '' }}</div>
            <div><span style="color:#555">License:</span> {{ $installer['license'] ?? '' }}</div>
            <div><span style="color:#555">Tel:</span> {{ $installer['tel'] ?? '' }}</div>
            <div><span style="color:#555">Email:</span> {{ $installer['email'] ?? '' }}</div>
        </td>
        <td style="padding:3mm;vertical-align:top;border:1px solid #ccc;font-size:7pt">
            <div>{{ $jobAddress['line1'] ?? '' }}</div>
            <div>{{ $jobAddress['line2'] ?? '' }}</div>
            <div>{{ $jobAddress['city'] ?? '' }} {{ $jobAddress['postcode'] ?? '' }}</div>
        </td>
        <td style="padding:3mm;vertical-align:top;border:1px solid #ccc;font-size:7pt">
            <div><span style="color:#555">Name:</span> {{ $client['name'] ?? '' }}</div>
            <div><span style="color:#555">Tel:</span> {{ $client['tel'] ?? '' }}</div>
            <div><span style="color:#555">Email:</span> {{ $client['email'] ?? '' }}</div>
        </td>
    </tr>
</table>

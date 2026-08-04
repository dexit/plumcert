@props(['title', 'certNo'])

<table style="width:100%;border-collapse:collapse;margin-bottom:10mm">
    <tr>
        <td style="background:#FFD700;padding:4mm;text-align:center;width:70%">
            <div style="font-size:14pt;font-weight:bold">{{ $title ?? 'Certificate' }}</div>
        </td>
        <td style="padding:4mm;text-align:right;vertical-align:middle;width:30%">
            <div style="font-size:8pt"><strong>Cert No:</strong> {{ $certNo ?? '' }}</div>
        </td>
    </tr>
</table>

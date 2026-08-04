@props(['issuedBy','receivedBy','date'])

<table style="width:100%;border-collapse:collapse;margin:8mm 0;font-size:8pt">
    <tr>
        <td style="width:33.3%;text-align:center;padding:3mm;border:1px solid #999">
            <div style="border-bottom:1px solid #000;height:15mm;margin-bottom:2mm"></div>
            <div style="font-size:7pt"><strong>Issued By</strong></div>
            <div style="font-size:7pt">{{ $issuedBy ?? '' }}</div>
        </td>
        <td style="width:33.3%;text-align:center;padding:3mm;border:1px solid #999">
            <div style="border-bottom:1px solid #000;height:15mm;margin-bottom:2mm"></div>
            <div style="font-size:7pt"><strong>Received By</strong></div>
            <div style="font-size:7pt">{{ $receivedBy ?? '' }}</div>
        </td>
        <td style="width:33.3%;text-align:center;padding:3mm;border:1px solid #999">
            <div style="font-size:7pt"><strong>Date</strong></div>
            <div style="border-bottom:1px solid #000;height:10mm;line-height:10mm;text-align:center">{{ $date ?? '' }}</div>
        </td>
    </tr>
</table>

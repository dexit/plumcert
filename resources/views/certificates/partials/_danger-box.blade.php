@props(['classification'])

<div style="background:#FF0000;color:white;padding:5mm;text-align:center;margin:5mm 0;border:2px solid #CC0000">
    <div style="font-size:11pt;font-weight:bold">{{ $classification ?? 'IMMEDIATELY DANGEROUS' }}</div>
</div>

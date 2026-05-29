@props(['gasEmergency'=>true])

<div style="font-size:6pt;text-align:center;margin-top:10mm;padding-top:5mm;border-top:1px solid #ccc;color:#666">
    <div><strong>Gas Safe Register Contact:</strong> 0800 408 5500</div>
    @if($gasEmergency)
        <div><strong>Gas Emergency Services:</strong> 0800 111 999</div>
    @endif
</div>

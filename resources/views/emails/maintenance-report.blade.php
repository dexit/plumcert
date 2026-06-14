<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Weekly Maintenance Report</title>
</head>
<body style="font-family:Arial,sans-serif;line-height:1.6;color:#222;margin:0;padding:0;background:#f4f4f4">
    <div style="max-width:680px;margin:0 auto;padding:24px">
        <div style="background:#1a3a6b;color:#fff;padding:20px 24px;border-radius:6px 6px 0 0">
            <h1 style="margin:0;font-size:22px">Weekly Maintenance Report</h1>
            <p style="margin:4px 0 0;opacity:.8;font-size:13px">Generated {{ $generated_at->format('l, d F Y H:i') }}</p>
        </div>

        <div style="background:#fff;padding:24px;border:1px solid #e5e5e5;border-top:none">
            <table style="width:100%;border-collapse:collapse;margin-bottom:24px;text-align:center">
                <tr>
                    <td style="padding:12px;background:#fff7ed;border-radius:6px">
                        <div style="font-size:28px;font-weight:bold;color:#b45309">{{ $services_due->count() }}</div>
                        <div style="font-size:12px;color:#777">Services Due (30d)</div>
                    </td>
                    <td style="width:12px"></td>
                    <td style="padding:12px;background:#eff6ff;border-radius:6px">
                        <div style="font-size:28px;font-weight:bold;color:#1d4ed8">{{ $certs_expiring->count() }}</div>
                        <div style="font-size:12px;color:#777">Certs Expiring (30d)</div>
                    </td>
                    <td style="width:12px"></td>
                    <td style="padding:12px;background:#f0fdf4;border-radius:6px">
                        <div style="font-size:28px;font-weight:bold;color:#15803d">{{ $reminders_pending }}</div>
                        <div style="font-size:12px;color:#777">Reminders Pending</div>
                    </td>
                </tr>
            </table>

            <h2 style="font-size:16px;color:#1a3a6b;border-bottom:2px solid #FFD700;padding-bottom:6px">Boiler Services Due</h2>
            @if($services_due->isEmpty())
                <p style="color:#777">No services due in the next 30 days.</p>
            @else
                <table style="width:100%;border-collapse:collapse;font-size:13px">
                    <tr style="text-align:left;color:#555">
                        <th style="padding:6px 4px;border-bottom:1px solid #eee">Customer</th>
                        <th style="padding:6px 4px;border-bottom:1px solid #eee">Appliance</th>
                        <th style="padding:6px 4px;border-bottom:1px solid #eee">Due</th>
                    </tr>
                    @foreach($services_due as $boiler)
                        @php $due = \Carbon\Carbon::parse($boiler->next_service_due); @endphp
                        <tr>
                            <td style="padding:6px 4px;border-bottom:1px solid #f3f3f3">
                                {{ trim(($boiler->property?->customer?->first_name ?? '') . ' ' . ($boiler->property?->customer?->last_name ?? '')) ?: '—' }}
                            </td>
                            <td style="padding:6px 4px;border-bottom:1px solid #f3f3f3">{{ $boiler->make }} {{ $boiler->model }}</td>
                            <td style="padding:6px 4px;border-bottom:1px solid #f3f3f3;color:{{ $due->isPast() ? '#b00020' : '#222' }}">
                                {{ $due->format('d M Y') }}{{ $due->isPast() ? ' (overdue)' : '' }}
                            </td>
                        </tr>
                    @endforeach
                </table>
            @endif

            <h2 style="font-size:16px;color:#1a3a6b;border-bottom:2px solid #FFD700;padding-bottom:6px;margin-top:28px">Certificates Expiring</h2>
            @if($certs_expiring->isEmpty())
                <p style="color:#777">No certificates expiring in the next 30 days.</p>
            @else
                <table style="width:100%;border-collapse:collapse;font-size:13px">
                    <tr style="text-align:left;color:#555">
                        <th style="padding:6px 4px;border-bottom:1px solid #eee">Cert No.</th>
                        <th style="padding:6px 4px;border-bottom:1px solid #eee">Customer</th>
                        <th style="padding:6px 4px;border-bottom:1px solid #eee">Expires</th>
                    </tr>
                    @foreach($certs_expiring as $cert)
                        @php $expires = \Carbon\Carbon::parse($cert->issued_at)->addYear(); @endphp
                        <tr>
                            <td style="padding:6px 4px;border-bottom:1px solid #f3f3f3">{{ $cert->certificate_number }}</td>
                            <td style="padding:6px 4px;border-bottom:1px solid #f3f3f3">
                                {{ trim(($cert->customer?->first_name ?? '') . ' ' . ($cert->customer?->last_name ?? '')) ?: '—' }}
                            </td>
                            <td style="padding:6px 4px;border-bottom:1px solid #f3f3f3;color:{{ $expires->isPast() ? '#b00020' : '#222' }}">
                                {{ $expires->format('d M Y') }}{{ $expires->isPast() ? ' (expired)' : '' }}
                            </td>
                        </tr>
                    @endforeach
                </table>
            @endif
        </div>

        <p style="font-size:12px;color:#999;text-align:center;margin-top:16px">
            Plumcert Gas Safety — automated weekly report
        </p>
    </div>
</body>
</html>

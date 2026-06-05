<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Your Gas Safety Certificate</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            border-bottom: 3px solid #0066cc;
        }
        .header h1 {
            color: #0066cc;
            margin: 0;
            font-size: 28px;
        }
        .content {
            padding: 20px;
            background-color: #ffffff;
        }
        .content p {
            margin: 15px 0;
        }
        .cert-details {
            background-color: #f0f7ff;
            padding: 15px;
            border-left: 4px solid #0066cc;
            margin: 20px 0;
        }
        .cert-details strong {
            display: block;
            color: #0066cc;
            margin-bottom: 5px;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            border-top: 1px solid #ddd;
            font-size: 12px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Your Gas Safety Certificate</h1>
        </div>

        <div class="content">
            <p>Dear {{ $certificate->customer?->first_name ?? 'Customer' }},</p>

            <p>Please find attached your {{ match($certificate->type) {
                'cp12_homeowner' => 'CP12 Homeowner Gas Safety Record',
                'cp12_landlord' => 'CP12 Landlord Gas Safety Record',
                default => 'Gas Safety Certificate'
            } }} (Cert No: {{ $certificate->certificate_number }}).</p>

            <div class="cert-details">
                <strong>Issued:</strong>
                {{ $certificate->issued_at?->format('d M Y') ?? 'To be confirmed' }}
            </div>

            <p>Your certificate is now valid and includes comprehensive details of your boiler inspection. Keep this certificate safe as you may need to present it for landlord compliance, property lettings, or insurance purposes.</p>

            <p>If you have any questions regarding your certificate or the inspection, please don't hesitate to contact us.</p>
        </div>

        <div class="footer">
            <p><strong>Plumcert Gas Safety</strong></p>
            <p>Gas Safe Registered | 0800 XXX XXXX</p>
            <p>Your trusted boiler service partner</p>
        </div>
    </div>
</body>
</html>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Boiler Service Reminder</title>
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
        .due-date {
            background-color: #f0f7ff;
            padding: 15px;
            border-left: 4px solid #0066cc;
            margin: 20px 0;
        }
        .due-date strong {
            display: block;
            color: #0066cc;
            margin-bottom: 5px;
        }
        .cta-button {
            display: inline-block;
            background-color: #ffc107;
            color: #333;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 4px;
            margin: 20px 0;
            font-weight: bold;
            text-align: center;
        }
        .cta-button:hover {
            background-color: #ffb300;
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
            <h1>Boiler Service Reminder</h1>
        </div>

        <div class="content">
            <p>Dear {{ $reminder->customer?->first_name ?? 'Customer' }},</p>

            <p>Your {{ $reminder->boiler?->make ?? '' }} {{ $reminder->boiler?->model ?? 'boiler' }} at {{ $reminder->property?->address ?? 'your property' }} is due for its annual service.</p>

            <div class="due-date">
                <strong>Service Due Date:</strong>
                {{ $reminder->due_at?->format('d M Y') ?? 'To be confirmed' }}
            </div>

            <p>Regular boiler servicing keeps your system running safely and efficiently, and is essential for maintaining your Gas Safe certification.</p>

            <div style="text-align: center;">
                <a href="https://plumcert.co.uk/book" class="cta-button">Book Your Service Now</a>
            </div>

            <p>If you have any questions or would like to arrange your service, please don't hesitate to contact us.</p>
        </div>

        <div class="footer">
            <p><strong>Plumcert Gas Safety</strong></p>
            <p>Gas Safe Registered | 0800 XXX XXXX</p>
            <p>Your trusted boiler service partner</p>
        </div>
    </div>
</body>
</html>

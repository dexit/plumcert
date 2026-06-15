<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"></head>
<body style="font-family:Arial,sans-serif;max-width:600px;margin:0 auto;padding:20px;color:#333">
    <h2 style="color:#1d4ed8">Your Gas Safety Certificates</h2>
    <p>Hi {{ $customer->first_name }},</p>
    <p>You can view your gas safety certificates, download copies and request your next service visit using your secure customer portal:</p>
    <p style="text-align:center;margin:30px 0">
        <a href="{{ $link }}" style="background:#1d4ed8;color:white;padding:14px 28px;border-radius:8px;text-decoration:none;font-weight:bold;display:inline-block">
            View My Certificates
        </a>
    </p>
    <p style="color:#666;font-size:13px">This link is valid for 30 days and is unique to you — please do not share it.</p>
    <hr style="border:none;border-top:1px solid #eee;margin:20px 0">
    <p style="color:#999;font-size:12px">Plumcert Gas Engineers</p>
</body>
</html>

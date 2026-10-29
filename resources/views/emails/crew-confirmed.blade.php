<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Your crew is confirmed for {{ $eventDate }}</title>
</head>
<body style="margin:0; padding:0; background-color:#F5F5F3; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#F5F5F3; padding:24px 0;">
<tr><td align="center">
<table role="presentation" width="600" cellpadding="0" cellspacing="0" border="0" style="max-width:600px; width:100%; background-color:#ffffff; border-radius:16px; overflow:hidden; box-shadow:0 2px 12px rgba(0,0,0,0.06);">
<!-- Header - Logo Left -->
<tr><td align="left" style="background-color:#ffffff; padding:22px 28px; border-bottom:1px solid #F0F0F0;">
<a href="https://africrew.com" target="_blank" style="text-decoration:none; display:inline-block;"><img src="https://africrew.com/images/logo.png" alt="AfriCrew - Crew Connect Hub" width="190" style="width:190px; max-width:190px; height:auto; display:block; border:0; outline:none; text-decoration:none;"></a>
</td></tr>
<!-- Content -->
<tr><td style="padding:32px 32px 8px 32px;">
<h1 style="margin:0 0 16px 0; font-size:20px; color:#111111; font-weight:700; line-height:26px;">Your crew is confirmed for {{ $eventDate }}</h1>
<p style="margin:0 0 16px 0; font-size:15px; color:#2D2D2D; line-height:24px;">Hi {{ $firstName }},</p>
<p style="margin:0 0 16px 0; font-size:15px; color:#2D2D2D; line-height:24px;">Your crew is confirmed for Gig {{ $gigId }}.</p>
<p style="margin:0 0 16px 0; font-size:15px; color:#2D2D2D; line-height:24px;">Crew: {{ $crewName }}<br>Date: {{ $eventDate }}</p>
<p style="margin:0 0 16px 0; font-size:15px; color:#2D2D2D; line-height:24px;">Please sign the gig contract to secure your booking.</p>

</td></tr>

<tr><td align="left" style="padding:8px 32px 32px 32px;">
<a href="{{ $contractLink }}" style="display:inline-block; background: linear-gradient(90deg, #FFB800 0%, #FF9A00 18%, #FF6A30 38%, #FF4D6D 65%, #E94090 100%); color:#ffffff; font-weight:800; font-size:14px; text-decoration:none; padding:14px 32px; border-radius:100px; letter-spacing:0.3px; box-shadow:0 8px 20px rgba(233,64,144,0.30); text-align:center;">Sign Contract</a>
</td></tr>

<!-- Footer - No logo, only text -->
<tr><td align="center" style="background-color:#0A0A0A; padding:20px 28px;">
<p style="margin:0; font-size:12px; color:#9CA3AF;">© 2026. All Rights Reserved. AfriCrew™</p>
<p style="margin:6px 0 0 0; font-size:11px; color:#6B7280;"><a href="https://africrew.com" style="color:#FF9A00; text-decoration:none;">africrew.com</a></p>
</td></tr>
</table>
</td></tr>
</table>
</body>
</html>

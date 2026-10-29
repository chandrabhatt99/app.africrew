<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Work Reference Request: {{ $crewName }} on AfriCrew</title>
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
<div style="display:inline-block; padding:4px 12px; background-color:#FEF3C7; border:1px solid #FDE68A; border-radius:100px; font-size:12px; font-weight:700; color:#92400E; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:16px;">
🛡️ Work Reference Verification
</div>
<h1 style="margin:0 0 16px 0; font-size:20px; color:#111111; font-weight:700; line-height:26px;">Work Reference Request: {{ $crewName }}</h1>
<p style="margin:0 0 16px 0; font-size:15px; color:#2D2D2D; line-height:24px;">Hi {{ $refereeFirstName }},</p>
<p style="margin:0 0 16px 0; font-size:15px; color:#2D2D2D; line-height:24px;">
    @if(!empty($period) && $period !== 'recent projects')
        <strong>{{ $crewName }}</strong> has listed you as a professional referee on their AfriCrew digital CV and indicated that you worked together during <strong>{{ $period }}</strong>.
    @else
        <strong>{{ $crewName }}</strong> has listed you as a professional referee on their AfriCrew digital CV and indicated that you worked together on recent projects.
    @endif
</p>

<!-- Details Card Box -->
<div style="background-color:#FAF9F6; border:1px solid #E5E7EB; border-radius:12px; padding:18px 20px; margin:0 0 20px 0;">
    <p style="margin:0 0 8px 0; font-size:14px; color:#111827; font-weight:700;">Professional: <span style="font-weight:600; color:#4B5563;">{{ $crewName }}</span></p>
    <p style="margin:0 0 8px 0; font-size:14px; color:#111827; font-weight:700;">Role / Discipline: <span style="font-weight:600; color:#4B5563;">{{ $crewCategory }}</span></p>
    <p style="margin:0 0 8px 0; font-size:14px; color:#111827; font-weight:700;">Company / Project: <span style="font-weight:600; color:#4B5563;">{{ $companyOrProject ?: $refereeOrg }}</span></p>
    @if(!empty($period) && $period !== 'recent projects')
    <p style="margin:0; font-size:14px; color:#111827; font-weight:700;">Period Worked Together: <span style="font-weight:600; color:#4B5563;">{{ $period }}</span></p>
    @endif
</div>

<p style="margin:0 0 16px 0; font-size:15px; color:#2D2D2D; line-height:24px;">
    Would you take a moment to review and verify this reference? You can confirm these details, provide a rating, leave feedback on their performance, or let us know if any adjustments are needed.
</p>

<!-- Privacy Notice Box -->
<div style="background-color:#F0FDF4; border:1px solid #BBF7D0; border-radius:10px; padding:12px 16px; margin:0 0 20px 0;">
    <p style="margin:0; font-size:13px; color:#166534; line-height:20px;">
        🔒 <strong>Your Privacy is Respected:</strong> You choose whether your name and review appear publicly on {{ $crewFirstName }}’s profile. Your email address and phone number are kept strictly confidential by AfriCrew and are never displayed publicly.
    </p>
</div>

</td></tr>

<!-- Action Button -->
<tr><td align="left" style="padding:8px 32px 28px 32px;">
<a href="{{ $reviewUrl }}" style="display:inline-block; background: linear-gradient(90deg, #FFB800 0%, #FF9A00 18%, #FF6A30 38%, #FF4D6D 65%, #E94090 100%); color:#ffffff; font-weight:800; font-size:14px; text-decoration:none; padding:14px 32px; border-radius:100px; letter-spacing:0.3px; box-shadow:0 8px 20px rgba(233,64,144,0.30); text-align:center;">Review & Verify Reference &rarr;</a>
</td></tr>

<!-- Sign-off -->
<tr><td style="padding:0 32px 28px 32px;">
<p style="margin:0 0 4px 0; font-size:14px; color:#4B5563; line-height:22px;">Thank you for helping our professionals build verified, credible profiles.</p>
<p style="margin:12px 0 0 0; font-size:14px; color:#111827; font-weight:700; line-height:20px;">
    AfriCrew Team<br>
    <a href="mailto:mail@africrew.com" style="color:#FF9A00; text-decoration:none; font-weight:600;">mail@africrew.com</a>
</p>
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

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>AfriCrew Application Update</title>
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
<tr><td style="padding:32px 32px 12px 32px;">
<div style="display:inline-block; padding:4px 12px; background-color:#FEE2E2; border:1px solid #FECACA; border-radius:100px; font-size:12px; font-weight:700; color:#991B1B; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:16px;">
Application Status Update
</div>
<h1 style="margin:0 0 16px 0; font-size:20px; color:#111111; font-weight:700; line-height:26px;">AfriCrew Application & Interview Decision</h1>
<p style="margin:0 0 16px 0; font-size:15px; color:#2D2D2D; line-height:24px;">Hi {{ $firstName }},</p>
<p style="margin:0 0 16px 0; font-size:15px; color:#2D2D2D; line-height:24px;">
Thank you for your interest in joining AfriCrew and for taking the time to participate in our talent vetting process.
</p>
<p style="margin:0 0 16px 0; font-size:15px; color:#2D2D2D; line-height:24px;">
Following a careful review of your profile and interview evaluation, we regret to inform you that your crew application has not been approved at this time.
</p>

@if(!empty($reason))
<div style="background-color:#F9FAFB; border-left:4px solid #EF4444; padding:14px 18px; margin:20px 0; border-radius:4px;">
  <div style="font-size:12px; font-weight:700; color:#374151; text-transform:uppercase; margin-bottom:4px;">Evaluation Feedback:</div>
  <div style="font-size:14px; color:#1F2937; line-height:20px;">{{ $reason }}</div>
</div>
@endif

<p style="margin:0 0 20px 0; font-size:14px; color:#4B5563; line-height:22px;">
We encourage you to continue enhancing your skills and experience. If you have questions regarding this decision, you may reach out to our team at <a href="mailto:mail@africrew.ke" style="color:#D97706; text-decoration:none; font-weight:600;">mail@africrew.ke</a>.
</p>
</td></tr>

<!-- Footer -->
<tr><td align="center" style="background-color:#0A0A0A; padding:20px 28px;">
<p style="margin:0; font-size:12px; color:#9CA3AF;">© 2026. All Rights Reserved. AfriCrew™</p>
<p style="margin:6px 0 0 0; font-size:11px; color:#6B7280;"><a href="https://africrew.com" style="color:#FF9A00; text-decoration:none;">africrew.com</a> • <a href="mailto:mail@africrew.ke" style="color:#9CA3AF; text-decoration:none;">mail@africrew.ke</a></p>
</td></tr>
</table>
</td></tr>
</table>
</body>
</html>

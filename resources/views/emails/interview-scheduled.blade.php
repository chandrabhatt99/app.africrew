<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Your AfriCrew Interview is Scheduled</title>
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
<div style="display:inline-block; padding:4px 12px; background-color:#FEF3C7; border:1px solid #FDE68A; border-radius:100px; font-size:12px; font-weight:700; color:#92400E; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:16px;">
🎙️ Vetting Interview Scheduled
</div>
<h1 style="margin:0 0 16px 0; font-size:22px; color:#111111; font-weight:800; line-height:28px;">Your AfriCrew Interview is Scheduled!</h1>
<p style="margin:0 0 16px 0; font-size:15px; color:#2D2D2D; line-height:24px;">Hi {{ $firstName }},</p>
<p style="margin:0 0 20px 0; font-size:15px; color:#2D2D2D; line-height:24px;">
Great news! Following the review of your crew registration, our administrative team has scheduled your official vetting interview. Please find the details below:
</p>

<!-- Schedule Box -->
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#F9FAFB; border:1px solid #E5E7EB; border-radius:12px; padding:18px 20px; margin-bottom:24px;">
<tr>
  <td style="padding:6px 0; font-size:13px; font-weight:600; color:#6B7280; width:140px;">📅 Date:</td>
  <td style="padding:6px 0; font-size:14px; font-weight:800; color:#111827;">{{ $interviewDate }}</td>
</tr>
<tr>
  <td style="padding:6px 0; font-size:13px; font-weight:600; color:#6B7280;">⏰ Time:</td>
  <td style="padding:6px 0; font-size:14px; font-weight:800; color:#111827;">{{ $interviewTime }}</td>
</tr>
<tr>
  <td style="padding:6px 0; font-size:13px; font-weight:600; color:#6B7280;">👤 Interviewer:</td>
  <td style="padding:6px 0; font-size:14px; font-weight:700; color:#111827;">{{ $interviewerName }}</td>
</tr>
@if(!empty($interviewLink))
<tr>
  <td style="padding:6px 0; font-size:13px; font-weight:600; color:#6B7280;">🔗 Meeting Link / Location:</td>
  <td style="padding:6px 0; font-size:14px; font-weight:700; color:#D97706;">
    @if(Str::startsWith($interviewLink, ['http://', 'https://']))
      <a href="{{ $interviewLink }}" target="_blank" style="color:#D97706; text-decoration:underline;">Join Meeting Online</a>
    @else
      {{ $interviewLink }}
    @endif
  </td>
</tr>
@endif
@if(!empty($interviewNotes))
<tr>
  <td style="padding:8px 0 0 0; font-size:13px; font-weight:600; color:#6B7280; vertical-align:top;">📝 Instructions / Notes:</td>
  <td style="padding:8px 0 0 0; font-size:13px; font-weight:500; color:#374151; line-height:20px;">{{ $interviewNotes }}</td>
</tr>
@endif
</table>

<p style="margin:0 0 8px 0; font-size:14px; color:#4B5563; line-height:22px;">
Please ensure you are ready on time. If you have any questions, feel free to contact us at <a href="mailto:mail@africrew.ke" style="color:#D97706; text-decoration:none; font-weight:600;">mail@africrew.ke</a>.
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

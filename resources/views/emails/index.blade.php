<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AfriCrew Email Templates Hub</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-dark: #0A0A0A;
            --card-bg: #141414;
            --card-border: #262626;
            --accent-orange: #FF9A00;
            --accent-pink: #E94090;
            --gradient: linear-gradient(90deg, #FFB800 0%, #FF9A00 18%, #FF6A30 38%, #FF4D6D 65%, #E94090 100%);
            --text-main: #FFFFFF;
            --text-muted: #9CA3AF;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            background-color: var(--bg-dark);
            color: var(--text-main);
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            min-height: 100vh;
            padding: 40px 24px;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        .header {
            margin-bottom: 36px;
            padding-bottom: 24px;
            border-bottom: 1px solid var(--card-border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 20px;
        }
        .header h1 {
            font-size: 28px;
            font-weight: 800;
            letter-spacing: -0.5px;
            background: var(--gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .header p {
            color: var(--text-muted);
            font-size: 14px;
            margin-top: 6px;
        }
        .quick-test-card {
            background: rgba(255,255,255,0.03);
            border: 1px solid var(--card-border);
            border-radius: 14px;
            padding: 20px 24px;
            margin-bottom: 32px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 16px;
        }
        .quick-test-form {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            align-items: center;
        }
        .input-field, .select-field {
            background: #1C1C1C;
            border: 1px solid #333;
            color: #fff;
            padding: 10px 16px;
            border-radius: 8px;
            font-size: 14px;
            outline: none;
        }
        .input-field:focus, .select-field:focus {
            border-color: var(--accent-orange);
        }
        .btn-primary {
            background: var(--gradient);
            color: #fff;
            border: none;
            padding: 10px 24px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
            transition: opacity 0.2s;
        }
        .btn-primary:hover {
            opacity: 0.9;
        }
        .btn-secondary {
            background: #222;
            color: #fff;
            border: 1px solid #333;
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            transition: all 0.2s;
        }
        .btn-secondary:hover {
            background: #2a2a2a;
            border-color: #444;
        }
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 100px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .badge-new {
            background: rgba(233, 64, 144, 0.2);
            color: #FF4D6D;
            border: 1px solid rgba(233, 64, 144, 0.4);
        }
        .badge-updated {
            background: rgba(255, 154, 0, 0.2);
            color: #FF9A00;
            border: 1px solid rgba(255, 154, 0, 0.4);
        }
        .badge-recipient {
            background: #222;
            color: #A0AEC0;
            border: 1px solid #333;
        }
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(360px, 1fr));
            gap: 20px;
        }
        .template-card {
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: 14px;
            padding: 24px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transition: transform 0.2s, border-color 0.2s;
        }
        .template-card:hover {
            transform: translateY(-2px);
            border-color: #3b3b3b;
        }
        .card-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            margin-bottom: 12px;
        }
        .card-title {
            font-size: 18px;
            font-weight: 700;
            color: #fff;
        }
        .card-subject {
            font-size: 13px;
            color: #d1d5db;
            margin-bottom: 12px;
            padding: 8px 12px;
            background: #1a1a1a;
            border-radius: 6px;
            border-left: 3px solid var(--accent-orange);
            word-break: break-word;
        }
        .card-desc {
            font-size: 13px;
            color: var(--text-muted);
            margin-bottom: 16px;
            line-height: 1.5;
        }
        .card-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 16px;
            padding-top: 16px;
            border-top: 1px solid #1f1f1f;
        }
        .alert-success {
            background: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(16, 185, 129, 0.3);
            color: #34D399;
            padding: 12px 18px;
            border-radius: 8px;
            margin-bottom: 24px;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div>
                <h1>AfriCrew Email System Hub</h1>
                <p>Preview, validate and test dispatch all 23 responsive transactional email templates</p>
            </div>
            <div>
                <a href="{{ route('home') }}" class="btn-secondary">← Back to Site</a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert-success">
                ✓ {{ session('success') }}
            </div>
        @endif

        <div class="quick-test-card">
            <div>
                <strong style="display:block; font-size:15px; margin-bottom:4px;">Live Mailbox Test Dispatcher</strong>
                <span style="font-size:13px; color:var(--text-muted);">Send actual test emails through your configured mail driver (SMTP/Log)</span>
            </div>
            <form action="{{ route('emails.sendTest') }}" method="POST" class="quick-test-form">
                @csrf
                <input type="email" name="email" placeholder="recipient@example.com" required class="input-field" style="width:230px;">
                <select name="type" required class="select-field">
                    @foreach($templates as $tmpl)
                        <option value="{{ $tmpl['key'] }}">{{ $tmpl['title'] }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn-primary">Send Test Email</button>
            </form>
        </div>

        <div class="grid">
            @foreach($templates as $tmpl)
                <div class="template-card">
                    <div>
                        <div class="card-header">
                            <div>
                                <h2 class="card-title">{{ $tmpl['title'] }}</h2>
                            </div>
                            <div style="display:flex; gap:6px;">
                                @if($tmpl['badge'] === 'New')
                                    <span class="badge badge-new">New</span>
                                @elseif($tmpl['badge'] === 'Updated')
                                    <span class="badge badge-updated">Updated</span>
                                @endif
                                <span class="badge badge-recipient">{{ $tmpl['recipient'] }}</span>
                            </div>
                        </div>

                        <div class="card-subject">
                            <span style="color:#888; font-size:11px; display:block; text-transform:uppercase; margin-bottom:2px;">Subject</span>
                            {{ $tmpl['subject'] }}
                        </div>

                        <p class="card-desc">
                            <strong style="color:#aaa;">Trigger:</strong> {{ $tmpl['trigger'] }}
                        </p>
                    </div>

                    <div class="card-footer">
                        <span style="font-size:12px; color:#666;">Route: /emails/preview/{{ $tmpl['key'] }}</span>
                        <a href="{{ route('emails.preview', $tmpl['key']) }}" target="_blank" class="btn-secondary">
                            Live Preview ↗
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</body>
</html>

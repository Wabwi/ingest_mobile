<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Daily Ingest Reminder</title>
    <style>
        body {
            font-family: 'Outfit', 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: #0b0f19;
            color: #f8fafc;
            margin: 0;
            padding: 0;
            -webkit-text-size-adjust: none;
            -ms-text-size-adjust: none;
        }
        .wrapper {
            width: 100%;
            table-layout: fixed;
            background-color: #0b0f19;
            padding-top: 40px;
            padding-bottom: 40px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #151d30;
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.3);
        }
        .header {
            background-color: #151d30;
            padding: 30px;
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }
        .header h1 {
            color: #6366f1;
            margin: 0;
            font-size: 24px;
            font-weight: 800;
            letter-spacing: -0.5px;
        }
        .content {
            padding: 40px 30px;
            line-height: 1.6;
        }
        .content h2 {
            color: #f8fafc;
            font-size: 20px;
            font-weight: 700;
            margin-top: 0;
            margin-bottom: 20px;
        }
        .content p {
            color: #94a3b8;
            font-size: 16px;
            margin-bottom: 30px;
        }
        .cta-btn {
            display: inline-block;
            background-color: #6366f1;
            color: #ffffff !important;
            text-decoration: none;
            font-weight: 600;
            padding: 12px 30px;
            border-radius: 10px;
            box-shadow: 0 4px 14px 0 rgba(99, 102, 241, 0.4);
            font-size: 16px;
            text-align: center;
        }
        .footer {
            background-color: rgba(11, 15, 25, 0.4);
            padding: 20px;
            text-align: center;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
        }
        .footer p {
            color: #64748b;
            font-size: 12px;
            margin: 0;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container">
            <div class="header">
                <h1>INGEST TRACKER</h1>
            </div>
            <div class="content">
                <h2>Hello {{ $user->name }},</h2>
                <p>We noticed you haven't logged any meals or bowel movements today. Keeping a consistent daily log is the best way to track your health trends and see how well you are eating.</p>
                <p>It only takes a minute to log your breakfast, lunch, or a bowel movement on your dashboard.</p>
                <div style="text-align: center; margin-top: 40px; margin-bottom: 20px;">
                    <a href="{{ route('dashboard') }}" class="cta-btn">Go to Dashboard</a>
                </div>
            </div>
            <div class="footer">
                <p>&copy; {{ date('Y') }} Ingest Tracker. Stay healthy and consistent.</p>
            </div>
        </div>
    </div>
</body>
</html>

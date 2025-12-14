<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $announcement->title }}</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #ffffff;
            padding: 30px;
            text-align: center;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }

        .priority-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            margin-top: 10px;
            text-transform: uppercase;
        }

        .priority-high {
            background-color: #fee2e2;
            color: #dc2626;
        }

        .priority-normal {
            background-color: #d1fae5;
            color: #059669;
        }

        .priority-low {
            background-color: #dbeafe;
            color: #2563eb;
        }

        .content {
            padding: 30px;
        }

        .content h2 {
            color: #1f2937;
            font-size: 20px;
            margin-top: 0;
            margin-bottom: 15px;
        }

        .content p {
            color: #4b5563;
            margin: 0 0 15px 0;
            white-space: pre-wrap;
        }

        .meta {
            background-color: #f9fafb;
            padding: 20px 30px;
            border-top: 1px solid #e5e7eb;
        }

        .meta-item {
            display: flex;
            align-items: center;
            color: #6b7280;
            font-size: 14px;
            margin-bottom: 8px;
        }

        .meta-item:last-child {
            margin-bottom: 0;
        }

        .meta-item svg {
            width: 16px;
            height: 16px;
            margin-right: 8px;
        }

        .footer {
            background-color: #1f2937;
            color: #9ca3af;
            padding: 20px 30px;
            text-align: center;
            font-size: 12px;
        }

        .button {
            display: inline-block;
            padding: 12px 24px;
            background-color: #84cc16;
            color: #ffffff;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            margin-top: 15px;
        }

        .button:hover {
            background-color: #65a30d;
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>📢 Company Announcement</h1>
            <span class="priority-badge priority-{{ $announcement->priority }}">
                {{ ucfirst($announcement->priority) }} Priority
            </span>
        </div>

        <!-- Content -->
        <div class="content">
            <h2>{{ $announcement->title }}</h2>
            <p>{{ $announcement->content }}</p>

            <a href="{{ url('/employee/announcements/' . $announcement->id) }}" class="button">
                View Full Announcement
            </a>
        </div>

        <!-- Meta Information -->
        <div class="meta">
            <div class="meta-item">
                <span>📅</span>
                <span>Posted on {{ $announcement->created_at->format('M d, Y h:i A') }}</span>
            </div>
            <div class="meta-item">
                <span>👤</span>
                <span>From {{ $announcement->creator->name }}</span>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>This is an automated email from your company's HR system.</p>
            <p>Please do not reply to this email.</p>
        </div>
    </div>
</body>

</html>
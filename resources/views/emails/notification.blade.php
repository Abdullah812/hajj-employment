<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $notification->title }}</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            color: #333;
            direction: rtl;
            text-align: right;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            padding: 20px 0;
            border-bottom: 2px solid #eee;
        }
        .content {
            padding: 20px 0;
        }
        .footer {
            text-align: center;
            padding: 20px 0;
            border-top: 2px solid #eee;
            font-size: 12px;
            color: #666;
        }
        .button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #b47e13;
            color: #ffffff;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
        }
        .icon {
            font-size: 24px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="icon">
                <i class="{{ $notification->icon_class }}"></i>
            </div>
            <h1>{{ $notification->title }}</h1>
        </div>
        
        <div class="content">
            <p>{{ $notification->message }}</p>
            
            @if($notification->action_url)
            <div style="text-align: center;">
                <a href="{{ $notification->action_url }}" class="button">عرض التفاصيل</a>
            </div>
            @endif
        </div>
        
        <div class="footer">
            <p>تم إرسال هذا البريد الإلكتروني من نظام مناسك المشاعر للتوظيف</p>
            <p>إذا لم تكن ترغب في تلقي هذه الإشعارات، يمكنك تعديل إعدادات الإشعارات من حسابك</p>
        </div>
    </div>
</body>
</html> 
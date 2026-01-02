<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>رمز إعادة تعيين كلمة المرور - {{ $appName }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px;
            direction: rtl;
            text-align: right;
        }
        
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            position: relative;
        }
        
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 40px 30px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .header::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            animation: float 6s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(180deg); }
        }
        
        .logo {
            width: 80px;
            height: 80px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(10px);
            border: 2px solid rgba(255, 255, 255, 0.3);
            position: relative;
            z-index: 2;
        }
        
        .logo::before {
            content: '🎓';
            font-size: 40px;
        }
        
        .header h1 {
            color: #ffffff;
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 10px;
            position: relative;
            z-index: 2;
        }
        
        .header p {
            color: rgba(255, 255, 255, 0.9);
            font-size: 16px;
            position: relative;
            z-index: 2;
        }
        
        .content {
            padding: 40px 30px;
            background: #ffffff;
        }
        
        .greeting {
            font-size: 20px;
            color: #2d3748;
            margin-bottom: 20px;
            font-weight: 600;
        }
        
        .message {
            font-size: 16px;
            color: #4a5568;
            line-height: 1.6;
            margin-bottom: 30px;
        }
        
        .code-container {
            background: linear-gradient(135deg, #f7fafc 0%, #edf2f7 100%);
            border-radius: 15px;
            padding: 30px;
            text-align: center;
            margin: 30px 0;
            border: 2px solid #e2e8f0;
            position: relative;
            overflow: hidden;
        }
        
        .code-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #667eea, #764ba2, #667eea);
            background-size: 200% 100%;
            animation: shimmer 2s linear infinite;
        }
        
        @keyframes shimmer {
            0% { background-position: -200% 0; }
            100% { background-position: 200% 0; }
        }
        
        .code-label {
            font-size: 14px;
            color: #718096;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 600;
        }
        
        .verification-code {
            font-size: 36px;
            font-weight: 800;
            color: #2d3748;
            letter-spacing: 8px;
            margin: 15px 0;
            font-family: 'Courier New', monospace;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        
        .code-note {
            font-size: 14px;
            color: #718096;
            margin-top: 15px;
        }
        
        .timer-container {
            background: linear-gradient(135deg, #fed7d7 0%, #feb2b2 100%);
            border-radius: 10px;
            padding: 15px;
            margin: 20px 0;
            text-align: center;
            border: 1px solid #fc8181;
        }
        
        .timer-icon {
            font-size: 20px;
            margin-bottom: 5px;
        }
        
        .timer-text {
            font-size: 14px;
            color: #c53030;
            font-weight: 600;
        }
        
        .instructions {
            background: #f0fff4;
            border-radius: 10px;
            padding: 20px;
            margin: 25px 0;
            border-right: 4px solid #48bb78;
        }
        
        .instructions h3 {
            color: #2f855a;
            font-size: 16px;
            margin-bottom: 10px;
            font-weight: 600;
        }
        
        .instructions ol {
            color: #2d3748;
            font-size: 14px;
            line-height: 1.6;
            padding-right: 20px;
        }
        
        .instructions li {
            margin-bottom: 8px;
        }
        
        .security-notice {
            background: #fffaf0;
            border-radius: 10px;
            padding: 20px;
            margin: 25px 0;
            border-right: 4px solid #ed8936;
        }
        
        .security-notice h3 {
            color: #c05621;
            font-size: 16px;
            margin-bottom: 10px;
            font-weight: 600;
        }
        
        .security-notice p {
            color: #2d3748;
            font-size: 14px;
            line-height: 1.6;
        }
        
        .footer {
            background: #f7fafc;
            padding: 30px;
            text-align: center;
            border-top: 1px solid #e2e8f0;
        }
        
        .footer-content {
            margin-bottom: 20px;
        }
        
        .footer h3 {
            color: #2d3748;
            font-size: 18px;
            margin-bottom: 10px;
            font-weight: 600;
        }
        
        .footer p {
            color: #718096;
            font-size: 14px;
            line-height: 1.6;
            margin-bottom: 15px;
        }
        
        .contact-info {
            background: #ffffff;
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
            border: 1px solid #e2e8f0;
        }
        
        .contact-info h4 {
            color: #2d3748;
            font-size: 16px;
            margin-bottom: 10px;
            font-weight: 600;
        }
        
        .contact-info p {
            color: #4a5568;
            font-size: 14px;
            margin-bottom: 5px;
        }
        
        .social-links {
            margin: 20px 0;
        }
        
        .social-links a {
            display: inline-block;
            margin: 0 10px;
            padding: 10px;
            background: #667eea;
            color: #ffffff;
            border-radius: 50%;
            text-decoration: none;
            width: 40px;
            height: 40px;
            text-align: center;
            line-height: 20px;
            transition: all 0.3s ease;
        }
        
        .social-links a:hover {
            background: #764ba2;
            transform: translateY(-2px);
        }
        
        .copyright {
            color: #a0aec0;
            font-size: 12px;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
        }
        
        .button {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #ffffff;
            padding: 12px 30px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: 600;
            margin: 20px 0;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }
        
        .button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
        }
        
        @media (max-width: 600px) {
            .email-container {
                margin: 10px;
                border-radius: 15px;
            }
            
            .header, .content, .footer {
                padding: 20px;
            }
            
            .verification-code {
                font-size: 28px;
                letter-spacing: 4px;
            }
            
            .header h1 {
                font-size: 24px;
            }
        }
        
        .neumorphic {
            background: #e0e5ec;
            border-radius: 15px;
            box-shadow: 
                9px 9px 16px #a3b1c6,
                -9px -9px 16px #ffffff;
        }
        
        .neumorphic-inset {
            background: #e0e5ec;
            border-radius: 15px;
            box-shadow: 
                inset 9px 9px 16px #a3b1c6,
                inset -9px -9px 16px #ffffff;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="header">
            <div class="logo"></div>
            <h1>{{ $appName }}</h1>
            <p>{{ $universityName }}</p>
        </div>
        
        <!-- Content -->
        <div class="content">
            <div class="greeting">
                مرحباً {{ $user->name }}،
            </div>
            
            <div class="message">
                تلقينا طلباً لإعادة تعيين كلمة المرور الخاصة بحسابك في تطبيق {{ $appName }}. 
                لإكمال عملية إعادة تعيين كلمة المرور، يرجى استخدام رمز التفعيل التالي:
            </div>
            
            <!-- Verification Code -->
            <div class="code-container neumorphic">
                <div class="code-label">رمز التفعيل</div>
                <div class="verification-code">{{ $resetCode->code }}</div>
                <div class="code-note">
                    هذا الرمز صالح لمدة {{ $expiryMinutes }} دقائق فقط
                </div>
            </div>
            
            <!-- Timer -->
            <div class="timer-container">
                <div class="timer-icon">⏰</div>
                <div class="timer-text">
                    ينتهي هذا الرمز في: {{ $resetCode->expires_at->format('H:i') }} 
                    بتاريخ {{ $resetCode->expires_at->format('Y/m/d') }}
                </div>
            </div>
            
            <!-- Instructions -->
            <div class="instructions">
                <h3>📋 خطوات إعادة تعيين كلمة المرور:</h3>
                <ol>
                    <li>افتح تطبيق {{ $appName }} على جهازك</li>
                    <li>اضغط على "نسيت كلمة المرور؟"</li>
                    <li>أدخل رمز التفعيل المرسل إليك</li>
                    <li>اختر كلمة مرور جديدة وقوية</li>
                    <li>احفظ كلمة المرور الجديدة في مكان آمن</li>
                </ol>
            </div>
            
            <!-- Security Notice -->
            <div class="security-notice">
                <h3>🔒 تنبيه أمني مهم</h3>
                <p>
                    إذا لم تطلب إعادة تعيين كلمة المرور، يرجى تجاهل هذا البريد الإلكتروني. 
                    لا تشارك رمز التفعيل مع أي شخص آخر. إذا كنت تشك في أن حسابك قد تم اختراقه، 
                    يرجى التواصل مع فريق الدعم الفني فوراً.
                </p>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="footer">
            <div class="footer-content">
                <h3>{{ $appName }}</h3>
                <p>منصة الخدمات الطلابية الشاملة</p>
                <p>نسعى لتقديم أفضل الخدمات التعليمية والإدارية للطلاب</p>
            </div>
            
            <div class="contact-info neumorphic">
                <h4>معلومات التواصل</h4>
                <p>📧 البريد الإلكتروني: {{ $supportEmail }}</p>
                <p>🌐 الموقع الإلكتروني: {{ $appUrl }}</p>
                <p>🏛️ {{ $universityName }}</p>
            </div>
            
            <div class="social-links">
                <a href="#" title="فيسبوك">📘</a>
                <a href="#" title="تويتر">🐦</a>
                <a href="#" title="إنستغرام">📷</a>
                <a href="#" title="لينكد إن">💼</a>
            </div>
            
            <div class="copyright">
                <p>© {{ date('Y') }} {{ $appName }}. جميع الحقوق محفوظة.</p>
                <p>تم تطوير هذا التطبيق بواسطة فريق {{ $universityName }}</p>
            </div>
        </div>
    </div>
</body>
</html>


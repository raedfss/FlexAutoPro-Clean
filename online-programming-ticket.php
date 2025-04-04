<?php
session_start();
if (!isset($_SESSION['email']) || $_SESSION['user_type'] !== 'user') {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['username'];
$email = $_SESSION['email'];
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>حجز تذكرة برمجة أونلاين | FlexAuto</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Segoe UI', Tahoma, sans-serif;
            color: white;
            background-color: #1a1f2e;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            line-height: 1.6;
        }

        /* خلفية SVG متحركة */
        .svg-background {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            overflow: hidden;
            opacity: 0.5;
        }

        .svg-object {
            width: 100%;
            height: 100%;
            pointer-events: none;
        }

        header {
            background-color: rgba(0, 0, 0, 0.85);
            padding: 18px 20px;
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            color: #00ffff;
            letter-spacing: 1px;
            border-bottom: 1px solid rgba(0, 255, 255, 0.3);
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.4);
        }

        main {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 30px 20px;
            width: 100%;
        }

        .container {
            background: rgba(0, 0, 0, 0.6);
            width: 100%;
            max-width: 800px;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 0 30px rgba(0, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(66, 135, 245, 0.2);
            margin: 0 auto;
            transition: all 0.3s ease;
        }

        .container:hover {
            box-shadow: 0 0 40px rgba(0, 255, 255, 0.2);
        }

        h1 {
            font-size: 28px;
            margin-bottom: 15px;
            color: #fff;
            text-align: center;
            text-shadow: 0 0 10px rgba(0, 255, 255, 0.5);
        }

        .role {
            font-size: 16px;
            margin-bottom: 25px;
            color: #a0d0ff;
            text-align: center;
        }

        .form-style {
            text-align: right;
            margin-top: 20px;
        }

        .form-section {
            margin-bottom: 25px;
            padding-bottom: 20px;
            border-bottom: 1px dashed rgba(66, 135, 245, 0.3);
        }

        .form-section:last-of-type {
            border-bottom: none;
        }

        .section-title {
            font-size: 18px;
            color: #00ffff;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
        }

        .section-title::before {
            content: '';
            display: inline-block;
            width: 5px;
            height: 18px;
            background-color: #00ffff;
            margin-left: 8px;
            border-radius: 3px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #4287f5;
        }

        input[type="text"],
        input[type="tel"],
        input[type="email"],
        select,
        textarea {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 8px;
            border: 1px solid #3a4052;
            background-color: rgba(30, 35, 50, 0.8);
            color: white;
            font-size: 16px;
            transition: all 0.3s ease;
        }

        input[type="text"]:focus,
        input[type="tel"]:focus,
        input[type="email"]:focus,
        select:focus,
        textarea:focus {
            border-color: #4287f5;
            box-shadow: 0 0 8px rgba(66, 135, 245, 0.5);
            outline: none;
        }

        .required::after {
            content: ' *';
            color: #ff6b6b;
        }

        .optional {
            font-size: 13px;
            color: #a0a0a0;
            margin-right: 5px;
            font-weight: normal;
        }

        .input-hint {
            font-size: 12px;
            color: #a0d0ff;
            margin-top: -15px;
            margin-bottom: 15px;
            display: block;
        }

        .input-group {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }

        .input-group > div {
            flex: 1;
            min-width: 250px;
        }

        input[type="submit"] {
            background: linear-gradient(135deg, #1e90ff, #4287f5);
            color: white;
            border: none;
            border-radius: 8px;
            padding: 14px 28px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            display: block;
            margin: 25px auto;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
            transition: all 0.3s ease;
        }

        input[type="submit"]:hover {
            background: linear-gradient(135deg, #4287f5, #63b3ed);
            transform: translateY(-3px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.4);
        }

        .logout {
            text-align: center;
            margin-top: 30px;
            display: flex;
            justify-content: center;
            gap: 15px;
        }

        .logout a {
            color: #ff6b6b;
            text-decoration: none;
            font-weight: bold;
            padding: 10px 20px;
            border: 1px solid rgba(255, 107, 107, 0.4);
            border-radius: 5px;
            transition: all 0.3s;
            display: inline-block;
        }

        .logout a:hover {
            background-color: rgba(255, 107, 107, 0.1);
            border-color: rgba(255, 107, 107, 0.6);
        }
        
        .file-upload-section {
            margin-bottom: 20px;
        }
        
        .file-input-container {
            margin-bottom: 15px;
            padding: 15px;
            border-radius: 8px;
            background-color: rgba(30, 35, 50, 0.5);
            border: 1px dashed #3a4052;
            transition: all 0.3s ease;
        }
        
        .file-input-container:hover {
            border-color: #4287f5;
            background-color: rgba(30, 35, 50, 0.7);
        }
        
        .file-input {
            display: block;
            width: 100%;
            padding: 10px;
            margin: 8px 0;
            border-radius: 5px;
            background-color: rgba(20, 25, 40, 0.8);
            color: white;
            border: 1px solid #2a3040;
            cursor: pointer;
        }
        
        .file-info {
            font-size: 12px;
            color: #a0d0ff;
            margin-top: 5px;
        }

        textarea {
            resize: vertical;
            min-height: 120px;
            font-family: inherit;
        }

        .form-note {
            background-color: rgba(66, 135, 245, 0.1);
            border-right: 3px solid #4287f5;
            padding: 10px 15px;
            margin: 20px 0;
            border-radius: 5px;
            font-size: 14px;
        }

        .home-link {
            background-color: rgba(0, 150, 136, 0.1) !important;
            color: #00ffaa !important;
            border: 1px solid rgba(0, 150, 136, 0.4) !important;
        }

        .home-link:hover {
            background-color: rgba(0, 150, 136, 0.2) !important;
            border-color: rgba(0, 150, 136, 0.6) !important;
        }

        footer {
            background-color: rgba(0, 0, 0, 0.9);
            color: #eee;
            text-align: center;
            padding: 20px;
            width: 100%;
            margin-top: auto;
        }

        .footer-highlight {
            font-size: 18px;
            font-weight: bold;
            color: #00ffff;
            margin-bottom: 10px;
        }

        @media (max-width: 768px) {
            main {
                padding: 20px 15px;
            }
            
            .container {
                padding: 20px;
            }
            
            h1 {
                font-size: 24px;
            }
            
            input[type="submit"] {
                width: 100%;
            }

            .input-group {
                flex-direction: column;
                gap: 0;
            }

            .logout {
                flex-direction: column;
                gap: 10px;
            }
        }
    </style>
</head>
<body>

<div class="svg-background">
    <embed type="image/svg+xml" src="admin/admin_background.svg" class="svg-object">
</div>

<header>
    FlexAuto - حجز تذكرة برمجة أونلاين
</header>

<main>
    <div class="container">
        <h1>مرحبًا <?= htmlspecialchars($username) ?>!</h1>
        <div class="role">🧾 يمكنك هنا حجز تذكرة وشرح ما تحتاج إليه من برمجة أونلاين</div>

        <form method="POST" action="ticket_submit.php" class="form-style" enctype="multipart/form-data">
            
            <!-- بيانات التواصل -->
            <div class="form-section">
                <h3 class="section-title">📞 بيانات التواصل</h3>
                
                <div class="input-group">
                    <div>
                        <label class="required">رقم الهاتف:</label>
                        <input type="tel" name="phone" required placeholder="مثال: 0777123456 أو +962777123456" 
                               pattern="^(\+)?\d{10,15}$">
                        <span class="input-hint">سيتم استخدام هذا الرقم للتواصل بخصوص طلبك</span>
                    </div>
                    
                    <div>
                        <label>البريد الإلكتروني: <span class="optional">(مُسجل)</span></label>
                        <input type="email" name="primary_email" value="<?= htmlspecialchars($email) ?>" readonly>
                    </div>
                </div>
                
                <label>بريد إلكتروني بديل: <span class="optional">(اختياري)</span></label>
                <input type="email" name="alternative_email" placeholder="أدخل بريد إلكتروني بديل إذا كنت تريد استلام التحديثات عليه">
            </div>
            
            <!-- بيانات السيارة -->
            <div class="form-section">
                <h3 class="section-title">🚘 بيانات السيارة</h3>

                <label class="required">نوع السيارة:</label>
                <input type="text" name="car_type" required placeholder="مثال: مرسيدس E300 موديل 2023">

                <div class="input-group">
                    <div>
                        <label class="required">رقم الشاسيه:</label>
                        <input type="text" name="chassis" required placeholder="أدخل رقم الشاسيه المكون من 17 خانة"
                               pattern=".{17,17}" title="يجب أن يتكون رقم الشاسيه من 17 خانة بالضبط">
                        <span class="input-hint">يوجد على لوحة البيانات أسفل الزجاج الأمامي أو على باب السائق</span>
                    </div>
                    
                    <div>
                        <label>سنة الصنع: <span class="optional">(اختياري)</span></label>
                        <input type="text" name="year" placeholder="مثال: 2023" pattern="[0-9]{4}">
                    </div>
                </div>
            </div>

            <!-- بيانات الخدمة -->
            <div class="form-section">
                <h3 class="section-title">🛠️ بيانات الخدمة</h3>

                <label class="required">نوع الخدمة المطلوبة:</label>
                <select name="service_type" required>
                    <option value="">-- اختر الخدمة --</option>
                    <option value="key_programming">برمجة مفتاح</option>
                    <option value="airbag_reset">مسح بيانات الحادث</option>
                    <option value="ecu_tuning">تعديل برمجة</option>
                    <option value="online_diagnosis">تشخيص عن بُعد</option>
                    <option value="odometer">تعديل عداد المسافة</option>
                    <option value="ecu_repair">إصلاح كمبيوتر</option>
                    <option value="other">أخرى</option>
                </select>

                <label class="required">وصف مفصل:</label>
                <textarea name="details" rows="6" required placeholder="اكتب هنا ما الذي تريد القيام به بالتفصيل... كلما كانت المعلومات أكثر دقة، كلما كان الحل أسرع وأفضل."></textarea>
            </div>
            
            <!-- ملفات وصور -->
            <div class="form-section">
                <h3 class="section-title">📂 ملفات وصور</h3>
                
                <div class="form-note">
                    تحميل الملفات والصور سيساعد فريقنا على فهم احتياجاتك بشكل أفضل وتوفير الخدمة المناسبة بسرعة أكبر.
                </div>
                
                <div class="file-upload-section">
                    <div class="file-input-container">
                        <label>📊 تحميل سوفوير دمب (اختياري):</label>
                        <input type="file" name="software_dump" class="file-input" accept=".bin,.hex,.kfx,.orig">
                        <div class="file-info">صيغ الملفات المقبولة: .bin, .hex, .kfx, .orig (الحجم الأقصى: 10 ميجابايت)</div>
                    </div>
                    
                    <div class="file-input-container">
                        <label>🖼️ تحميل صور (اختياري):</label>
                        <input type="file" name="images[]" class="file-input" accept="image/*" multiple>
                        <div class="file-info">يمكنك تحميل أكثر من صورة (الحد الأقصى: 5 صور، 2 ميجابايت لكل صورة)</div>
                    </div>
                </div>
            </div>

            <input type="submit" value="📨 إرسال التذكرة">
        </form>

        <div class="logout">
            <a href="dashboard.php" class="home-link">🏠 الرئيسية</a>
            <a href="tickets.php">📋 تذاكري السابقة</a>
            <a href="logout.php">🔓 تسجيل الخروج</a>
        </div>
    </div>
</main>

<footer>
    <div class="footer-highlight">ذكاءٌ في الخدمة، سرعةٌ في الاستجابة، جودةٌ بلا حدود.</div>
    <div>Smart service, fast response, unlimited quality.</div>
    <div style="margin-top: 8px;">📧 raedfss@hotmail.com | ☎️ +962796519007</div>
    <div style="margin-top: 5px;">&copy; <?= date('Y') ?> FlexAuto. جميع الحقوق محفوظة.</div>
</footer>

</body>
</html>
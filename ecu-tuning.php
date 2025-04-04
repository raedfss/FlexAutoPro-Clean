<?php
session_start();
if (!isset($_SESSION['email']) || $_SESSION['user_type'] !== 'user') {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['username'];
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تعديل برمجة السيارة | FlexAuto</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        <?php include 'style_home.css'; ?>
    </style>
</head>
<body>

<div class="svg-background">
    <embed type="image/svg+xml" src="admin/admin_background.svg" class="svg-object">
</div>

<header>
    FlexAuto - تعديل برمجة السيارة
</header>

<main>
    <div class="container">
        <h1>مرحبًا <?= htmlspecialchars($username) ?>!</h1>
        <div class="role">🚗 اختر نوع التعديل وارفع ملف البرمجة</div>

        <form method="POST" action="#" enctype="multipart/form-data" class="form-style">
            <label>نوع التعديل:</label><br>
            <select name="tuning_type" required>
                <option value="speed_limit">إزالة محدد السرعة</option>
                <option value="dpf_delete">إزالة DPF</option>
                <option value="option_add">إضافة خيارات جديدة</option>
            </select><br><br>

            <label>رقم الشاسيه:</label><br>
            <input type="text" name="chassis" required><br><br>

            <label>تحميل ملف البرمجة:</label><br>
            <input type="file" name="ecu_file" required><br><br>

            <input type="submit" value="طلب التعديل">
        </form>

        <div class="logout">
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

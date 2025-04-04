<?php
// includes/db.php

// إعدادات الاتصال بقاعدة البيانات
$host = 'localhost';
$dbname = 'flexauto'; // تأكد أن اسم قاعدة البيانات مطابق
$username = 'root';   // غيّر حسب بياناتك
$password = '';       // غيّر حسب إعدادات السيرفر

try {
    // إنشاء الاتصال باستخدام PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);

    // تعيين خصائص الاتصال
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    // في حال فشل الاتصال
    die("فشل الاتصال بقاعدة البيانات: " . $e->getMessage());
}
?>

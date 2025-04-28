<?php
// FlexAutoPro - db_connect.php
// إعداد الاتصال بقاعدة البيانات - يدعم XAMPP (MySQL) أو Neon (PostgreSQL) تلقائيًا

// نبدأ الجلسة إذا لم تكن قد بدأت
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// التحقق من بيئة العمل (محلي أو إنتاج)
$isLocalhost = in_array($_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1']);

if ($isLocalhost) {
    // 📌 Localhost - XAMPP - MySQL
    $db_type    = 'mysql';
    $db_host    = '127.0.0.1';
    $db_name    = 'flexauto';  // تأكد من اسم قاعدتك على XAMPP
    $db_user    = 'root';
    $db_pass    = '';
    $db_charset = 'utf8mb4';
    $dsn        = "mysql:host=$db_host;dbname=$db_name;charset=$db_charset";
} else {
    // 📌 Production - Neon - PostgreSQL
    $db_type  = 'pgsql';
    $db_host  = 'ep-silent-recipe-a4whzvsp-pooler.us-east-1.aws.neon.tech';
    $db_port  = '5432';
    $db_name  = 'neondb';
    $db_user  = 'neondb_owner';
    $db_pass  = 'npg_eWfsJy0PN5EQ';
    $dsn      = "pgsql:host=$db_host;port=$db_port;dbname=$db_name;sslmode=require";
}

try {
    $pdoOptions = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ];

    $pdo = new PDO($dsn, $db_user, $db_pass, $pdoOptions);

} catch (PDOException $e) {
    die("Database Connection Failed: " . $e->getMessage());
}
?>

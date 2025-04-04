<?php
// ==========================
// FlexAuto - Logout Script
// ==========================

session_start();          // بدء الجلسة الحالية
session_unset();          // حذف جميع متغيرات الجلسة
session_destroy();        // تدمير الجلسة نهائيًا

// إعادة التوجيه إلى صفحة تسجيل الدخول
header("Location: login.php");
exit;
?>

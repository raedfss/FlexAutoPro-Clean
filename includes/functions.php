<?php
// includes/functions.php

// دالة: تحويل التاريخ لصيغة عربية (مثال)
function formatDate($datetime) {
    return date("Y-m-d H:i", strtotime($datetime));
}

// دالة: عرض رسالة تنبيه (Bootstrap أو عادي)
function showMessage($type, $message) {
    echo "<div class='alert alert-$type'>$message</div>";
}

// دالة: التحقق من كون المستخدم مشرف
function isAdmin() {
    return (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin');
}

// دالة: توليد كود عشوائي (مثلاً لتفعيل أو رقم طلب)
function generateCode($length = 8) {
    return substr(str_shuffle('ABCDEFGHJKLMNPQRSTUVWXYZ23456789'), 0, $length);
}


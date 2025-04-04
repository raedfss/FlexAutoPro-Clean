<?php
$conn = new mysqli("localhost", "root", "", "flexauto");

// فحص الاتصال
if ($conn->connect_error) {
    die("فشل الاتصال بقاعدة البيانات: " . $conn->connect_error);
}
?>

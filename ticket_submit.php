<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['email']) || $_SESSION['user_type'] !== 'user') {
    header("Location: login.php");
    exit;
}

// استقبال القيم
$username = $_SESSION['username'];
$primary_email = $_SESSION['email'];
$alt_email = $_POST['alternative_email'] ?? '';
$phone = $_POST['phone'];
$car_type = $_POST['car_type'];
$chassis = $_POST['chassis'];
$year = $_POST['year'] ?? '';
$service_type = $_POST['service_type'];
$details = $_POST['details'];

// رفع ملفات السوفوير
$dump_filename = '';
if (!empty($_FILES['software_dump']['name'])) {
    $upload_dir = 'uploads/dumps/';
    if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
    $dump_filename = $upload_dir . time() . '_' . basename($_FILES['software_dump']['name']);
    move_uploaded_file($_FILES['software_dump']['tmp_name'], $dump_filename);
}

// رفع الصور
$image_paths = [];
if (!empty($_FILES['images']['name'][0])) {
    $img_dir = 'uploads/images/';
    if (!is_dir($img_dir)) mkdir($img_dir, 0777, true);
    foreach ($_FILES['images']['name'] as $key => $img_name) {
        $tmp_name = $_FILES['images']['tmp_name'][$key];
        $new_name = $img_dir . time() . '_' . basename($img_name);
        move_uploaded_file($tmp_name, $new_name);
        $image_paths[] = $new_name;
    }
}
$image_files = implode(',', $image_paths);

// حفظ التذكرة في قاعدة البيانات
$sql = "INSERT INTO tickets (username, primary_email, alt_email, phone, car_type, chassis, year, service_type, details, dump_file, image_files)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "sssssssssss", $username, $primary_email, $alt_email, $phone, $car_type, $chassis, $year, $service_type, $details, $dump_filename, $image_files);

if (mysqli_stmt_execute($stmt)) {
    $ticket_id = mysqli_insert_id($conn);

    // إرسال بريد تنبيه
    $to = "raedfss@hotmail.com";
    $subject = "🆕 تذكرة جديدة من $username - FlexAuto";
    $body = "تم استلام تذكرة جديدة.\n\n"
          . "الاسم: $username\n"
          . "الهاتف: $phone\n"
          . "البريد: $primary_email\n"
          . "البريد البديل: $alt_email\n"
          . "السيارة: $car_type - $year\n"
          . "الشاسيه: $chassis\n"
          . "الخدمة: $service_type\n\n"
          . "الوصف:\n$details\n\n"
          . "رقم التذكرة: FLEX-$ticket_id\n"
          . "تم الإنشاء في: " . date("Y-m-d H:i");

    $headers = "From: noreply@flexauto.com";

    mail($to, $subject, $body, $headers);

    // إعادة توجيه إلى صفحة الشكر مع رقم التذكرة
    header("Location: thank_you.php?id=$ticket_id");
    exit;
} else {
    echo "❌ فشل في حفظ التذكرة: " . mysqli_error($conn);
}
?>

<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';
require_once 'includes/header.php';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $vehicle_type = trim($_POST['vehicle_type'] ?? '');
    $ecu_number = trim($_POST['ecu_number'] ?? '');
    $file = $_FILES['eeprom_file'] ?? null;

    if (empty($vehicle_type) || empty($ecu_number) || !$file) {
        $error = "جميع الحقول مطلوبة.";
    } else {
        $allowed_exts = ['bin', 'hex'];
        $file_ext = pathinfo($file['name'], PATHINFO_EXTENSION);

        if (!in_array(strtolower($file_ext), $allowed_exts)) {
            $error = "الملف غير مدعوم. يجب أن يكون bin أو hex.";
        } elseif ($file['size'] > 2 * 1024 * 1024) {
            $error = "حجم الملف كبير. الحد الأقصى 2MB.";
        } else {
            $filename = uniqid("eeprom_") . "." . $file_ext;
            $destination = "uploads/" . $filename;

            if (move_uploaded_file($file['tmp_name'], $destination)) {
                $stmt = $pdo->prepare("INSERT INTO airbag_resets (user_id, ecu_number, vehicle_type, uploaded_file) VALUES (?, ?, ?, ?)");
                $stmt->execute([$_SESSION['user_id'], $ecu_number, $vehicle_type, $filename]);
                $success = "تم إرسال طلب المسح بنجاح.";
            } else {
                $error = "فشل في رفع الملف. حاول مجددًا.";
            }
        }
    }
}
?>

<h2>طلب مسح بيانات الحادث (Airbag)</h2>

<?php
if (!empty($error)) showMessage("danger", $error);
if (!empty($success)) showMessage("success", $success);
?>

<form method="POST" action="airbag_reset.php" enctype="multipart/form-data">
    <label>نوع السيارة:</label><br>
    <input type="text" name="vehicle_type" required><br><br>

    <label>رقم وحدة ECU:</label><br>
    <input type="text" name="ecu_number" required><br><br>

    <label>ملف EEPROM (.bin أو .hex):</label><br>
    <input type="file" name="eeprom_file" accept=".bin,.hex" required><br><br>

    <button type="submit">إرسال الطلب</button>
</form>

<?php require_once 'includes/footer.php'; ?>

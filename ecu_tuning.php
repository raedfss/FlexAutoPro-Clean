<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';
require_once 'includes/header.php';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ecu_model = trim($_POST['ecu_model'] ?? '');
    $modifications = trim($_POST['modifications'] ?? '');
    $file = $_FILES['ecu_file'] ?? null;

    if (empty($ecu_model) || empty($modifications) || !$file) {
        $error = "جميع الحقول مطلوبة.";
    } else {
        $allowed_exts = ['bin', 'hex', 'ori', 'mod'];
        $file_ext = pathinfo($file['name'], PATHINFO_EXTENSION);

        if (!in_array(strtolower($file_ext), $allowed_exts)) {
            $error = "الملف غير مدعوم. يجب أن يكون bin, hex, ori, أو mod.";
        } elseif ($file['size'] > 3 * 1024 * 1024) {
            $error = "حجم الملف كبير. الحد الأقصى 3MB.";
        } else {
            $filename = uniqid("ecu_") . "." . $file_ext;
            $destination = "uploads/" . $filename;

            if (move_uploaded_file($file['tmp_name'], $destination)) {
                $stmt = $pdo->prepare("INSERT INTO ecu_tuning_requests (user_id, ecu_model, modifications, uploaded_file) VALUES (?, ?, ?, ?)");
                $stmt->execute([$_SESSION['user_id'], $ecu_model, $modifications, $filename]);
                $success = "تم إرسال طلب تعديل الـ ECU بنجاح.";
            } else {
                $error = "فشل في رفع الملف. حاول مجددًا.";
            }
        }
    }
}
?>

<h2>طلب تعديل برمجة ECU</h2>

<?php
if (!empty($error)) showMessage("danger", $error);
if (!empty($success)) showMessage("success", $success);
?>

<form method="POST" action="ecu_tuning.php" enctype="multipart/form-data">
    <label>موديل وحدة ECU:</label><br>
    <input type="text" name="ecu_model" required><br><br>

    <label>التعديلات المطلوبة:</label><br>
    <textarea name="modifications" rows="4" required></textarea><br><br>

    <label>ملف ECU (.bin, .hex, .ori, .mod):</label><br>
    <input type="file" name="ecu_file" accept=".bin,.hex,.ori,.mod" required><br><br>

    <button type="submit">إرسال الطلب</button>
</form>

<?php require_once 'includes/footer.php'; ?>

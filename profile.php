<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';
require_once 'includes/header.php';

$user_id = $_SESSION['user_id'];
$success = '';
$error = '';

// جلب بيانات المستخدم الحالية
$stmt = $pdo->prepare("SELECT username, email FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// عند تقديم النموذج
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_username = trim($_POST['username']);
    $new_password = $_POST['password'];
    $confirm = $_POST['confirm'];

    if (empty($new_username)) {
        $error = "يجب إدخال الاسم.";
    } elseif (!empty($new_password) && $new_password !== $confirm) {
        $error = "كلمتا المرور غير متطابقتين.";
    } else {
        // تحديث الاسم
        $stmt = $pdo->prepare("UPDATE users SET username = ? WHERE id = ?");
        $stmt->execute([$new_username, $user_id]);
        $_SESSION['username'] = $new_username;

        // تحديث كلمة المرور إن وُجدت
        if (!empty($new_password)) {
            $hashed = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt->execute([$hashed, $user_id]);
        }

        $success = "تم تحديث بيانات الحساب بنجاح.";
    }
}
?>

<h2>الملف الشخصي</h2>

<?php
if (!empty($error)) {
    showMessage("danger", $error);
}
if (!empty($success)) {
    showMessage("success", $success);
}
?>

<form method="POST" action="profile.php">
    <label>الاسم الكامل:</label><br>
    <input type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required><br><br>

    <label>البريد الإلكتروني (غير قابل للتعديل):</label><br>
    <input type="email" value="<?php echo htmlspecialchars($user['email']); ?>" disabled><br><br>

    <label>كلمة مرور جديدة (اختياري):</label><br>
    <input type="password" name="password"><br><br>

    <label>تأكيد كلمة المرور:</label><br>
    <input type="password" name="confirm"><br><br>

    <button type="submit">تحديث البيانات</button>
</form>

<?php require_once 'includes/footer.php'; ?>

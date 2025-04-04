<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';
require_once 'includes/header.php';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        $error = "جميع الحقول مطلوبة.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "البريد الإلكتروني غير صالح.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO contacts (name, email, subject, message) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $email, $subject, $message]);
        $success = "تم إرسال رسالتك بنجاح. سنقوم بالرد قريبًا.";
    }
}
?>

<h2>تواصل معنا</h2>

<?php
if (!empty($error)) showMessage("danger", $error);
if (!empty($success)) showMessage("success", $success);
?>

<form method="POST" action="contact.php">
    <label>الاسم الكامل:</label><br>
    <input type="text" name="name" required><br><br>

    <label>البريد الإلكتروني:</label><br>
    <input type="email" name="email" required><br><br>

    <label>عنوان الرسالة:</label><br>
    <input type="text" name="subject" required><br><br>

    <label>الرسالة:</label><br>
    <textarea name="message" rows="5" required></textarea><br><br>

    <button type="submit">إرسال</button>
</form>

<?php require_once 'includes/footer.php'; ?>

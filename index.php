<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once '../includes/header.php';

// تأكيد أن المستخدم مشرف فقط
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}
?>

<h2>لوحة تحكم المشرف</h2>

<p>مرحبًا، <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong> 👋</p>
<p>هذه هي لوحة تحكم الإدارة. يمكنك من هنا إدارة النظام بالكامل.</p>

<div class="admin-links">
    <ul>
        <li><a href="users.php">👥 إدارة المستخدمين</a></li>
        <li><a href="requests.php">📄 متابعة الطلبات</a></li>
        <li><a href="logs.php">🕵️ سجل العمليات</a></li>
        <li><a href="settings.php">⚙️ إعدادات النظام</a></li>
    </ul>
</div>

<style>
.admin-links ul {
    list-style: none;
    padding: 0;
}

.admin-links ul li {
    margin: 10px 0;
}

.admin-links ul li a {
    display: inline-block;
    padding: 10px 15px;
    background-color: #004080;
    color: #fff;
    text-decoration: none;
    border-radius: 6px;
}

.admin-links ul li a:hover {
    background-color: #0066cc;
}
</style>

<?php require_once '../includes/footer.php'; ?>

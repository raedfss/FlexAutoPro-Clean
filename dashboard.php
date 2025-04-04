<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';
require_once 'includes/header.php';
?>

<h2>مرحبًا، <?php echo htmlspecialchars($_SESSION['username']); ?> 👋</h2>

<p>أهلاً بك في لوحة التحكم الخاصة بك على منصة <strong>FlexAuto</strong>.</p>

<div class="dashboard-links">
    <ul>
        <li><a href="request_code.php">🔐 طلب كود برمجي</a></li>
        <li><a href="airbag_reset.php">💥 مسح بيانات الحوادث</a></li>
        <li><a href="ecu_tuning.php">⚙️ تعديل برمجة ECU</a></li>
        <li><a href="notifications.php">🔔 عرض الإشعارات</a></li>
        <li><a href="messages.php">📩 الرسائل</a></li>
        <li><a href="profile.php">👤 إدارة الملف الشخصي</a></li>
    </ul>
</div>

<style>
.dashboard-links ul {
    list-style: none;
    padding: 0;
}

.dashboard-links ul li {
    margin: 10px 0;
}

.dashboard-links ul li a {
    display: inline-block;
    padding: 10px 15px;
    background-color: #004080;
    color: #fff;
    text-decoration: none;
    border-radius: 6px;
}

.dashboard-links ul li a:hover {
    background-color: #0066cc;
}
</style>

<?php require_once 'includes/footer.php'; ?>

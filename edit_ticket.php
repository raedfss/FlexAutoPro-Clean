<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: ../login.php");
    exit;
}

// استيراد ملف قاعدة البيانات الذي يستخدم PDO
require_once 'db.php';

$username = $_SESSION['username'];

// استخدام PDO بدلاً من mysqli
try {
    // استعلام للحصول على تذاكر المستخدم باستخدام PDO
    $stmt = $pdo->prepare("SELECT * FROM tickets WHERE username = ? ORDER BY created_at DESC");
    $stmt->execute([$username]);
    $tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("خطأ في الاتصال بقاعدة البيانات: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تذاكري | FlexAuto</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, sans-serif;
            background-color: #1a1f2e;
            color: white;
            margin: 0;
            padding: 0;
        }

        .svg-background {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            opacity: 0.5;
        }

        .svg-object {
            width: 100%;
            height: 100%;
        }

        header {
            background-color: rgba(0, 0, 0, 0.85);
            padding: 18px;
            text-align: center;
            font-size: 24px;
            color: #00ffff;
            font-weight: bold;
            border-bottom: 1px solid rgba(0, 255, 255, 0.3);
        }

        main {
            padding: 30px 20px;
            max-width: 1000px;
            margin: auto;
        }

        .container {
            background: rgba(0, 0, 0, 0.6);
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 0 30px rgba(0, 255, 255, 0.1);
            border: 1px solid rgba(66, 135, 245, 0.2);
        }

        h1 {
            text-align: center;
            color: #00ffff;
            margin-bottom: 30px;
        }

        .tickets-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .tickets-table th, .tickets-table td {
            padding: 12px 15px;
            text-align: right;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .tickets-table th {
            background-color: rgba(0, 0, 0, 0.3);
            color: #00ffff;
        }

        .tickets-table tr:hover {
            background-color: rgba(0, 255, 255, 0.05);
        }

        .btn {
            padding: 8px 15px;
            border-radius: 30px;
            font-weight: bold;
            border: none;
            cursor: pointer;
            transition: 0.3s ease;
            text-decoration: none;
            display: inline-block;
            font-size: 14px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #1e90ff, #4287f5);
            color: white;
        }

        .btn-new {
            background: linear-gradient(135deg, #00c853, #00e676);
            color: white;
            font-size: 16px;
            padding: 12px 25px;
            margin-bottom: 20px;
        }

        .status-new {
            color: #00e676;
        }

        .status-in-progress {
            color: #ffeb3b;
        }

        .status-completed {
            color: #00ffff;
        }

        .no-tickets {
            text-align: center;
            color: #a0d0ff;
            font-size: 18px;
            margin: 30px 0;
        }

        footer {
            background-color: rgba(0, 0, 0, 0.9);
            color: #eee;
            text-align: center;
            padding: 20px;
            margin-top: 50px;
        }

        .footer-highlight {
            font-size: 18px;
            font-weight: bold;
            color: #00ffff;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

<div class="svg-background">
    <embed type="image/svg+xml" src="../admin/admin_background.svg" class="svg-object">
</div>

<header>FlexAuto - تذاكري</header>

<main>
    <div class="container">
        <h1>تذاكر الخدمة الخاصة بك</h1>
        
        <div style="text-align: center;">
            <a href="../new_ticket.php" class="btn btn-new"><i class="fas fa-plus-circle"></i> إنشاء تذكرة جديدة</a>
        </div>

        <?php if (count($tickets) > 0): ?>
            <table class="tickets-table">
                <thead>
                    <tr>
                        <th>رقم التذكرة</th>
                        <th>نوع الخدمة</th>
                        <th>نوع السيارة</th>
                        <th>تاريخ الإنشاء</th>
                        <th>الحالة</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tickets as $ticket): ?>
                        <tr>
                            <td>FLEX-<?= $ticket['id'] ?></td>
                            <td><?= htmlspecialchars($ticket['service_type']) ?></td>
                            <td><?= htmlspecialchars($ticket['car_type']) ?></td>
                            <td><?= date('Y-m-d H:i', strtotime($ticket['created_at'])) ?></td>
                            <td>
                                <?php 
                                if ($ticket['status'] == 'new'): ?>
                                    <span class="status-new">جديدة</span>
                                <?php elseif ($ticket['status'] == 'in_progress'): ?>
                                    <span class="status-in-progress">قيد المعالجة</span>
                                <?php elseif ($ticket['status'] == 'completed'): ?>
                                    <span class="status-completed">مكتملة</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="../ticket_details.php?id=<?= $ticket['id'] ?>" class="btn btn-primary"><i class="fas fa-eye"></i> عرض</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="no-tickets">
                <p>ليس لديك أي تذاكر خدمة حتى الآن.</p>
                <p>يمكنك إنشاء تذكرة جديدة للحصول على المساعدة.</p>
            </div>
        <?php endif; ?>
    </div>
</main>

<footer>
    <div class="footer-highlight">ذكاءٌ في الخدمة، سرعةٌ في الاستجابة، جودةٌ بلا حدود.</div>
    <div>Smart service, fast response, unlimited quality.</div>
    <div style="margin-top: 8px;">📧 raedfss@hotmail.com | ☎️ +962796519007</div>
    <div style="margin-top: 5px;">&copy; <?= date('Y') ?> FlexAuto. جميع الحقوق محفوظة.</div>
</footer>

</body>
</html>
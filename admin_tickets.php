<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['email']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// تحديث حالة التذكرة عند النقر على "تمت المراجعة"
if (isset($_GET['mark_seen'])) {
    $id = intval($_GET['mark_seen']);
    $conn->query("UPDATE tickets SET is_seen = 1 WHERE id = $id");
    header("Location: admin_tickets.php");
    exit;
}

// إلغاء التذكرة (يمكن تعديل هذا حسب منطق التطبيق)
if (isset($_GET['cancel_ticket'])) {
    $id = intval($_GET['cancel_ticket']);
    // هنا يمكنك إضافة الكود الخاص بإلغاء التذكرة حسب احتياجاتك
    // مثال: $conn->query("UPDATE tickets SET status = 'cancelled' WHERE id = $id");
    header("Location: admin_tickets.php");
    exit;
}

// جلب كل التذاكر - نحافظ على الاستعلام الأصلي
$tickets = $conn->query("SELECT * FROM tickets ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>نظام إدارة تذاكر FlexAuto</title>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts - Cairo for Arabic -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #0099ff;
            --primary-dark: #0077cc;
            --secondary: #00d9ff;
            --dark-bg: #0f172a;
            --darker-bg: #070e1b;
            --card-bg: #1a1f2e;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --light-text: #f8fafc;
            --muted-text: #94a3b8;
            --border-color: rgba(255, 255, 255, 0.1);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Cairo', sans-serif;
            background-color: var(--dark-bg);
            color: var(--light-text);
            margin: 0;
            padding: 0;
            min-height: 100vh;
            position: relative;
            padding-bottom: 80px;
        }

        /* ---- الهيدر ---- */
        .top-bar {
            height: 4px;
            background: linear-gradient(90deg, var(--primary), var(--secondary));
        }
        
        header {
            background-color: var(--darker-bg);
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
            z-index: 100;
        }
        
        .logo {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 22px;
            font-weight: bold;
            color: var(--secondary);
        }
        
        .logo i {
            font-size: 24px;
        }
        
        .admin-controls {
            display: flex;
            gap: 15px;
        }
        
        .admin-controls a {
            text-decoration: none;
            color: var(--light-text);
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 8px 12px;
            border-radius: 4px;
            font-size: 14px;
            transition: all 0.2s ease;
        }
        
        .admin-controls a:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }

        /* ---- المحتوى الرئيسي ---- */
        .container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 20px;
        }
        
        .page-title {
            margin-bottom: 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .title-text {
            font-size: 24px;
            font-weight: 700;
            color: var(--secondary);
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .actions-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .ticket-stats {
            display: flex;
            gap: 15px;
        }
        
        .stat-item {
            background-color: var(--card-bg);
            padding: 10px 15px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        
        .stat-item i {
            color: var(--primary);
        }
        
        .search-box {
            display: flex;
            gap: 10px;
        }
        
        .search-box input {
            padding: 8px 15px;
            border-radius: 6px;
            border: 1px solid var(--border-color);
            background-color: rgba(255, 255, 255, 0.1);
            color: var(--light-text);
            font-family: 'Cairo', sans-serif;
        }
        
        .search-box input::placeholder {
            color: var(--muted-text);
        }
        
        .search-box button {
            padding: 8px 15px;
            border-radius: 6px;
            background-color: var(--primary);
            color: white;
            border: none;
            cursor: pointer;
            font-family: 'Cairo', sans-serif;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: all 0.3s ease;
        }
        
        .search-box button:hover {
            background-color: var(--primary-dark);
        }

        /* ---- جدول التذاكر ---- */
        .tickets-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            background-color: var(--card-bg);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }
        
        .tickets-table th {
            background-color: rgba(0, 0, 0, 0.2);
            color: var(--secondary);
            font-weight: 600;
            text-align: center;
            padding: 16px;
            font-size: 14px;
            letter-spacing: 0.5px;
            border-bottom: 2px solid rgba(0, 153, 255, 0.2);
        }
        
        .tickets-table td {
            padding: 16px;
            text-align: center;
            border-bottom: 1px solid var(--border-color);
            vertical-align: middle;
        }
        
        .tickets-table tr:last-child td {
            border-bottom: none;
        }
        
        .tickets-table tr {
            transition: all 0.2s ease;
        }
        
        .tickets-table tr:hover {
            background-color: rgba(255, 255, 255, 0.05);
        }

        /* ---- أنماط عناصر الجدول ---- */
        .ticket-id {
            font-weight: 700;
            color: var(--primary);
            white-space: nowrap;
        }
        
        .car-info {
            display: flex;
            flex-direction: column;
            gap: 4px;
            align-items: center;
        }
        
        .car-model {
            font-weight: 600;
        }
        
        .chassis-number {
            font-size: 12px;
            color: var(--muted-text);
        }
        
        .service-tag {
            background: rgba(0, 153, 255, 0.15);
            color: var(--secondary);
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
            border: 1px solid rgba(0, 153, 255, 0.3);
        }
        
        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            font-size: 13px;
            font-weight: 600;
            width: max-content;
            margin: 0 auto;
        }
        
        .status-reviewed {
            background-color: rgba(16, 185, 129, 0.15);
            color: var(--success);
            border: 1px solid rgba(16, 185, 129, 0.3);
        }
        
        .status-pending {
            background-color: rgba(245, 158, 11, 0.15);
            color: var(--warning);
            border: 1px solid rgba(245, 158, 11, 0.3);
        }
        
        /* ---- زر الإجراءات ---- */
        .action-btn {
            padding: 8px 16px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
            background-color: var(--primary);
            color: white;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border: none;
            cursor: pointer;
            font-family: 'Cairo', sans-serif;
            font-size: 13px;
        }
        
        .action-btn:hover {
            background-color: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 3px 10px rgba(0, 153, 255, 0.3);
        }
        
        .btn-disabled {
            background-color: #475569;
            cursor: default;
        }
        
        .btn-disabled:hover {
            background-color: #475569;
            transform: none;
            box-shadow: none;
        }
        
        /* ---- قائمة الإجراءات المنسدلة ---- */
        .dropdown {
            position: relative;
            display: inline-block;
        }
        
        .dropdown-content {
            display: none;
            position: absolute;
            right: 0;
            background-color: var(--dark-bg);
            min-width: 180px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            z-index: 1;
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid var(--border-color);
        }
        
        .dropdown:hover .dropdown-content {
            display: block;
        }
        
        .dropdown-item {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 12px 16px;
            text-decoration: none;
            color: var(--light-text);
            transition: all 0.2s ease;
            font-size: 13px;
        }
        
        .dropdown-item:hover {
            background-color: rgba(0, 153, 255, 0.1);
        }
        
        .dropdown-divider {
            height: 1px;
            background-color: var(--border-color);
        }
        
        .dropdown-item.delete {
            color: var(--danger);
        }
        
        .dropdown-item.delete:hover {
            background-color: rgba(239, 68, 68, 0.1);
        }
        
        .more-btn {
            background-color: transparent;
            border: 1px solid var(--border-color);
            color: var(--light-text);
            padding: 8px;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        
        .more-btn:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }

        /* ---- الفوتر ---- */
        footer {
            background-color: var(--darker-bg);
            color: var(--muted-text);
            text-align: center;
            padding: 20px;
            position: absolute;
            bottom: 0;
            width: 100%;
            border-top: 1px solid var(--border-color);
        }

        /* ---- التوافقية مع الموبايل ---- */
        @media (max-width: 992px) {
            .actions-row {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }
            
            .search-box {
                width: 100%;
            }
            
            .search-box input {
                flex: 1;
            }
        }
        
        @media (max-width: 768px) {
            .hide-mobile {
                display: none;
            }
            
            .tickets-table th, 
            .tickets-table td {
                padding: 12px 8px;
                font-size: 12px;
            }
            
            .action-btn {
                padding: 6px 10px;
                font-size: 12px;
            }
            
            .admin-controls {
                display: none;
            }
            
            .mobile-menu-btn {
                display: block;
                background: none;
                border: none;
                color: var(--light-text);
                font-size: 20px;
                cursor: pointer;
            }
        }
    </style>
</head>
<body>

<div class="top-bar"></div>

<header>
    <div class="logo">
        <i class="fas fa-ticket-alt"></i>
        <span>FlexAuto</span> | نظام إدارة تذاكر البرمجة
    </div>
    <div class="admin-controls">
        <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> لوحة التحكم</a>
        <a href="customers.php"><i class="fas fa-users"></i> العملاء</a>
        <a href="reports.php"><i class="fas fa-chart-bar"></i> التقارير</a>
        <a href="settings.php"><i class="fas fa-cog"></i> الإعدادات</a>
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> تسجيل الخروج</a>
    </div>
    <button class="mobile-menu-btn hide-mobile">
        <i class="fas fa-bars"></i>
    </button>
</header>

<div class="container">
    <div class="page-title">
        <div class="title-text">
            <i class="fas fa-clipboard-list"></i>
            إدارة التذاكر
        </div>
    </div>
    
    <div class="actions-row">
        <div class="ticket-stats">
            <?php
            $total = $tickets->num_rows;
            // ملاحظة: يجب التأكد من وجود عمود is_seen في جدول البيانات
            $seen_count = 0;
            $unseen_count = 0;
            
            // إعادة ضبط مؤشر النتائج
            $tickets->data_seek(0);
            
            // حساب التذاكر المراجعة وغير المراجعة
            while ($row = $tickets->fetch_assoc()) {
                if (isset($row['is_seen']) && $row['is_seen'] == 1) {
                    $seen_count++;
                } else {
                    $unseen_count++;
                }
            }
            
            // إعادة ضبط مؤشر النتائج مرة أخرى
            $tickets->data_seek(0);
            ?>
            <div class="stat-item">
                <i class="fas fa-ticket-alt"></i>
                <span>إجمالي التذاكر: <strong><?= $total ?></strong></span>
            </div>
            <div class="stat-item">
                <i class="fas fa-check-circle"></i>
                <span>تمت المراجعة: <strong><?= $seen_count ?></strong></span>
            </div>
            <div class="stat-item">
                <i class="fas fa-clock"></i>
                <span>بانتظار المراجعة: <strong><?= $unseen_count ?></strong></span>
            </div>
        </div>
        
        <div class="search-box">
            <input type="text" placeholder="بحث عن تذكرة..." id="searchInput">
            <button>
                <i class="fas fa-search"></i>
                بحث
            </button>
        </div>
    </div>
    
    <table class="tickets-table">
        <thead>
            <tr>
                <th>رقم</th>
                <th>العميل</th>
                <th>الهاتف</th>
                <th>السيارة</th>
                <th class="hide-mobile">الشاسيه</th>
                <th>الخدمة</th>
                <th>الحالة</th>
                <th>الإجراءات</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $tickets->fetch_assoc()): ?>
            <tr>
                <td><span class="ticket-id">FLEX-<?= $row['id'] ?></span></td>
                <td><?= htmlspecialchars($row['username']) ?></td>
                <td><?= htmlspecialchars($row['phone']) ?></td>
                <td>
                    <div class="car-info">
                        <div class="car-model"><?= htmlspecialchars($row['car_type']) ?></div>
                        <div class="chassis-number hide-mobile"><?= substr(htmlspecialchars($row['chassis']), 0, 8) ?>...</div>
                    </div>
                </td>
                <td class="hide-mobile"><?= htmlspecialchars($row['chassis']) ?></td>
                <td><span class="service-tag"><?= htmlspecialchars($row['service_type']) ?></span></td>
                <td>
                    <?php if (isset($row['is_seen']) && $row['is_seen'] == 1): ?>
                        <div class="status-badge status-reviewed">
                            <i class="fas fa-check-circle"></i>
                            تمت المراجعة
                        </div>
                    <?php else: ?>
                        <div class="status-badge status-pending">
                            <i class="fas fa-clock"></i>
                            قيد الانتظار
                        </div>
                    <?php endif; ?>
                </td>
                <td>
                    <div class="dropdown">
                        <?php if (!isset($row['is_seen']) || $row['is_seen'] != 1): ?>
                            <a href="?mark_seen=<?= $row['id'] ?>" class="action-btn">
                                <i class="fas fa-check"></i> تمت المراجعة
                            </a>
                        <?php else: ?>
                            <button class="action-btn btn-disabled">
                                <i class="fas fa-check-circle"></i> تم
                            </button>
                        <?php endif; ?>
                        
                        <button class="more-btn">
                            <i class="fas fa-ellipsis-v"></i>
                        </button>
                        
                        <div class="dropdown-content">
                            <a href="ticket_details.php?id=<?= $row['id'] ?>" class="dropdown-item">
                                <i class="fas fa-eye"></i> عرض التفاصيل
                            </a>
                            <a href="mailto:customer@example.com" class="dropdown-item">
                                <i class="fas fa-envelope"></i> التواصل مع العميل
                            </a>
                            <a href="tel:<?= htmlspecialchars($row['phone']) ?>" class="dropdown-item">
                                <i class="fas fa-phone"></i> اتصال بالعميل
                            </a>
                            <div class="dropdown-divider"></div>
                            <a href="update_status.php?id=<?= $row['id'] ?>" class="dropdown-item">
                                <i class="fas fa-edit"></i> تحديث الحالة
                            </a>
                            <a href="assign_ticket.php?id=<?= $row['id'] ?>" class="dropdown-item">
                                <i class="fas fa-user-plus"></i> تعيين للفني
                            </a>
                            <div class="dropdown-divider"></div>
                            <a href="?cancel_ticket=<?= $row['id'] ?>" class="dropdown-item delete" onclick="return confirm('هل أنت متأكد من إلغاء هذه التذكرة؟')">
                                <i class="fas fa-ban"></i> إلغاء التذكرة
                            </a>
                        </div>
                    </div>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<footer>
    جميع الحقوق محفوظة &copy; <?= date('Y') ?> - FlexAuto
</footer>

</body>
</html>
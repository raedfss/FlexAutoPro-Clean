<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: ../login.php");
    exit;
}

// تضمين ملف الاتصال بقاعدة البيانات
// تغيير المسار هنا - db.php موجود في نفس المجلد
require_once 'db.php';

// معلومات المستخدم
$username = $_SESSION['username'];

// استعلام لجلب تذاكر المستخدم
$query = "SELECT * FROM tickets WHERE username = ? ORDER BY created_at DESC";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// حساب إحصائيات التذاكر
$total_tickets = mysqli_num_rows($result);
$reviewed_tickets = 0;
$pending_tickets = 0;

// نسخة من نتائج الاستعلام لحساب الإحصائيات
$temp_result = mysqli_query($conn, "SELECT * FROM tickets WHERE username = '$username'");
while ($row = mysqli_fetch_assoc($temp_result)) {
    if (isset($row['is_seen']) && $row['is_seen'] == 1) {
        $reviewed_tickets++;
    } else {
        $pending_tickets++;
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تذاكري السابقة | FlexAuto</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Bootstrap RTL -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.rtl.min.css">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Segoe UI', Tahoma, sans-serif;
            color: white;
            background-color: #1a1f2e;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            line-height: 1.6;
        }

        /* خلفية SVG متحركة */
        .svg-background {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            overflow: hidden;
            opacity: 0.5;
        }

        .svg-object {
            width: 100%;
            height: 100%;
            pointer-events: none;
        }

        header {
            background-color: rgba(0, 0, 0, 0.85);
            padding: 18px 20px;
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            color: #00ffff;
            letter-spacing: 1px;
            border-bottom: 1px solid rgba(0, 255, 255, 0.3);
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.4);
        }

        .container {
            flex: 1;
            padding: 30px 20px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .page-header {
            padding-bottom: 20px;
            margin-bottom: 30px;
            border-bottom: 1px solid rgba(66, 135, 245, 0.2);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }

        .page-title {
            font-size: 28px;
            color: #00ffff;
            margin-bottom: 5px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .page-subtitle {
            font-size: 16px;
            color: #a0d0ff;
            opacity: 0.8;
        }

        .btn {
            padding: 12px 25px;
            border-radius: 50px;
            font-weight: bold;
            text-decoration: none;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .btn-primary {
            background: linear-gradient(135deg, #1e90ff, #4287f5);
            color: white;
            box-shadow: 0 4px 15px rgba(30, 144, 255, 0.3);
            border: none;
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(30, 144, 255, 0.4);
        }

        .btn-secondary {
            background: rgba(30, 35, 50, 0.8);
            color: #00ffff;
            border: 1px solid rgba(0, 255, 255, 0.3);
        }

        .btn-secondary:hover {
            background: rgba(30, 35, 50, 1);
            transform: translateY(-3px);
        }

        .btn-warning {
            background: linear-gradient(135deg, #ffa500, #ff8c00);
            color: white;
            border: none;
            box-shadow: 0 4px 15px rgba(255, 165, 0, 0.3);
        }

        .btn-warning:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(255, 165, 0, 0.4);
        }

        .btn-sm {
            padding: 8px 15px;
            font-size: 14px;
        }

        .stats-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: rgba(0, 0, 0, 0.6);
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
            display: flex;
            align-items: center;
            gap: 15px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(66, 135, 245, 0.2);
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 3px;
            background: linear-gradient(90deg, #00ffff, #4287f5, #00ffff);
            animation: border-glow 3s infinite;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            background: rgba(0, 0, 0, 0.3);
            flex-shrink: 0;
        }

        .total-icon {
            color: #4287f5;
        }

        .reviewed-icon {
            color: #00c853;
        }

        .pending-icon {
            color: #ffc107;
        }

        .stat-content h3 {
            font-size: 28px;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .stat-content p {
            font-size: 16px;
            color: #a0d0ff;
            margin: 0;
        }

        .search-container {
            background: rgba(0, 0, 0, 0.3);
            border-radius: 50px;
            padding: 6px 20px;
            display: flex;
            align-items: center;
            margin-bottom: 30px;
            border: 1px solid rgba(66, 135, 245, 0.2);
        }

        .search-icon {
            color: #4287f5;
            font-size: 18px;
            margin-left: 10px;
        }

        .search-input {
            background: transparent;
            border: none;
            padding: 10px;
            color: white;
            flex-grow: 1;
            font-size: 16px;
        }

        .search-input:focus {
            outline: none;
        }

        .search-input::placeholder {
            color: #a0d0ff;
            opacity: 0.6;
        }

        .ticket-card {
            background: rgba(0, 0, 0, 0.6);
            border-radius: 15px;
            margin-bottom: 25px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            transition: all 0.3s ease;
            animation: fade-in 0.5s ease-out;
            position: relative;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(66, 135, 245, 0.2);
        }

        .ticket-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
        }

        .ticket-header {
            background: rgba(0, 0, 0, 0.4);
            padding: 18px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid rgba(66, 135, 245, 0.2);
        }

        .ticket-id {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: bold;
            font-size: 18px;
        }

        .id-number {
            color: #00ffff;
        }

        .ticket-status {
            padding: 8px 15px;
            border-radius: 50px;
            font-size: 14px;
            font-weight: bold;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .status-reviewed {
            background-color: rgba(0, 200, 83, 0.2);
            color: #00c853;
            border: 1px solid rgba(0, 200, 83, 0.3);
        }

        .status-pending {
            background-color: rgba(255, 193, 7, 0.2);
            color: #ffc107;
            border: 1px solid rgba(255, 193, 7, 0.3);
        }

        .ticket-body {
            padding: 20px;
        }

        .info-group {
            margin-bottom: 20px;
        }

        .info-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
            margin-bottom: 15px;
        }

        .info-item {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .info-label {
            color: #a0d0ff;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .info-value {
            font-size: 16px;
            font-weight: 500;
        }

        .chassis-number {
            font-family: monospace;
            letter-spacing: 1px;
            background: rgba(0, 0, 0, 0.2);
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 14px;
            display: inline-block;
        }

        .ticket-comments {
            background: rgba(0, 0, 0, 0.2);
            padding: 15px;
            border-radius: 10px;
            margin-top: 15px;
            border-right: 3px solid #4287f5;
        }

        .comments-title {
            display: flex;
            align-items: center;
            gap: 5px;
            color: #a0d0ff;
            margin-bottom: 10px;
            font-size: 14px;
        }

        .comment-text {
            margin: 0;
        }

        .ticket-footer {
            padding: 15px 20px;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            background: rgba(0, 0, 0, 0.2);
            border-top: 1px solid rgba(66, 135, 245, 0.2);
        }

        .empty-state {
            background: rgba(0, 0, 0, 0.6);
            border-radius: 15px;
            padding: 40px 20px;
            text-align: center;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(66, 135, 245, 0.2);
            animation: fade-in 0.8s ease-out;
        }

        .empty-icon {
            font-size: 60px;
            margin-bottom: 20px;
            color: #4287f5;
            opacity: 0.5;
        }

        .empty-title {
            font-size: 24px;
            margin-bottom: 10px;
            color: #00ffff;
        }

        .empty-message {
            color: #a0d0ff;
            margin-bottom: 30px;
        }

        .btn-lg {
            padding: 15px 30px;
            font-size: 18px;
        }

        footer {
            background-color: rgba(0, 0, 0, 0.9);
            color: #eee;
            text-align: center;
            padding: 20px;
            width: 100%;
        }

        .footer-highlight {
            font-size: 18px;
            font-weight: bold;
            color: #00ffff;
            margin-bottom: 10px;
        }

        @keyframes border-glow {
            0% { opacity: 0.6; }
            50% { opacity: 1; }
            100% { opacity: 0.6; }
        }

        @keyframes fade-in {
            from { 
                opacity: 0;
                transform: translateY(10px);
            }
            to { 
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 768px) {
            .page-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }

            .btn-mobile-full {
                width: 100%;
            }

            .ticket-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }

            .ticket-footer {
                justify-content: center;
            }

            .btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>

<div class="svg-background">
    <embed type="image/svg+xml" src="../admin/admin_background.svg" class="svg-object">
</div>

<header>FlexAuto - نظام خدمات برمجة السيارات</header>

<div class="container">
    <!-- عنوان الصفحة -->
    <div class="page-header">
        <div>
            <h1 class="page-title">
                <i class="fas fa-ticket-alt"></i>
                تذاكري السابقة
            </h1>
            <p class="page-subtitle">عرض وإدارة جميع تذاكر خدمات برمجة السيارات الخاصة بك</p>
        </div>
        <a href="../new_ticket.php" class="btn btn-primary btn-mobile-full">
            <i class="fas fa-plus-circle"></i>
            إنشاء تذكرة جديدة
        </a>
    </div>

    <!-- إحصائيات التذاكر -->
    <div class="stats-cards">
        <div class="stat-card">
            <div class="stat-icon total-icon">
                <i class="fas fa-ticket-alt"></i>
            </div>
            <div class="stat-content">
                <h3><?php echo $total_tickets; ?></h3>
                <p>إجمالي التذاكر</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon reviewed-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-content">
                <h3><?php echo $reviewed_tickets; ?></h3>
                <p>تمت المراجعة</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon pending-icon">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-content">
                <h3><?php echo $pending_tickets; ?></h3>
                <p>قيد الانتظار</p>
            </div>
        </div>
    </div>

    <!-- خانة البحث -->
    <div class="search-container">
        <i class="fas fa-search search-icon"></i>
        <input type="text" id="ticketSearch" class="search-input" placeholder="البحث في التذاكر...">
    </div>

    <!-- قائمة التذاكر -->
    <?php if (mysqli_num_rows($result) > 0): ?>
        <div class="ticket-list" id="ticketsList">
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <div class="ticket-card">
                    <!-- رأس التذكرة -->
                    <div class="ticket-header">
                        <div class="ticket-id">
                            <i class="fas fa-hashtag"></i>
                            <span class="id-number">FLEX-<?php echo $row['id']; ?></span>
                        </div>
                        <?php if (isset($row['is_seen']) && $row['is_seen'] == 1): ?>
                            <div class="ticket-status status-reviewed">
                                <i class="fas fa-check-circle"></i>
                                تمت المراجعة
                            </div>
                        <?php else: ?>
                            <div class="ticket-status status-pending">
                                <i class="fas fa-clock"></i>
                                قيد المراجعة
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- محتوى التذكرة -->
                    <div class="ticket-body">
                        <div class="info-group">
                            <div class="info-row">
                                <div class="info-item">
                                    <div class="info-label">
                                        <i class="fas fa-cog"></i>
                                        نوع الخدمة
                                    </div>
                                    <div class="info-value"><?php echo htmlspecialchars($row['service_type']); ?></div>
                                </div>
                                <div class="info-item">
                                    <div class="info-label">
                                        <i class="fas fa-car"></i>
                                        نوع السيارة
                                    </div>
                                    <div class="info-value"><?php echo htmlspecialchars($row['car_type']); ?></div>
                                </div>
                            </div>
                            
                            <div class="info-row">
                                <div class="info-item">
                                    <div class="info-label">
                                        <i class="fas fa-fingerprint"></i>
                                        رقم الشاسيه
                                    </div>
                                    <div class="info-value">
                                        <span class="chassis-number"><?php echo htmlspecialchars($row['chassis']); ?></span>
                                    </div>
                                </div>
                                <div class="info-item">
                                    <div class="info-label">
                                        <i class="fas fa-calendar-alt"></i>
                                        تاريخ الإنشاء
                                    </div>
                                    <div class="info-value"><?php echo date('Y/m/d', strtotime($row['created_at'])); ?></div>
                                </div>
                            </div>
                        </div>
                        
                        <?php if (!empty($row['comments'])): ?>
                        <div class="ticket-comments">
                            <div class="comments-title">
                                <i class="fas fa-comment-alt"></i>
                                ملاحظات
                            </div>
                            <p class="comment-text"><?php echo htmlspecialchars($row['comments']); ?></p>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- زر الإجراءات -->
                    <div class="ticket-footer">
                        <a href="../ticket_details.php?id=<?php echo $row['id']; ?>" class="btn btn-primary btn-sm">
                            <i class="fas fa-eye"></i> عرض التفاصيل
                        </a>
                        <button type="button" class="btn btn-secondary btn-sm" onclick="printTicketInfo(<?php echo $row['id']; ?>)">
                            <i class="fas fa-print"></i> طباعة
                        </button>
                        <?php if (!isset($row['is_seen']) || $row['is_seen'] == 0): ?>
                            <a href="../edit_ticket.php?id=<?php echo $row['id']; ?>" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit"></i> تعديل
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <!-- رسالة عدم وجود تذاكر -->
        <div class="empty-state">
            <div class="empty-icon">
                <i class="fas fa-ticket-alt"></i>
            </div>
            <h2 class="empty-title">لا توجد تذاكر محفوظة</h2>
            <p class="empty-message">لم تقم بإنشاء أي تذاكر خدمة حتى الآن</p>
            <a href="../new_ticket.php" class="btn btn-primary btn-lg">
                <i class="fas fa-plus-circle"></i> إنشاء تذكرة جديدة
            </a>
        </div>
    <?php endif; ?>
</div>

<footer>
    <div class="footer-highlight">ذكاءٌ في الخدمة، سرعةٌ في الاستجابة، جودةٌ بلا حدود.</div>
    <div>Smart service, fast response, unlimited quality.</div>
    <div style="margin-top: 8px;">📧 raedfss@hotmail.com | ☎️ +962796519007</div>
    <div style="margin-top: 5px;">&copy; <?= date('Y') ?> FlexAuto. جميع الحقوق محفوظة.</div>
</footer>

<script>
// وظيفة البحث في التذاكر
document.getElementById('ticketSearch').addEventListener('keyup', function() {
    const searchVal = this.value.toLowerCase();
    const ticketCards = document.querySelectorAll('.ticket-card');
    
    ticketCards.forEach(card => {
        const text = card.textContent.toLowerCase();
        if (text.includes(searchVal)) {
            card.style.display = '';
        } else {
            card.style.display = 'none';
        }
    });
});

// وظيفة طباعة معلومات التذكرة
function printTicketInfo(ticketId) {
    // إنشاء نافذة طباعة جديدة
    const printWindow = window.open('', '_blank', 'width=800,height=600');
    
    // البحث عن التذكرة بالمعرف
    const allTickets = document.querySelectorAll('.ticket-card');
    let foundTicket = null;
    
    for (let i = 0; i < allTickets.length; i++) {
        const ticketIdElement = allTickets[i].querySelector('.id-number');
        if (ticketIdElement && ticketIdElement.textContent.includes(ticketId)) {
            foundTicket = allTickets[i];
            break;
        }
    }
    
    if (!foundTicket) {
        alert('لم يتم العثور على التذكرة المحددة.');
        return;
    }
    
    // استخراج معلومات التذكرة
    const ticketIdText = foundTicket.querySelector('.id-number').textContent;
    const serviceType = foundTicket.querySelectorAll('.info-value')[0].textContent;
    const carType = foundTicket.querySelectorAll('.info-value')[1].textContent;
    const chassisNum = foundTicket.querySelector('.chassis-number').textContent;
    const createdDate = foundTicket.querySelectorAll('.info-value')[3].textContent;
    
    // إنشاء محتوى النافذة للطباعة
    const printContent = `
        <!DOCTYPE html>
        <html dir="rtl">
        <head>
            <title>طباعة تذكرة ${ticketIdText}</title>
            <meta charset="UTF-8">
            <style>
                body {
                    font-family: 'Segoe UI', Tahoma, sans-serif;
                    padding: 20px;
                    direction: rtl;
                }
                .print-header {
                    text-align: center;
                    margin-bottom: 30px;
                    border-bottom: 2px solid #1e90ff;
                    padding-bottom: 20px;
                }
                .ticket-info {
                    margin-bottom: 30px;
                }
                .ticket-info h2 {
                    margin-bottom: 20px;
                    color: #1e90ff;
                }
                .info-row {
                    display: flex;
                    margin-bottom: 15px;
                    border-bottom: 1px solid #eee;
                    padding-bottom: 10px;
                }
                .info-label {
                    width: 150px;
                    font-weight: bold;
                }
                .info-value {
                    flex: 1;
                }
                .print-footer {
                    margin-top: 50px;
                    text-align: center;
                    border-top: 2px solid #1e90ff;
                    padding-top: 20px;
                    font-size: 12px;
                }
                @media print {
                    body {
                        margin: 0;
                        padding: 20px;
                    }
                    .no-print {
                        display: none;
                    }
                }
            </style>
        </head>
        <body>
            <div class="print-header">
                <h1>نظام FlexAuto لخدمات برمجة السيارات</h1>
                <p>تذكرة خدمة</p>
            </div>
            
            <div class="ticket-info">
                <h2>${ticketIdText}</h2>
                
                <div class="info-row">
                    <div class="info-label">نوع الخدمة:</div>
                    <div class="info-value">${serviceType}</div>
                </div>
                
                <div class="info-row">
                    <div class="info-label">نوع السيارة:</div>
                    <div class="info-value">${carType}</div>
                </div>
                
                <div class="info-row">
                    <div class="info-label">رقم الشاسيه:</div>
                    <div class="info-value">${chassisNum}</div>
                </div>
                
                <div class="info-row">
                    <div class="info-label">تاريخ الإنشاء:</div>
                    <div class="info-value">${createdDate}</div>
                </div>
            </div>
            
            <div class="print-footer">
                <p>FlexAuto &copy; ${new Date().getFullYear()} - نظام خدمات برمجة السيارات</p>
                <p>للتواصل: 0797979797 | info@flexauto.com</p>
            </div>
            
            <div class="no-print" style="text-align: center; margin-top: 30px;">
                <button onclick="window.print();" style="padding: 12px 25px; background: linear-gradient(135deg, #1e90ff, #4287f5); color: white; border: none; border-radius: 50px; cursor: pointer; font-weight: bold;">
                    طباعة التذكرة
                </button>
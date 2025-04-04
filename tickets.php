<?php
// تعيين عنوان الصفحة
$pageTitle = "إدارة التذاكر | FlexAuto";

// تضمين ملف header.php (الذي يحتوي على كود session_start)
require_once 'includes/header.php';

// التأكد من أن المستخدم مسجل دخول وأنه مدير
if (!isset($_SESSION['username']) || $_SESSION['user_type'] !== 'admin') {
    // إذا لم يكن مسجل دخول كمدير، حوله إلى الصفحة الرئيسية
    header("Location: index.php");
    exit;
}

// تضمين ملف الاتصال بقاعدة البيانات
require_once 'includes/db.php';

// تحديث حالة التذكرة عند النقر على "تمت المراجعة"
if (isset($_GET['mark_seen'])) {
    $id = intval($_GET['mark_seen']);
    $update_query = "UPDATE tickets SET is_seen = 1 WHERE id = ?";
    $stmt = mysqli_prepare($conn, $update_query);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    // إعادة توجيه إلى نفس الصفحة لتجنب إعادة الإرسال
    header("Location: tickets.php");
    exit;
}

// استعلام لجلب كل التذاكر
$query = "SELECT * FROM tickets ORDER BY created_at DESC";
$result = mysqli_query($conn, $query);

// حساب إحصائيات التذاكر
$total_tickets = mysqli_num_rows($result);
$reviewed_tickets = 0;
$pending_tickets = 0;

// نسخة من نتائج الاستعلام لحساب الإحصائيات
$temp_result = mysqli_query($conn, "SELECT * FROM tickets");
while ($row = mysqli_fetch_assoc($temp_result)) {
    if (isset($row['is_seen']) && $row['is_seen'] == 1) {
        $reviewed_tickets++;
    } else {
        $pending_tickets++;
    }
}
?>

<!-- صفحة إدارة التذاكر -->
<div class="container py-5">
    <!-- العنوان والوصف -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="mb-2 fw-bold text-primary">
                <i class="fas fa-clipboard-list me-2"></i>إدارة التذاكر
            </h1>
            <p class="text-muted">عرض وإدارة جميع تذاكر برمجة السيارات المرسلة من العملاء</p>
        </div>
        <div class="col-md-4 text-md-end mt-3 mt-md-0">
            <a href="export_tickets.php" class="btn btn-secondary">
                <i class="fas fa-file-export me-1"></i> تصدير البيانات
            </a>
        </div>
    </div>

    <!-- إحصائيات التذاكر -->
    <div class="row mb-4 g-3">
        <div class="col-md-4">
            <div class="card bg-primary bg-gradient text-white h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="rounded-circle bg-white bg-opacity-25 p-3 me-3">
                        <i class="fas fa-ticket-alt fa-2x text-white"></i>
                    </div>
                    <div>
                        <h3 class="fw-bold mb-0"><?php echo $total_tickets; ?></h3>
                        <p class="mb-0">إجمالي التذاكر</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-success bg-gradient text-white h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="rounded-circle bg-white bg-opacity-25 p-3 me-3">
                        <i class="fas fa-check-circle fa-2x text-white"></i>
                    </div>
                    <div>
                        <h3 class="fw-bold mb-0"><?php echo $reviewed_tickets; ?></h3>
                        <p class="mb-0">تمت المراجعة</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-warning bg-gradient text-dark h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="rounded-circle bg-white bg-opacity-25 p-3 me-3">
                        <i class="fas fa-clock fa-2x text-dark"></i>
                    </div>
                    <div>
                        <h3 class="fw-bold mb-0"><?php echo $pending_tickets; ?></h3>
                        <p class="mb-0">قيد الانتظار</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- خانة البحث والتصفية -->
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="input-group">
                <span class="input-group-text bg-white">
                    <i class="fas fa-search text-muted"></i>
                </span>
                <input type="text" id="ticketSearch" class="form-control" placeholder="البحث في التذاكر...">
            </div>
        </div>
        <div class="col-md-4">
            <select id="statusFilter" class="form-select">
                <option value="all">جميع التذاكر</option>
                <option value="reviewed">تمت المراجعة</option>
                <option value="pending">قيد الانتظار</option>
            </select>
        </div>
    </div>

    <!-- جدول التذاكر -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="ticketsTable">
                    <thead class="table-light">
                        <tr>
                            <th class="p-3">رقم</th>
                            <th class="p-3">العميل</th>
                            <th class="p-3">الهاتف</th>
                            <th class="p-3">السيارة</th>
                            <th class="p-3">الشاسيه</th>
                            <th class="p-3">الخدمة</th>
                            <th class="p-3">الحالة</th>
                            <th class="p-3">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($result) > 0): ?>
                            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                <tr class="ticket-row <?php echo (isset($row['is_seen']) && $row['is_seen'] == 1) ? 'reviewed' : 'pending'; ?>">
                                    <td class="p-3">
                                        <span class="fw-bold text-primary">FLEX-<?php echo $row['id']; ?></span>
                                    </td>
                                    <td class="p-3"><?php echo htmlspecialchars($row['username']); ?></td>
                                    <td class="p-3"><?php echo htmlspecialchars($row['phone']); ?></td>
                                    <td class="p-3"><?php echo htmlspecialchars($row['car_type']); ?></td>
                                    <td class="p-3 font-monospace"><?php echo htmlspecialchars($row['chassis']); ?></td>
                                    <td class="p-3">
                                        <span class="badge bg-primary rounded-pill px-3 py-2">
                                            <?php echo htmlspecialchars($row['service_type']); ?>
                                        </span>
                                    </td>
                                    <td class="p-3">
                                        <?php if (isset($row['is_seen']) && $row['is_seen'] == 1): ?>
                                            <span class="badge bg-success rounded-pill px-3 py-2">
                                                <i class="fas fa-check-circle me-1"></i> تمت المراجعة
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-warning text-dark rounded-pill px-3 py-2">
                                                <i class="fas fa-clock me-1"></i> قيد المراجعة
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="p-3">
                                        <div class="dropdown">
                                            <?php if (!isset($row['is_seen']) || $row['is_seen'] != 1): ?>
                                                <a href="?mark_seen=<?php echo $row['id']; ?>" class="btn btn-sm btn-success me-1">
                                                    <i class="fas fa-check me-1"></i> تمت المراجعة
                                                </a>
                                            <?php else: ?>
                                                <button class="btn btn-sm btn-secondary me-1" disabled>
                                                    <i class="fas fa-check-circle me-1"></i> تم
                                                </button>
                                            <?php endif; ?>
                                            
                                            <button class="btn btn-sm btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                المزيد
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li>
                                                    <a class="dropdown-item" href="ticket_details.php?id=<?php echo $row['id']; ?>">
                                                        <i class="fas fa-eye me-1"></i> عرض التفاصيل
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" href="mailto:<?php echo $row['email']; ?>">
                                                        <i class="fas fa-envelope me-1"></i> التواصل مع العميل
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" href="tel:<?php echo $row['phone']; ?>">
                                                        <i class="fas fa-phone me-1"></i> اتصال بالعميل
                                                    </a>
                                                </li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <a class="dropdown-item" href="assign_technician.php?id=<?php echo $row['id']; ?>">
                                                        <i class="fas fa-user-plus me-1"></i> تعيين للفني
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item text-danger" href="delete_ticket.php?id=<?php echo $row['id']; ?>" onclick="return confirm('هل أنت متأكد من حذف هذه التذكرة؟')">
                                                        <i class="fas fa-trash-alt me-1"></i> حذف التذكرة
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center p-5">
                                    <i class="fas fa-ticket-alt fa-3x text-muted mb-3"></i>
                                    <h4>لا توجد تذاكر متاحة</h4>
                                    <p class="text-muted">لم يتم إنشاء أي تذاكر بعد</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
// وظيفة البحث في التذاكر
document.getElementById('ticketSearch').addEventListener('keyup', function() {
    filterTickets();
});

// وظيفة تصفية التذاكر حسب الحالة
document.getElementById('statusFilter').addEventListener('change', function() {
    filterTickets();
});

// وظيفة تصفية التذاكر
function filterTickets() {
    const searchVal = document.getElementById('ticketSearch').value.toLowerCase();
    const statusFilter = document.getElementById('statusFilter').value;
    const rows = document.querySelectorAll('#ticketsTable tbody tr');
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        const isReviewed = row.classList.contains('reviewed');
        
        let showRow = true;
        
        // تطبيق تصفية البحث
        if (!text.includes(searchVal)) {
            showRow = false;
        }
        
        // تطبيق تصفية الحالة
        if (statusFilter === 'reviewed' && !isReviewed) {
            showRow = false;
        } else if (statusFilter === 'pending' && isReviewed) {
            showRow = false;
        }
        
        // إظهار أو إخفاء الصف
        row.style.display = showRow ? '' : 'none';
    });
}
</script>

<?php require_once 'includes/footer.php'; ?>
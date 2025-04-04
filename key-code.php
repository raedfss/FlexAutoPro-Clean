<?php
session_start();
if (!isset($_SESSION['email']) || $_SESSION['user_type'] !== 'user') {
    header("Location: login.php");
    exit;
}

require_once 'includes/db.php';

$username = $_SESSION['username'];
$message = "";
$showForm = true;
$requestId = null;
$requestedVin = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_request'])) {
    $car_type = $_POST['car_type'] ?? '';
    $vin = trim($_POST['vin'] ?? '');
    $request_type = $_POST['toyota_type'] ?? 'key_code';
    $seed_number = trim($_POST['seed_number'] ?? '');
    $data1 = trim($_POST['data1'] ?? '');
    $data2 = trim($_POST['data2'] ?? '');
    $data3 = trim($_POST['data3'] ?? '');

    try {
        $stmt = $pdo->prepare("
            INSERT INTO key_requests (username, car_type, request_type, vin, seed_number, data1, data2, data3, status, created_at)
            VALUES (:username, :car_type, :request_type, :vin, :seed_number, :data1, :data2, :data3, 0, NOW())
        ");
        $stmt->execute([
            ':username' => $username,
            ':car_type' => $car_type,
            ':request_type' => $request_type,
            ':vin' => $vin,
            ':seed_number' => $seed_number,
            ':data1' => $data1,
            ':data2' => $data2,
            ':data3' => $data3
        ]);
        $lastInsertId = $pdo->lastInsertId();
        $requestId = $lastInsertId;
        $requestedVin = $vin;
        $showForm = false;
    } catch (PDOException $e) {
        $message = "❌ خطأ في إدخال البيانات: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>طلب كود المفتاح | FlexAuto</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background-color: #1a1f2e;
            color: white;
            overflow-x: hidden;
        }
        .svg-background {
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            z-index: -1;
            overflow: hidden;
            opacity: 0.5;
        }
        .svg-object {
            width: 100%;
            height: 100%;
            pointer-events: none;
        }
        .container {
            max-width: 750px;
            margin: 60px auto;
            background: rgba(0,0,0,0.6);
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 0 30px rgba(0,255,255,0.1);
            backdrop-filter: blur(8px);
            animation: fadeIn 0.5s ease-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        h1 {
            text-align: center;
            color: #00ffff;
            margin-bottom: 30px;
            text-shadow: 0 0 10px rgba(0,255,255,0.4);
            position: relative;
            padding-bottom: 15px;
        }
        h1:after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 3px;
            background: linear-gradient(90deg, rgba(0,255,255,0), rgba(0,255,255,1), rgba(0,255,255,0));
        }
        label {
            color: #a0d0ff;
            display: block;
            margin: 15px 0 5px;
            font-weight: bold;
        }
        input[type="text"] {
            width: 100%;
            padding: 12px;
            border-radius: 8px;
            border: 1px solid rgba(0, 255, 255, 0.3);
            background: rgba(255,255,255,0.05);
            color: white;
            font-size: 16px;
            transition: all 0.3s;
        }
        input:focus {
            outline: none;
            border-color: #00ffff;
            box-shadow: 0 0 15px rgba(0,255,255,0.3);
            background: rgba(255,255,255,0.1);
        }
        .btn-group {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin: 10px 0 25px;
        }
        .btn-group button {
            flex: 1;
            min-width: 120px;
            padding: 12px 20px;
            background: rgba(0,191,255,0.2);
            color: white;
            border: 1px solid rgba(0,191,255,0.3);
            border-radius: 8px;
            cursor: pointer;
            font-size: 15px;
            transition: all 0.3s;
        }
        .btn-group button.active,
        .btn-group button:hover {
            background: rgba(0,191,255,0.5);
            box-shadow: 0 0 15px rgba(0,255,255,0.3);
            transform: translateY(-2px);
        }
        .form-section {
            display: none;
            animation: slideDown 0.4s ease-out;
            margin-bottom: 20px;
        }
        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .submit-btn {
            margin-top: 30px;
            width: 100%;
            background: linear-gradient(135deg, #1e90ff, #00bfff);
            border: none;
            padding: 16px;
            font-size: 18px;
            border-radius: 10px;
            font-weight: bold;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(0,0,0,0.3);
            display: none;
            color: white;
            transition: all 0.3s;
        }
        .submit-btn:hover {
            background: linear-gradient(135deg, #00bfff, #1e90ff);
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.4);
        }
        .success-container {
            text-align: center;
            padding: 30px;
            border-radius: 15px;
            background: linear-gradient(135deg, rgba(0,100,100,0.3), rgba(0,50,100,0.3));
            border: 1px solid rgba(0,200,200,0.3);
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            animation: scaleIn 0.5s ease-out;
        }
        @keyframes scaleIn {
            from { opacity: 0; transform: scale(0.9); }
            to { opacity: 1; transform: scale(1); }
        }
        .success-icon {
            font-size: 60px;
            color: #2ecc71;
            margin-bottom: 20px;
            text-shadow: 0 0 15px rgba(46,204,113,0.5);
        }
        .success-title {
            font-size: 26px;
            color: #2ecc71;
            margin-bottom: 30px;
        }
        .request-details {
            background: rgba(0,0,0,0.4);
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0 30px;
            display: inline-block;
            min-width: 80%;
        }
        .request-detail {
            margin: 15px 0;
            font-size: 18px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .detail-label {
            color: #a0d0ff;
            font-weight: bold;
        }
        .detail-value {
            color: #00ffff;
            font-weight: bold;
            font-size: 20px;
            padding: 10px 20px;
            background: rgba(0,0,0,0.3);
            border-radius: 8px;
            box-shadow: inset 0 0 10px rgba(0,0,0,0.2);
            min-width: 150px;
            text-align: center;
        }
        .success-message {
            line-height: 1.8;
            font-size: 18px;
            margin: 30px 0;
        }
        .back-link {
            display: inline-block;
            margin-top: 20px;
            color: #00ffff;
            text-decoration: none;
            font-weight: bold;
            font-size: 18px;
            padding: 12px 30px;
            border: 2px solid rgba(0,255,255,0.3);
            border-radius: 8px;
            transition: all 0.3s;
        }
        .back-link:hover {
            background: rgba(0,255,255,0.2);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        .alert-error {
            background-color: rgba(231, 76, 60, 0.2);
            color: #e74c3c;
            border: 1px solid rgba(231, 76, 60, 0.4);
            border-radius: 8px;
            padding: 15px 20px;
            margin-bottom: 20px;
            text-align: center;
            font-size: 16px;
            line-height: 1.8;
        }
        @media (max-width: 768px) {
            .container { margin: 20px; padding: 20px; }
            .btn-group { flex-direction: column; }
            .request-detail { flex-direction: column; align-items: flex-start; }
            .detail-value { margin-top: 8px; width: 100%; }
        }
    </style>
</head>
<body>

<div class="svg-background">
    <embed type="image/svg+xml" src="admin/admin_background.svg" class="svg-object">
</div>

<div class="container">
    <h1>طلب كود المفتاح</h1>

    <?php if (!empty($message)): ?>
        <div class="alert-error">
            <?= $message ?>
        </div>
    <?php endif; ?>
    
    <?php if (!$showForm && $requestId): ?>
        <div class="success-container">
            <div class="success-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="success-title">تم استلام طلبكم بنجاح!</div>
            
            <div class="request-details">
                <div class="request-detail">
                    <span class="detail-label"><i class="fas fa-hashtag"></i> رقم الطلب:</span>
                    <span class="detail-value"><?= $requestId ?></span>
                </div>
                <div class="request-detail">
                    <span class="detail-label"><i class="fas fa-car"></i> رقم الشاسيه:</span>
                    <span class="detail-value"><?= $requestedVin ?></span>
                </div>
            </div>
            
            <div class="success-message">
                <p><i class="fas fa-info-circle"></i> طلبكم قيد المراجعة والتنفيذ.</p>
                <p><i class="fas fa-clock"></i> سيتم التواصل معكم قريبًا عبر بيانات الاتصال التي تم إدخالها.</p>
                <p><i class="fas fa-heart"></i> شكرًا لتواصلكم معنا في FlexAuto.</p>
            </div>
            
            <a href="home.php" class="back-link">
                <i class="fas fa-home"></i> العودة إلى الصفحة الرئيسية
            </a>
        </div>
    <?php elseif($showForm): ?>
    <form method="POST" id="keyForm">
        <label>اختر نوع السيارة:</label>
        <div class="btn-group">
            <button type="button" data-car="KIA"><i class="fas fa-car"></i> KIA</button>
            <button type="button" data-car="Hyundai"><i class="fas fa-car"></i> Hyundai</button>
            <button type="button" data-car="Toyota"><i class="fas fa-car"></i> Toyota</button>
            <button type="button" onclick="window.location='online-programming-ticket.php'"><i class="fas fa-plus-circle"></i> أخرى</button>
        </div>
        <input type="hidden" name="car_type" id="car_type">

        <div id="section-kia" class="form-section">
            <label><i class="fas fa-fingerprint"></i> رقم الشاسيه:</label>
            <input type="text" name="vin" id="vin_kia" maxlength="17" placeholder="أدخل رقم الشاسيه المكون من 17 رقم/حرف">
        </div>

        <div id="section-hyundai" class="form-section">
            <label><i class="fas fa-fingerprint"></i> رقم الشاسيه:</label>
            <input type="text" name="vin" id="vin_hyundai" maxlength="17" placeholder="أدخل رقم الشاسيه المكون من 17 رقم/حرف">
        </div>

        <div id="section-toyota" class="form-section">
            <label>اختر نوع الخدمة:</label>
            <div class="btn-group">
                <button type="button" data-toyota="seed_number"><i class="fas fa-seedling"></i> Seed Number</button>
                <button type="button" data-toyota="pass_code"><i class="fas fa-key"></i> Pass Code</button>
            </div>
            <input type="hidden" name="toyota_type" id="toyota_type">

            <div id="form-seed" class="form-section">
                <label><i class="fas fa-fingerprint"></i> رقم الشاسيه:</label>
                <input type="text" name="vin" id="vin_seed" maxlength="17" placeholder="أدخل رقم الشاسيه المكون من 17 رقم/حرف">
                <label><i class="fas fa-seedling"></i> Seed Number (92 خانة):</label>
                <input type="text" name="seed_number" id="seed_number" maxlength="92" placeholder="أدخل Seed Number المكون من 92 خانة">
            </div>

            <div id="form-pass" class="form-section">
                <label><i class="fas fa-fingerprint"></i> رقم الشاسيه:</label>
                <input type="text" name="vin" id="vin_pass" maxlength="17" placeholder="أدخل رقم الشاسيه المكون من 17 رقم/حرف">
                <label><i class="fas fa-database"></i> Data 1:</label>
                <input type="text" name="data1" id="data1" placeholder="أدخل البيانات المطلوبة">
                <label><i class="fas fa-database"></i> Data 2:</label>
                <input type="text" name="data2" id="data2" placeholder="أدخل البيانات المطلوبة">
                <label><i class="fas fa-database"></i> Data 3:</label>
                <input type="text" name="data3" id="data3" placeholder="أدخل البيانات المطلوبة">
            </div>
        </div>

        <button type="submit" name="submit_request" class="submit-btn" id="submitBtn">
            <i class="fas fa-paper-plane"></i> طلب الكود
        </button>
    </form>
    <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const carBtns = document.querySelectorAll('[data-car]');
    const toyotaBtns = document.querySelectorAll('[data-toyota]');
    const submitBtn = document.getElementById('submitBtn');
    const carInput = document.getElementById('car_type');
    const toyotaInput = document.getElementById('toyota_type');

    const sections = {
        KIA: document.getElementById('section-kia'),
        Hyundai: document.getElementById('section-hyundai'),
        Toyota: document.getElementById('section-toyota'),
        seed_number: document.getElementById('form-seed'),
        pass_code: document.getElementById('form-pass')
    };

    function hideAll() {
        Object.values(sections).forEach(s => {
            if (s) s.style.display = 'none';
        });
        submitBtn.style.display = 'none';
    }

    carBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            hideAll();
            carBtns.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');

            const car = btn.dataset.car;
            carInput.value = car;
            if (sections[car]) {
                sections[car].style.display = 'block';
            }

            validateForm();
        });
    });

    toyotaBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            toyotaBtns.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            const type = btn.dataset.toyota;
            toyotaInput.value = type;

            if (sections.seed_number) sections.seed_number.style.display = 'none';
            if (sections.pass_code) sections.pass_code.style.display = 'none';
            if (sections[type]) sections[type].style.display = 'block';

            validateForm();
        });
    });

    document.querySelectorAll('input').forEach(i => {
        i.addEventListener('input', validateForm);
    });

    function validateForm() {
        const car = carInput.value;
        if (car === 'KIA' && document.getElementById('vin_kia') && document.getElementById('vin_kia').value.length === 17) {
            submitBtn.style.display = 'block';
        } else if (car === 'Hyundai' && document.getElementById('vin_hyundai') && document.getElementById('vin_hyundai').value.length === 17) {
            submitBtn.style.display = 'block';
        } else if (car === 'Toyota') {
            const type = toyotaInput.value;
            if (type === 'seed_number') {
                const vin = document.getElementById('vin_seed') ? document.getElementById('vin_seed').value : '';
                const seed = document.getElementById('seed_number') ? document.getElementById('seed_number').value : '';
                if (vin.length === 17 && seed.length === 92) {
                    submitBtn.style.display = 'block';
                } else submitBtn.style.display = 'none';
            } else if (type === 'pass_code') {
                const vin = document.getElementById('vin_pass') ? document.getElementById('vin_pass').value : '';
                const d1 = document.getElementById('data1') ? document.getElementById('data1').value : '';
                const d2 = document.getElementById('data2') ? document.getElementById('data2').value : '';
                const d3 = document.getElementById('data3') ? document.getElementById('data3').value : '';
                if (vin.length === 17 && d1 && d2 && d3) {
                    submitBtn.style.display = 'block';
                } else submitBtn.style.display = 'none';
            }
        } else {
            submitBtn.style.display = 'none';
        }
    }
});
</script>

</body>
</html>
<?php
session_start();
require_once 'includes/db.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email    = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm  = $_POST['confirm'];

    // التحقق من الحقول
    if (empty($username) || empty($email) || empty($password) || empty($confirm)) {
        $errors[] = "جميع الحقول مطلوبة.";
    }

    if ($password !== $confirm) {
        $errors[] = "كلمتا المرور غير متطابقتين.";
    }

    // التحقق من وجود المستخدم أو البريد الإلكتروني مسبقًا
    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = :username OR email = :email");
        $stmt->execute(['username' => $username, 'email' => $email]);

        if ($stmt->fetch()) {
            $errors[] = "اسم المستخدم أو البريد الإلكتروني مسجل مسبقًا.";
        }
    }

    // إذا لا يوجد أخطاء، يتم إنشاء الحساب
    if (empty($errors)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (:username, :email, :password)");
        $success = $stmt->execute([
            'username' => $username,
            'email'    => $email,
            'password' => $hashedPassword
        ]);

        if ($success) {
            $_SESSION['success'] = "تم إنشاء الحساب بنجاح! يمكنك تسجيل الدخول الآن.";
            header("Location: login.php");
            exit();
        } else {
            $errors[] = "حدث خطأ أثناء حفظ البيانات.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <title>تسجيل مستخدم جديد - FlexAuto</title>
    <link rel="stylesheet" href="assets/style.css">
    <style>
        body {
            background-color: #eef1f5;
            font-family: Tahoma, sans-serif;
        }
        .register-container {
            width: 400px;
            margin: 80px auto;
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 0px 15px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            margin-bottom: 25px;
        }
        input[type="text"], input[type="email"], input[type="password"] {
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        input[type="submit"] {
            background: #007acc;
            color: white;
            border: none;
            padding: 12px;
            width: 100%;
            border-radius: 5px;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background: #005f99;
        }
        .error {
            color: red;
            margin-bottom: 15px;
        }
        .success {
            color: green;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <h2>تسجيل مستخدم جديد</h2>

        <?php if (!empty($errors)): ?>
            <div class="error">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?= $error ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="register.php" method="POST">
            <input type="text" name="username" placeholder="اسم المستخدم" required>
            <input type="email" name="email" placeholder="البريد الإلكتروني" required>
            <input type="password" name="password" placeholder="كلمة المرور" required>
            <input type="password" name="confirm" placeholder="تأكيد كلمة المرور" required>
            <input type="submit" value="تسجيل">
        </form>
        <p style="text-align:center; margin-top: 10px;">
            لديك حساب؟ <a href="login.php">سجّل الدخول</a>
        </p>
    </div>
</body>
</html>

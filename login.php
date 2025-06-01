<?php
session_start();
require_once 'includes/db_connect.php';
require_once 'includes/log_activity.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username_or_email = trim(strip_tags($_POST['username'] ?? ''));
    $password = $_POST['password'] ?? '';
    $error = '';

    if (empty($username_or_email) || empty($password)) {
        $error = "يرجى إدخال جميع الحقول.";
        debugLog("حقول ناقصة: username=$username_or_email");
    } else {
        $stmt = $db->prepare(
            "SELECT u.*, r.role_name 
             FROM users u 
             LEFT JOIN roles r ON u.role_id = r.id 
             WHERE TRIM(LOWER(u.email)) = TRIM(LOWER(:login))
                OR TRIM(u.username) = TRIM(:login)
                OR TRIM(u.phone) = TRIM(:login)
             LIMIT 1"
        );
        $stmt->execute(['login' => $username_or_email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            // تجديد معرف الجلسة للحماية
            session_regenerate_id(true);

            $userRole = strtolower($user['role_name'] ?? $user['role'] ?? '');

            // دعم تغيير اسماء الادوار
            $tabRoles = [
                'lawyer'       => 'lawyer',
                'manager'      => 'manager',
                'admin'        => 'admin',
                'superadmin'   => 'superadmin',
                'client'       => 'client',
            ];
            $dashboardTab = $tabRoles[$userRole] ?? 'client';

            $_SESSION['user_id']      = $user['id'];
            $_SESSION['user_name']    = $user['name'];
            $_SESSION['user_photo']   = $user['profile_photo'] ?? 'images/default-profile.png';
            $_SESSION['user_role']    = $dashboardTab;
            $_SESSION['user_role_id'] = $user['role_id'] ?? null;

            logActivity(
                $db,
                'تسجيل دخول',
                'تم تسجيل دخول المستخدم إلى النظام',
                $user['name'],
                $dashboardTab
            );

            // التوجيه مباشرة إلى لوحة التحكم الجديدة
            header("Location: dashboard.php?tab={$dashboardTab}");
            exit;
              }
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8" />
    <title>تسجيل الدخول - حقك تعرف</title>
    <link href="https://fonts.googleapis.com/css?family=Cairo:wght@400;700&display=swap" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet" />
    <style>
        body {
            background: #11213a;
            min-height: 100vh;
            font-family: 'Cairo', Arial, Tahoma, sans-serif;
        }
        .login-box {
            background: #192846;
            border-radius: 20px;
            box-shadow: 0 6px 30px #0003;
            padding: 2.5rem;
            margin: 70px auto;
            max-width: 410px;
        }
        .login-title {
            color: #f7c873;
            font-size: 2rem;
            font-weight: bold;
            letter-spacing: 1px;
            text-align: center;
            margin-bottom: 24px;
        }
        label {
            color: #fff;
            font-weight: 600;
        }
        .form-control {
            background: #243455;
            color: #fff;
            border-radius: 8px;
            border: 1px solid #32416a;
        }
        .form-control:focus {
            background: #233054;
            border-color: #f7c873;
            color: #fff;
        }
        .btn-primary {
            background: linear-gradient(90deg, #237cbf, #2ec5a3);
            border: none;
            border-radius: 10px;
            font-weight: bold;
            font-size: 1.1rem;
            padding: 8px 0;
            letter-spacing: 1px;
        }
        .btn-primary:hover {
            background: linear-gradient(90deg, #1b658c, #238c72);
        }
        .site-footer {
            color: #ddd;
            font-size: 0.9rem;
            text-align: center;
            margin-top: 30px;
            opacity: 0.7;
        }
        .alert {
            font-size: 1rem;
            border-radius: 10px;
        }
    </style>
</head>
<body>
    <div class="login-box">
        <div class="login-title">تسجيل الدخول</div>
        <?php if (isset($error) && $error) : ?>
            <div class="alert alert-danger text-center mb-3"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form method="POST" autocomplete="off">
            <div class="mb-3">
                <label for="username">البريد الإلكتروني أو رقم الهاتف أو اسم المستخدم</label>
                <input type="text" class="form-control" name="username" id="username" required autofocus />
            </div>
            <div class="mb-3">
                <label for="password">كلمة المرور</label>
                <input type="password" class="form-control" name="password" id="password" required autocomplete="current-password" />
            </div>
            <button type="submit" class="btn btn-primary w-100 mt-2">دخول</button>
        </form>
    </div>
    <div class="site-footer">
        © 2025 حقك تعرف. جميع الحقوق محفوظة.
    </div>
</body>
</html>

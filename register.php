<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once 'includes/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name   = trim(strip_tags($_POST['full_name'] ?? ''));
    $email       = trim(strip_tags($_POST['email'] ?? ''));
    $phone       = trim(strip_tags($_POST['phone'] ?? ''));
    $username    = trim(strip_tags($_POST['username'] ?? ''));
    $role        = $_POST['role'] ?? 'client';
    $password    = $_POST['password'] ?? '';
    $confirm_pw  = $_POST['confirm_password'] ?? '';
    $profile_img = $_FILES['profile_image'] ?? null;
    $bar_id_card = $_FILES['bar_id_card'] ?? null;

    $errors = [];

    // التحقق من القيم
    if (empty($full_name) || empty($email) || empty($phone) || empty($username) || empty($password) || empty($confirm_pw) || empty($role)) {
        $errors[] = "جميع الحقول مطلوبة.";
    }
    if ($password !== $confirm_pw) {
        $errors[] = "كلمة المرور وتأكيد كلمة المرور غير متطابقتين.";
    }
    if (strlen($password) < 6) {
        $errors[] = "كلمة المرور يجب أن لا تقل عن 6 أحرف.";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "البريد الإلكتروني غير صالح.";
    }
    if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        $errors[] = "اسم المستخدم يجب أن يكون بالأحرف والأرقام الإنجليزية فقط.";
    }
    if (strlen($phone) !== 11 || !preg_match('/^07[0-9]{9}$/', $phone)) {
        $errors[] = "رقم الهاتف يجب أن يبدأ بـ 07 ويتكون من 11 رقمًا.";
    }

    // تحقق من البريد/الهاتف/اسم المستخدم غير مكررة
    $stmt = $db->prepare("SELECT COUNT(*) FROM users WHERE email = :email OR username = :username OR phone = :phone");
    $stmt->execute(['email' => $email, 'username' => $username, 'phone' => $phone]);
    if ($stmt->fetchColumn() > 0) {
        $errors[] = "البريد الإلكتروني أو رقم الهاتف أو اسم المستخدم مستخدم مسبقًا.";
    }

    // تحقق الصورة الشخصية (اختياري)
    $imgPath = null;
    if ($profile_img && $profile_img['error'] !== UPLOAD_ERR_NO_FILE) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
        $allowedExts = ['jpg', 'jpeg', 'png'];
        $ext = strtolower(pathinfo($profile_img['name'], PATHINFO_EXTENSION));
        if (!in_array($profile_img['type'], $allowedTypes) || !in_array($ext, $allowedExts)) {
            $errors[] = "يجب أن تكون الصورة الشخصية بصيغة JPG أو PNG فقط.";
        }
        if ($profile_img['size'] > 2 * 1024 * 1024) {
            $errors[] = "حجم الصورة الشخصية يجب أن لا يتجاوز 2 ميجابايت.";
        }
    }

    // تحقق هوية النقابة إذا كان الدور محامي
    $barIdPath = null;
    if ($role === 'lawyer') {
        if (!$bar_id_card || $bar_id_card['error'] === UPLOAD_ERR_NO_FILE) {
            $errors[] = "يجب إرفاق صورة من هوية النقابة.";
        } else {
            $allowedBarTypes = ['image/jpeg', 'image/png', 'image/jpg', 'application/pdf'];
            $allowedBarExts  = ['jpg', 'jpeg', 'png', 'pdf'];
            $ext2 = strtolower(pathinfo($bar_id_card['name'], PATHINFO_EXTENSION));
            if (!in_array($bar_id_card['type'], $allowedBarTypes) || !in_array($ext2, $allowedBarExts)) {
                $errors[] = "يجب أن تكون هوية النقابة بصيغة JPG أو PNG أو PDF فقط.";
            }
            if ($bar_id_card['size'] > 2 * 1024 * 1024) {
                $errors[] = "حجم هوية النقابة يجب أن لا يتجاوز 2 ميجابايت.";
            }
        }
    }

    if (empty($errors)) {
        $pw_hash = password_hash($password, PASSWORD_DEFAULT);

        // جلب رقم الدور واسم الدور المناسب
        $roleStmt = $db->prepare("SELECT id, role_name FROM roles WHERE role_name = :role LIMIT 1");
        $roleStmt->execute(['role' => $role]);
        $roleData = $roleStmt->fetch(PDO::FETCH_ASSOC);
        $role_id = $roleData ? $roleData['id'] : null;
        $role_name = $roleData ? $roleData['role_name'] : $role;

        // رفع الصورة الشخصية إذا وجدت
        if ($profile_img && $profile_img['error'] === UPLOAD_ERR_OK) {
            $ext = pathinfo($profile_img['name'], PATHINFO_EXTENSION);
            $imgPath = 'uploads/profile_' . time() . '_' . rand(100,999) . '.' . $ext;
            if (!move_uploaded_file($profile_img['tmp_name'], $imgPath)) {
                $imgPath = null;
            }
        }

        // رفع هوية النقابة إذا كان محامي
        if ($role === 'lawyer' && $bar_id_card && $bar_id_card['error'] === UPLOAD_ERR_OK) {
            $ext2 = pathinfo($bar_id_card['name'], PATHINFO_EXTENSION);
            $barIdPath = 'uploads/barid_' . time() . '_' . rand(100,999) . '.' . $ext2;
            if (!move_uploaded_file($bar_id_card['tmp_name'], $barIdPath)) {
                $barIdPath = null;
            }
        }

        // تسجيل الحساب مع role_id واسم الدور
        $insert = $db->prepare("INSERT INTO users (name, email, phone, username, password, role_id, role, status, profile_image, bar_id_card)
                                VALUES (:name, :email, :phone, :username, :password, :role_id, :role, 'active', :profile_image, :bar_id_card)");
        $inserted = $insert->execute([
            'name'         => $full_name,
            'email'        => $email,
            'phone'        => $phone,
            'username'     => $username,
            'password'     => $pw_hash,
            'role_id'      => $role_id,
            'role'         => $role_name,
            'profile_image'=> $imgPath,
            'bar_id_card'  => $barIdPath
        ]);

        if ($inserted) {
            // يمكن هنا تسجيل العملية في activity_log أو إرسال رسالة ترحيب للمستخدم
            header("Location: login.php?msg=registered");
            exit;
        } else {
            $errors[] = "حدث خطأ أثناء التسجيل، يرجى المحاولة لاحقًا.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تسجيل حساب جديد - حقك تعرف</title>
    <link href="https://fonts.googleapis.com/css?family=Cairo:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
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
            padding: 2.5rem 2.5rem 2rem 2.5rem;
            margin: 70px auto;
            max-width: 430px;
        }
        .login-title {
            color: #f7c873;
            font-size: 2rem;
            font-weight: bold;
            text-align: center;
            margin-bottom: 24px;
        }
        label { color: #fff; font-weight: 600; }
        .form-control, .form-select {
            background: #243455;
            color: #fff;
            border-radius: 8px;
            border: 1px solid #32416a;
        }
        .form-control:focus, .form-select:focus {
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
        .alert { font-size: 1rem; border-radius: 10px; }
        .note { color: #f7c873; font-size: .97rem; margin-top: 4px; }
    </style>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        var roleSelect = document.getElementById('role');
        var barIdDiv = document.getElementById('bar_id_card_div');
        function toggleBarIdCard() {
            if(roleSelect.value === 'lawyer') {
                barIdDiv.style.display = 'block';
            } else {
                barIdDiv.style.display = 'none';
            }
        }
        roleSelect.addEventListener('change', toggleBarIdCard);
        toggleBarIdCard();
    });
    </script>
</head>
<body>
    <div class="login-box">
        <div class="login-title">تسجيل حساب جديد</div>
        <?php
        if (!empty($errors)) {
            echo '<div class="alert alert-danger text-center mb-3">' . implode("<br>", $errors) . '</div>';
        }
        if (isset($success)) {
            echo '<div class="alert alert-success text-center mb-3">' . $success . '</div>';
        }
        ?>
        <form method="POST" autocomplete="off" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="full_name">الاسم الكامل</label>
                <input type="text" class="form-control" name="full_name" id="full_name" required value="<?= htmlspecialchars($_POST['full_name'] ?? '') ?>">
            </div>
            <div class="mb-3">
                <label for="email">البريد الإلكتروني</label>
                <input type="email" class="form-control" name="email" id="email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
            </div>
            <div class="mb-3">
                <label for="phone">رقم الهاتف</label>
                <input type="text" class="form-control" name="phone" id="phone" required value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
            </div>
            <div class="mb-3">
                <label for="username">اسم المستخدم</label>
                <input type="text" class="form-control" name="username" id="username" required value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
            </div>
            <div class="mb-3">
                <label for="role">نوع الحساب</label>
                <select name="role" id="role" class="form-select" required>
                    <option value="client" <?= (($_POST['role'] ?? '')=='client' ? 'selected':'') ?>>عميل/مواطن</option>
                    <option value="lawyer" <?= (($_POST['role'] ?? '')=='lawyer' ? 'selected':'') ?>>محامي</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="profile_image">الصورة الشخصية (اختياري)</label>
                <input type="file" class="form-control" name="profile_image" id="profile_image" accept="image/png,image/jpeg">
            </div>
            <div class="mb-3" id="bar_id_card_div" style="display: none;">
                <label for="bar_id_card">صورة من هوية النقابة <span style="color:#ff6464">*</span></label>
                <input type="file" class="form-control" name="bar_id_card" id="bar_id_card" accept="image/png,image/jpeg,image/jpg,application/pdf">
                <div class="note">يجب أن تكون الهوية <b>غير منتهية الصلاحية</b></div>
            </div>
            <div class="mb-3">
                <label for="password">كلمة المرور</label>
                <input type="password" class="form-control" name="password" id="password" required autocomplete="new-password">
            </div>
            <div class="mb-3">
                <label for="confirm_password">تأكيد كلمة المرور</label>
                <input type="password" class="form-control" name="confirm_password" id="confirm_password" required autocomplete="new-password">
            </div>
            <button type="submit" class="btn btn-primary w-100 mt-2">تسجيل</button>
        </form>
        <div class="text-center mt-3">
            <a href="login.php" class="link-light" style="text-decoration: underline; font-size: 1rem;">
                هل لديك حساب؟ سجل الدخول
            </a>
            <br>
            <a href="forgot_password.php" class="link-warning" style="text-decoration: underline; font-size: 0.97rem;">
                هل نسيت كلمة المرور؟
            </a>
        </div>
    </div>
    <div class="site-footer">
        © 2025 حقك تعرف. جميع الحقوق محفوظة.
    </div>
</body>
</html>

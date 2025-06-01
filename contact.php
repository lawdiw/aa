<?php
session_start();
require_once 'includes/db_connect.php';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = trim($_POST['name'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    $user_id = $_SESSION['user_id'] ?? null;

    if (!$name || !$email || !$subject || !$message) {
        $error = 'يرجى ملء جميع الحقول.';
    } else {
        if ($user_id) {
            // تأكد أن رقم المستخدم فعليًا في قاعدة البيانات
            $stmtCheck = $db->prepare("SELECT id FROM users WHERE id = ?");
            $stmtCheck->execute([$user_id]);
            if ($stmtCheck->fetch()) {
                // حفظ مع user_id
                $stmt = $db->prepare("INSERT INTO contact_messages (user_id, name, email, subject, message, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
                $exec = $stmt->execute([$user_id, $name, $email, $subject, $message]);
            } else {
                // حفظ كزائر فقط
                $stmt = $db->prepare("INSERT INTO contact_messages (name, email, subject, message, created_at) VALUES (?, ?, ?, ?, NOW())");
                $exec = $stmt->execute([$name, $email, $subject, $message]);
            }
        } else {
            // زائر فقط
            $stmt = $db->prepare("INSERT INTO contact_messages (name, email, subject, message, created_at) VALUES (?, ?, ?, ?, NOW())");
            $exec = $stmt->execute([$name, $email, $subject, $message]);
        }

        if ($exec) {
            $success = 'تم إرسال رسالتك بنجاح. سنرد عليك قريباً.';
        } else {
            $error = 'حدث خطأ أثناء إرسال الرسالة، حاول مرة أخرى.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>اتصل بنا - حقك تعرف</title>
  <link href="https://fonts.googleapis.com/css2?family=Cairo&family=Tajawal&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" />
  <link rel="stylesheet" href="css/style.css" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <style>
    body { background-color: #11213a; color: #fff; font-family: 'Cairo', 'Tajawal', sans-serif; }
    .contact-container {
      background: #192846;
      border-radius: 18px;
      box-shadow: 0 6px 30px #0008;
      max-width: 800px;
      margin: 50px auto;
      padding: 2.5rem 2.2rem 2rem 2.2rem;
      display: flex;
      gap: 2.2rem;
      flex-wrap: wrap;
    }
    .contact-form {
      flex: 2;
      min-width: 300px;
    }
    .contact-title {
      color: #f7c873;
      text-align: center;
      font-weight: bold;
      margin-bottom: 18px;
      font-size: 2rem;
    }
    .form-label { color: #f7c873; font-weight: 600; }
    .form-control {
      background: #233254;
      color: #fff;
      border-radius: 10px;
      border: 1px solid #2ec5a3;
      margin-bottom: 12px;
    }
    .form-control:focus {
      background: #253360;
      border-color: #f7c873;
      color: #fff;
      box-shadow: none;
    }
    .btn-primary {
      background: linear-gradient(90deg, #237cbf, #2ec5a3);
      border: none;
      border-radius: 12px;
      font-weight: bold;
      font-size: 1.1rem;
      width: 100%;
      padding: 11px 0;
      margin-top: 10px;
      transition: background 0.3s;
    }
    .btn-primary:hover { background: linear-gradient(90deg, #1b658c, #238c72);}
    .alert { border-radius: 10px; font-size: 1rem; }
    .contact-info {
      flex: 1;
      min-width: 230px;
      margin-top: 22px;
      background: #203456;
      padding: 1.1rem 1.3rem;
      border-radius: 10px;
      color: #fff;
      align-self: flex-start;
    }
    .contact-info ul {
      padding-left: 0;
      list-style: none;
      margin-bottom: 0;
    }
    .contact-info li {
      margin-bottom: 12px;
      font-size: 1.07rem;
      font-weight: 500;
    }
    .contact-info i { color: #2ec5a3; margin-left: 8px;}
    .back-link {
      display: block;
      text-align: center;
      margin-top: 18px;
      color: #f7c873;
      font-weight: 600;
      font-size: 1.05rem;
    }
    .back-link:hover { text-decoration: underline; color: #2ec5a3; }
    @media (max-width: 900px) {
      .contact-container { flex-direction: column; gap: 0; padding: 2rem 1.1rem; }
      .contact-info { margin-top: 30px; }
    }
  </style>
</head>
<body>

<?php include 'includes/navigation.php'; ?>

<div class="contact-container mt-5">
  <div class="contact-form">
    <div class="contact-title">تواصل معنا</div>
    <?php if ($success): ?>
      <div class="alert alert-success text-center"><?= htmlspecialchars($success) ?></div>
    <?php elseif ($error): ?>
      <div class="alert alert-danger text-center"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" autocomplete="off">
      <div class="mb-2">
        <label class="form-label" for="name">الاسم الكامل</label>
        <input type="text" id="name" name="name" class="form-control" required value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
      </div>
      <div class="mb-2">
        <label class="form-label" for="email">البريد الإلكتروني</label>
        <input type="email" id="email" name="email" class="form-control" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
      </div>
      <div class="mb-2">
        <label class="form-label" for="subject">الموضوع</label>
        <input type="text" id="subject" name="subject" class="form-control" required value="<?= htmlspecialchars($_POST['subject'] ?? '') ?>">
      </div>
      <div class="mb-2">
        <label class="form-label" for="message">رسالتك</label>
        <textarea id="message" name="message" class="form-control" rows="4" required><?= htmlspecialchars($_POST['message'] ?? '') ?></textarea>
      </div>
      <button type="submit" class="btn btn-primary">إرسال الرسالة</button>
    </form>
    <?php if(isset($_SESSION['user_id'])): ?>
      <a href="client_dashboard.php" class="back-link"><i class="bi bi-arrow-right-circle"></i> رجوع إلى لوحة التحكم</a>
    <?php else: ?>
      <a href="index.php" class="back-link"><i class="bi bi-arrow-right-circle"></i> الرجوع للصفحة الرئيسية</a>
    <?php endif; ?>
  </div>

<div class="contact-info">
  <div class="mb-2 fw-bold" style="font-size:1.14rem; color:#f7c873;">
    <i class="bi bi-info-circle"></i> معلومات الاتصال
  </div>
  <ul class="ps-0 mb-0" style="list-style:none;">
    <li class="mb-2 d-flex align-items-center">
      <i class="bi bi-telephone-fill me-2" style="font-size:1.15em; color:#2ec5a3;"></i>
      <div style="display:flex; flex-direction:column;">
        <a href="tel:07809799956" style="color:#2ec5a3; text-decoration:none; direction:ltr; font-family:monospace; margin-bottom:2px;">0780 979 9956</a>
        <a href="tel:07700151262" style="color:#2ec5a3; text-decoration:none; direction:ltr; font-family:monospace;">0770 015 1262</a>
      </div>
    </li>
    <li class="mb-2 d-flex align-items-center">
      <i class="bi bi-envelope-fill me-2" style="font-size:1.15em; color:#2ec5a3;"></i>
      <a href="mailto:haquktaerif@gmail.com" style="color:#2ec5a3; text-decoration:none;">haquktaerif@gmail.com</a>
    </li>
    <li class="mb-2 d-flex align-items-center">
      <i class="bi bi-geo-alt-fill me-2" style="font-size:1.15em; color:#2ec5a3;"></i>
      <span>بغداد - العراق</span>
    </li>
    <li class="mb-2 d-flex align-items-center">
      <i class="bi bi-clock-fill me-2" style="font-size:1.15em; color:#2ec5a3;"></i>
      <span>السبت - الخميس: 8:00 ص - 4:00 م</span>
    </li>
    <li class="d-flex align-items-center">
      <i class="bi bi-whatsapp me-2" style="font-size:1.15em; color:#2ec5a3;"></i>
      <a href="https://wa.me/9647809799956" style="color:#2ec5a3; text-decoration:none;">دردشة واتساب</a>
    </li>
  </ul>
</div>

<footer style="background: linear-gradient(135deg, #192846, #f7c873); color: #fff; padding: 18px 0; margin-top: 38px; text-align:center; font-weight:600; font-size:1rem;">
  <div class="container text-center">
    © 2025 حقك تعرف. جميع الحقوق محفوظة.
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

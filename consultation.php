<?php
session_start();
require_once 'includes/db_connect.php';

// تأمين الصفحة للمستخدمين المسجلين بدور 'client'
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'client') {
    header("Location: login.php");
    exit;
}

$success = '';
$error = '';

// جلب أنواع القضايا من قاعدة البيانات
$caseTypes = [];
try {
    $stmt = $db->query("SELECT id, group_name, type_name FROM case_types ORDER BY group_name, type_name");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $caseTypes[$row['group_name']][] = $row;
    }
} catch (Exception $e) {
    $caseTypes = [];
}

// قائمة المحافظات
$governorates = [
    "بغداد","البصرة","نينوى","أربيل","النجف","كربلاء","ديالى","الأنبار","صلاح الدين","دهوك",
    "كركوك","واسط","ميسان","ذي قار","المثنى","بابل","القادسية","السليمانية"
];

// عند إرسال النموذج
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $caseTypeId      = intval($_POST['caseType'] ?? 0);
    $fullName        = trim($_POST['fullName'] ?? '');
    $phone           = trim($_POST['phone'] ?? '');
    $governorate     = trim($_POST['governorate'] ?? '');
    $gender          = trim($_POST['gender'] ?? '');
    $message         = trim($_POST['message'] ?? '');
    $delivery_method = trim($_POST['delivery_method'] ?? '');
    $attachments     = ''; // معالجة الملفات إذا أردت لاحقاً

    // جلب اسم نوع القضية للموضوع
    $subject = '';
    $stmtCase = $db->prepare("SELECT type_name FROM case_types WHERE id = ?");
    $stmtCase->execute([$caseTypeId]);
    if ($row = $stmtCase->fetch(PDO::FETCH_ASSOC)) {
        $subject = $row['type_name'];
    }
    $stmtCase = null;

    // إدخال البيانات
    try {
        $stmt = $db->prepare("INSERT INTO consultations
            (user_id, case_type_id, subject, full_name, phone, governorate, gender, message, delivery_method, attachments, created_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())"
        );
        $ok = $stmt->execute([
            $_SESSION['user_id'], $caseTypeId, $subject, $fullName, $phone, $governorate, $gender,
            $message, $delivery_method, $attachments
        ]);
        if ($ok) {
            echo '<script>
                alert("✅ تم إرسال الاستشارة بنجاح. سيتم تحويلك للوحة التحكم");
                window.location.href = "client_dashboard.php";
            </script>';
            exit;
        } else {
            $error = 'حدث خطأ أثناء إرسال الاستشارة.';
        }
    } catch (PDOException $e) {
        $error = 'حدث خطأ أثناء إرسال الاستشارة: ' . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>طلب استشارة قانونية - حقك تعرف</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo&family=Tajawal&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" />
    <link rel="stylesheet" href="css/style.css" />
    <style>
        body { background: #11213a; color: #fff; font-family: 'Cairo', 'Tajawal', sans-serif;}
        .container-box {
            background: #192846;
            border-radius: 18px;
            box-shadow: 0 6px 30px #0008;
            max-width: 800px;
            margin: 55px auto;
            padding: 2.5rem 2.2rem;
        }
        .consult-title {
            color: #f7c873;
            text-align: center;
            font-weight: bold;
            margin-bottom: 22px;
            font-size: 2rem;
        }
        .alert-custom {
            background: linear-gradient(90deg, #f7c873, #2ec5a3);
            color: #11213a;
            border-radius: 10px;
            font-weight: 600;
            font-size: 1.12rem;
            box-shadow: 0 1px 12px #0004;
            padding: 1rem;
            margin-bottom: 1.7rem;
            text-align: center;
        }
        .form-label { color: #f7c873; font-weight: 600; }
        .form-control, .form-select {
            background: #233254 !important;
            color: #fff !important;
            border-radius: 10px;
            border: 1px solid #2ec5a3;
            margin-bottom: 13px;
        }
        .form-control:focus, .form-select:focus {
            background: #253360;
            border-color: #f7c873;
            color: #fff;
            box-shadow: none;
        }
        .form-control::placeholder { color: #c3c8d3 !important; }
        .btn-primary {
            background: linear-gradient(90deg, #237cbf, #2ec5a3);
            border: none;
            border-radius: 12px;
            font-weight: bold;
            font-size: 1.1rem;
            padding: 11px 0;
            margin-top: 10px;
            width: 100%;
            transition: background 0.3s;
        }
        .btn-primary:hover { background: linear-gradient(90deg, #1b658c, #238c72);}
        .form-check-label { color: #fff; }
    </style>
</head>
<body>
<?php include 'includes/navigation.php'; ?>
<div class="container-box mt-4">
    <!-- التنويه -->
    <div class="alert-custom mb-4">
        <i class="bi bi-exclamation-triangle-fill"></i>
        <strong>تنويه مهم:</strong>
        الاستشارات القانونية على المنصة <b>ليست مجانية</b>، ويجب دفع الأتعاب المحددة قبل استلام الرد من المحامي المختص. جميع الأسعار تظهر بوضوح قبل تأكيد الطلب.
    </div>
    <div class="consult-title">طلب استشارة قانونية</div>
    <?php if ($error): ?>
        <div class="alert alert-danger text-center"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="POST" autocomplete="off" enctype="multipart/form-data">
        <div class="row">
            <div class="col-md-6">
                <label class="form-label" for="caseType">نوع القضية <span class="text-danger">*</span></label>
                <select id="caseType" name="caseType" class="form-select" required>
                    <option value="">اختر نوع القضية ...</option>
                    <?php foreach ($caseTypes as $group => $items): ?>
                        <optgroup label="<?= htmlspecialchars($group) ?>">
                            <?php foreach ($items as $item): ?>
                                <option value="<?= $item['id'] ?>" <?= (isset($_POST['caseType']) && $_POST['caseType']==$item['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($item['type_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </optgroup>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label" for="governorate">المحافظة <span class="text-danger">*</span></label>
                <select id="governorate" name="governorate" class="form-select" required>
                    <option value="">اختر المحافظة ...</option>
                    <?php foreach ($governorates as $g): ?>
                        <option value="<?= htmlspecialchars($g) ?>" <?= (isset($_POST['governorate']) && $_POST['governorate']==$g) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($g) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <label class="form-label" for="fullName">الاسم الكامل <span class="text-danger">*</span></label>
                <input id="fullName" name="fullName" class="form-control" required value="<?= htmlspecialchars($_POST['fullName'] ?? '') ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label" for="gender">الجنس <span class="text-danger">*</span></label>
                <select id="gender" name="gender" class="form-select" required>
                    <option value="">اختر ...</option>
                    <option value="ذكر" <?= (isset($_POST['gender']) && $_POST['gender']=='ذكر') ? 'selected' : '' ?>>ذكر</option>
                    <option value="أنثى" <?= (isset($_POST['gender']) && $_POST['gender']=='أنثى') ? 'selected' : '' ?>>أنثى</option>
                </select>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <label class="form-label" for="phone">رقم الهاتف <span class="text-danger">*</span></label>
                <input id="phone" name="phone" class="form-control" placeholder="07XXXXXXXXX" required value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label" for="delivery_method">طريقة استلام الرد <span class="text-danger">*</span></label>
                <select id="delivery_method" name="delivery_method" class="form-select" required>
                    <option value="">اختر ...</option>
                    <option value="داخل المنصة" <?= (isset($_POST['delivery_method']) && $_POST['delivery_method']=='داخل المنصة') ? 'selected' : '' ?>>داخل المنصة</option>
                    <option value="بريد إلكتروني" <?= (isset($_POST['delivery_method']) && $_POST['delivery_method']=='بريد إلكتروني') ? 'selected' : '' ?>>بريد إلكتروني</option>
                    <option value="واتساب" <?= (isset($_POST['delivery_method']) && $_POST['delivery_method']=='واتساب') ? 'selected' : '' ?>>واتساب</option>
                    <option value="اتصال هاتفي" <?= (isset($_POST['delivery_method']) && $_POST['delivery_method']=='اتصال هاتفي') ? 'selected' : '' ?>>اتصال هاتفي</option>
                </select>
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label" for="message">تفاصيل الاستشارة <span class="text-danger">*</span></label>
            <textarea id="message" name="message" rows="4" class="form-control" required><?= htmlspecialchars($_POST['message'] ?? '') ?></textarea>
        </div>
        <div class="mb-3">
            <label class="form-label" for="attachments">إرفاق ملفات أو فيديو (اختياري)</label>
            <input type="file" id="attachments" name="attachments[]" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.mp4,.avi,.mov" multiple>
        </div>
        <button type="submit" class="btn btn-primary">إرسال الاستشارة</button>
    </form>
</div>
<footer style="background: linear-gradient(135deg, #192846, #f7c873); color: #fff; padding: 18px 0; margin-top: 38px; text-align:center; font-weight:600; font-size:1rem;">
  <div class="container text-center">
    © 2025 حقك تعرف. جميع الحقوق محفوظة.
  </div>
</footer>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

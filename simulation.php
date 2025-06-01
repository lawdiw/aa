<?php
session_start();
require_once 'includes/db_connect.php';

// استقبال الرسائل الجديدة
$success = $error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['scenario_text'])) {
    $scenario_text = trim($_POST['scenario_text']);
    $governorate = trim($_POST['governorate'] ?? '');
    $case_type_id = intval($_POST['case_type_id'] ?? 0);
    $description = trim($_POST['description'] ?? '');
    $user_id = $_SESSION['user_id'] ?? null;

    if ($scenario_text !== '' && $governorate !== '' && $case_type_id > 0) {
        $stmt = $db->prepare("INSERT INTO legal_simulations (user_id, scenario_text, governorate, case_type_id, description, approved, submitted_at) VALUES (?, ?, ?, ?, ?, 0, NOW())");
        if ($stmt->execute([$user_id, $scenario_text, $governorate, $case_type_id, $description])) {
            $success = "تم إرسال موقفك القانوني بنجاح! سيتم مراجعته والموافقة عليه من قبل الإدارة قبل النشر.";
        } else {
            $error = "حدث خطأ أثناء الإرسال، حاول مرة أخرى.";
        }
    } else {
        $error = "يرجى ملء جميع الحقول المطلوبة.";
    }
}

// جلب أنواع القضايا من قاعدة البيانات
$case_types = [];
$caseTypeQuery = $db->query("SELECT id, type_name FROM case_types ORDER BY type_name ASC");
while ($ct = $caseTypeQuery->fetch(PDO::FETCH_ASSOC)) {
    $case_types[] = $ct;
}

// جلب المواقف القانونية المعتمدة فقط
$stmt = $db->prepare("SELECT s.id, s.scenario_text, s.governorate, s.case_type_id, s.description, s.submitted_at, u.name 
                      FROM legal_simulations s 
                      LEFT JOIN users u ON s.user_id = u.id 
                      WHERE s.approved = 1
                      ORDER BY s.submitted_at DESC 
                      LIMIT 20");
$stmt->execute();
$scenarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

// مصفوفة المحافظات (يمكنك تعديلها حسب الحاجة)
$governorates = [
    "بغداد","البصرة","نينوى","أربيل","النجف","كربلاء","ديالى","صلاح الدين","الأنبار","الديوانية",
    "واسط","بابل","ذي قار","ميسان","المثنى","كركوك","دهوك","السليمانية"
];
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>المحاكاة القانونية - حقك تعرف</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo&family=Tajawal&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" />
    <link rel="stylesheet" href="css/style.css" />
    <style>
        body { background-color: #11213a; color: #fff; font-family: 'Cairo', 'Tajawal', sans-serif; }
        .container-box { background: #192846; border-radius: 20px; max-width: 1100px; margin: 30px auto 50px auto; box-shadow: 0 6px 30px #0007; padding: 2.2rem 2rem; }
        h1, h2, h3 { color: #f7c873; font-weight: bold; letter-spacing: .5px; text-align: center; }
        .scenario-card { background: #223158; color: #fff; border-radius: 14px; box-shadow: 0 3px 16px #0005; margin-bottom: 22px; border: none; }
        .scenario-card .card-body { font-size: 1.05rem; }
        .scenario-card .scenario-meta { color: #2ec5a3; font-size: .98rem; }
        .btn-scenario { background: linear-gradient(90deg, #237cbf, #2ec5a3); color: #fff; border-radius: 9px; font-weight: 600; border: none; }
        .btn-scenario:hover { background: linear-gradient(90deg, #1b658c, #238c72); }
        /* تنسيقات النافذة المنبثقة (modal) */
        .modal-content .form-control,
        .modal-content .form-select {
            background-color: #1b213a !important;
            color: #fff !important;
            border: 1px solid #2ec5a3;
        }
        .modal-content .form-control::placeholder,
        .modal-content .form-select option {
            color: #bfcad7 !important;
        }
        .modal-content .form-control:focus,
        .modal-content .form-select:focus {
            background-color: #232b44 !important;
            color: #fff !important;
            border-color: #FFD700 !important;
        }
        .modal-header { background: #192846; color: #f7c873; border-bottom: 1px solid #2ec5a3;}
        .modal-title { color: #FFD700; font-weight: bold;}
        .btn-close { filter: invert(1);}
        /* تنويه */
        .alert-sim-note {
            background: linear-gradient(90deg, #3b4c5e, #11213a 90%);
            color: #FFD700;
            border: 2px dashed #FFD700;
            border-radius: 11px;
            font-weight: bold;
            font-size: 1.08rem;
            margin-bottom: 30px;
            padding: 16px 14px;
            text-align: center;
        }
        .form-label { color: #f7c873 !important; font-weight: 600; }
    </style>
</head>
<body>
<?php include 'includes/navigation.php'; ?>

<div class="container-box">
    <h1 class="mb-4"><i class="bi bi-cpu"></i> المحاكاة القانونية التفاعلية</h1>
    <div class="alert-sim-note">
        <i class="bi bi-exclamation-triangle-fill"></i>
        تنويه مهم: المحاكاة القانونية في هذه الصفحة تمثل أمثلة واقعية لغرض التوعية، ولا تعتبر استشارة قانونية ملزمة. يمكنك إرسال موقفك القانوني وسيتم مراجعته من الإدارة قبل النشر.
    </div>

    <!-- زر إضافة موقف جديد -->
    <div class="text-end mb-3">
        <button class="btn btn-scenario" data-bs-toggle="modal" data-bs-target="#addScenarioModal">
            <i class="bi bi-plus-circle"></i> أرسل موقفك القانوني
        </button>
    </div>

    <!-- استعراض المواقف القانونية من قاعدة البيانات -->
    <h3 class="mb-4">مواقف قانونية من الواقع</h3>
    <?php if (count($scenarios) > 0): ?>
        <?php foreach($scenarios as $row): ?>
            <div class="card scenario-card">
                <div class="card-body">
                    <div class="mb-2"><b>الموقف:</b> <?= nl2br(htmlspecialchars($row['scenario_text'])) ?></div>
                    <?php if ($row['governorate']): ?>
                        <div><i class="bi bi-geo-alt-fill"></i> <span class="ms-2"><?= htmlspecialchars($row['governorate']) ?></span></div>
                    <?php endif; ?>
                    <?php if ($row['case_type_id'] && isset($case_types[array_search($row['case_type_id'], array_column($case_types, 'id'))]['type_name'])): ?>
                        <div><i class="bi bi-journal-text"></i> نوع القضية: <?= htmlspecialchars($case_types[array_search($row['case_type_id'], array_column($case_types, 'id'))]['type_name']) ?></div>
                    <?php endif; ?>
                    <?php if ($row['description']): ?>
                        <div><i class="bi bi-chat-left-text"></i> وصف: <?= nl2br(htmlspecialchars($row['description'])) ?></div>
                    <?php endif; ?>
                    <div class="scenario-meta mt-2">
                        <i class="bi bi-person-bounding-box"></i>
                        <?= htmlspecialchars($row['name'] ?? 'مستخدم مجهول') ?> &nbsp; 
                        <i class="bi bi-clock"></i> 
                        <?= date('Y-m-d H:i', strtotime($row['submitted_at'])) ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="alert alert-info text-center mt-4">لا توجد مواقف قانونية مُضافة بعد. كن أول من يشارك موقفه.</div>
    <?php endif; ?>
</div>

<!-- نافذة إضافة موقف قانوني جديد -->
<div class="modal fade" id="addScenarioModal" tabindex="-1" aria-labelledby="addScenarioModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form method="POST">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addScenarioModalLabel"><i class="bi bi-pencil-square"></i> أرسل موقفك القانوني</h5>
          <button type="button" class="btn-close ms-0" data-bs-dismiss="modal" aria-label="إغلاق"></button>
        </div>
        <div class="modal-body">
          <?php if ($success): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
          <?php elseif ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
          <?php endif; ?>
          <div class="mb-3">
            <label for="scenario_text" class="form-label">أكتب الموقف القانوني <span class="text-danger">*</span></label>
            <textarea class="form-control" id="scenario_text" name="scenario_text" rows="4" required></textarea>
          </div>
          <div class="mb-3">
            <label for="governorate" class="form-label">المحافظة <span class="text-danger">*</span></label>
            <select id="governorate" name="governorate" class="form-select" required>
                <option value="">اختر المحافظة...</option>
                <?php foreach ($governorates as $gov): ?>
                    <option value="<?= htmlspecialchars($gov) ?>"><?= htmlspecialchars($gov) ?></option>
                <?php endforeach; ?>
            </select>
          </div>
          <div class="mb-3">
            <label for="case_type_id" class="form-label">نوع القضية <span class="text-danger">*</span></label>
            <select id="case_type_id" name="case_type_id" class="form-select" required>
                <option value="">اختر نوع القضية...</option>
                <?php foreach ($case_types as $ct): ?>
                    <option value="<?= $ct['id'] ?>"><?= htmlspecialchars($ct['type_name']) ?></option>
                <?php endforeach; ?>
            </select>
          </div>
          <div class="mb-3">
            <label for="description" class="form-label">وصف مختصر للموقف (اختياري)</label>
            <textarea class="form-control" id="description" name="description" rows="2"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-scenario">إرسال</button>
        </div>
      </div>
    </form>
  </div>
</div>

<footer style="background: linear-gradient(135deg, #192846, #f7c873); color: #fff; padding: 18px 0; margin-top: 35px; text-align:center; font-weight:600; font-size:1rem;">
  <div class="container text-center">
    © 2025 حقك تعرف. جميع الحقوق محفوظة.
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

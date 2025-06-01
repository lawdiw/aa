<?php
session_start();
require_once 'includes/db_connect.php';

// دالة تسجيل النشاط
function logActivity($db, $userId, $username, $role, $actionType, $actionDesc, $ipAddress = 'غير معروف') {
    $stmt = $db->prepare("INSERT INTO activity_log (user_id, username, role, action_type, action_desc, ip_address, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
    $stmt->execute([$userId, $username, $role, $actionType, $actionDesc, $ipAddress]);
}

// إضافة دور المدير (manager) لصلاحيات تعيين المحامي
if (
    !isset($_SESSION['user_id']) ||
    !in_array($_SESSION['user_role'], ['admin', 'super_admin', 'manager'])
) {
    header("Location: login.php");
    exit;
}

$consultation_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($consultation_id <= 0) {
    die("<div class='alert alert-danger text-center mt-5'>رقم الاستشارة غير صالح.</div>");
}

// جلب الاستشارة المطلوبة
$stmt = $db->prepare("SELECT * FROM consultations WHERE id = ?");
$stmt->execute([$consultation_id]);
$consult = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$consult) {
    die("<div class='alert alert-danger text-center mt-5'>الاستشارة غير موجودة.</div>");
}

// جلب قائمة المحامين
$lawyers = $db->query("SELECT id, name FROM lawyers ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);

// جلب أعداد الاستشارات المحالة لكل محامي
$lawyerStats = [];
$res = $db->query("SELECT lawyer_id, COUNT(*) AS total FROM consultations WHERE lawyer_id IS NOT NULL GROUP BY lawyer_id");
foreach ($res->fetchAll(PDO::FETCH_ASSOC) as $row) {
    $lawyerStats[$row['lawyer_id']] = $row['total'];
}
$totalAssigned = array_sum($lawyerStats);

$assign_error = '';
$success_message = '';
$disable_assign = ($consult['status'] == 'closed' || $consult['status'] == 'completed');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['lawyer_id']) && !$disable_assign) {
    $lawyer_id = intval($_POST['lawyer_id']);
    // حماية من التكرار
    if ($consult['lawyer_id'] == $lawyer_id) {
        $assign_error = "المحامي مختار بالفعل لهذه الاستشارة.";
    } else {
        $stmt = $db->prepare("SELECT id FROM lawyers WHERE id = ?");
        $stmt->execute([$lawyer_id]);
        $lawyer_exists = $stmt->fetchColumn();

        if (!$lawyer_exists) {
            $assign_error = "المحامي غير موجود.";
        } else {
            // سجل العملية قبل التغيير
            $stmtLog = $db->prepare("INSERT INTO lawyer_assignments 
                (consultation_id, old_lawyer_id, new_lawyer_id, assigned_by, note) 
                VALUES (?, ?, ?, ?, ?)");
            $stmtLog->execute([
                $consultation_id, $consult['lawyer_id'], $lawyer_id, $_SESSION['user_id'], NULL
            ]);

            // حدث الاستشارة
            $stmt = $db->prepare("UPDATE consultations SET lawyer_id = ? WHERE id = ?");
            $stmt->execute([$lawyer_id, $consultation_id]);

            // تسجيل النشاط
            $userId = $_SESSION['user_id'];
            $username = $_SESSION['user_name'] ?? 'غير معروف';
            $role = $_SESSION['user_role'] ?? 'غير محدد';
            $ipAddress = $_SERVER['REMOTE_ADDR'] ?? 'غير معروف';

            $oldLawyer = $consult['lawyer_id'] ? "المحامي السابق ID: " . $consult['lawyer_id'] : "لم يكن هناك محامي معين";
            $newLawyer = "المحامي الجديد ID: $lawyer_id";
            $actionDesc = "تم تعيين محامي للاستشارة رقم $consultation_id. $oldLawyer، $newLawyer";

            logActivity($db, $userId, $username, $role, 'assign_lawyer', $actionDesc, $ipAddress);

            // إعادة التوجيه بعد النجاح
            header("Location: view_consultation.php?id=" . $consultation_id . "&assigned=1");
            exit;
        }
    }
}

function statusLabel($status) {
    switch($status) {
        case 'pending':     return '<span class="badge bg-warning text-dark">قيد المراجعة</span>';
        case 'in_progress': return '<span class="badge bg-info text-dark">قيد التنفيذ</span>';
        case 'completed':   return '<span class="badge bg-success">تم الرد</span>';
        case 'rejected':    return '<span class="badge bg-danger">مرفوضة</span>';
        case 'closed':      return '<span class="badge bg-secondary">مغلقة</span>';
        default:            return '<span class="badge bg-secondary">'.htmlspecialchars($status).'</span>';
    }
}
function lawyerDisplayName($l) {
    return (!empty($l['name'])) ? $l['name'] : "محامي بدون اسم";
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تعيين محامي للاستشارة - حقك تعرف</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        body {background: #f7fafd;}
        .main-card {
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 4px 24px #11213a18;
            padding: 30px 30px 24px 30px;
            max-width: 700px;
            margin: auto;
        }
        .dashboard-header {
            background: linear-gradient(95deg, #f7c873 60%, #fff 100%);
            color: #233054;
            padding: 22px 0;
            text-align: center;
            font-size: 1.6rem;
            font-weight: bold;
            border-radius: 0 0 22px 22px;
            margin-bottom: 25px;
            letter-spacing: 1px;
        }
        .consult-card {
            border-radius: 16px;
            border: 0;
            background: #f5f6fa;
            box-shadow: 0 1px 10px #23305422;
        }
        .consult-label {
            font-weight: bold; color: #233054; min-width: 120px; display: inline-block;
        }
        .consult-icon {
            width: 1.3em; height: 1.3em; vertical-align: middle; margin-left: 7px; opacity: .75;
        }
        .consult-value {color:#3c4452;}
        .form-label {font-weight: bold;}
        .lawyer-stats-info .alert {
            background: linear-gradient(100deg,#e3f0ff 60%,#fff 100%);
            color: #164173;
            font-weight: 500;
            border: 0;
            box-shadow: 0 2px 14px #23305418;
            border-radius: 1.7em;
            max-width: 380px;
            margin: 10px auto 0 auto;
            font-size: 1.07em;
            direction: rtl;
        }
        .lawyer-stats-info .badge.bg-primary {
            background-color: #247bcb !important;
        }
        .card-summary-alert {
            background: linear-gradient(100deg,#fff8db 60%,#fff 100%);
            color: #5f5102;
            border: 0;
            box-shadow: 0 2px 10px #f7c87330;
            font-weight: bold;
            font-size: 1em;
            margin: 0 0 14px 0;
        }
        .highlight-bg {
            background: #f7c87333;
            border-radius: 7px;
            padding: 6px 12px;
            display: inline-block;
        }
    </style>
</head>
<body>
<?php include 'includes/navigation.php'; ?>
<div class="dashboard-header">
    تعيين محامي للاستشارة
</div>
<div class="container my-5">
    <div class="main-card">

        <!-- إحصائية سريعة في الأعلى -->
        <div class="alert card-summary-alert text-center mb-4 shadow-sm">
            مجموع الاستشارات المحالة لجميع المحامين: <b><?= $totalAssigned ?></b>
        </div>

        <?php if ($assign_error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($assign_error) ?></div>
        <?php endif; ?>

        <!-- بطاقة الاستشارة بشكل متناسق واحترافي -->
        <div class="card consult-card mb-4 shadow-sm">
            <div class="card-body">
                <div class="mb-2">
                    <span class="consult-label highlight-bg"><i class="bi bi-hash consult-icon text-info"></i>رقم الاستشارة:</span>
                    <span class="consult-value"><?= $consult['id'] ?></span>
                </div>
                <div class="mb-2">
                    <span class="consult-label"><i class="bi bi-file-earmark-text consult-icon text-primary"></i>الموضوع:</span>
                    <span class="consult-value"><?= htmlspecialchars($consult['subject']) ?></span>
                </div>
                <div class="mb-2">
                    <span class="consult-label"><i class="bi bi-exclamation-circle consult-icon text-warning"></i>الحالة:</span>
                    <span class="consult-value"><?= statusLabel($consult['status']) ?></span>
                </div>
                <div class="mb-2">
                    <span class="consult-label"><i class="bi bi-person-badge consult-icon text-success"></i>محامي معين حالياً:</span>
                    <span class="consult-value">
                    <?php
                    if (!empty($consult['lawyer_id'])) {
                        $lawyer_stmt = $db->prepare("SELECT name FROM lawyers WHERE id = ?");
                        $lawyer_stmt->execute([$consult['lawyer_id']]);
                        $l = $lawyer_stmt->fetch(PDO::FETCH_ASSOC);
                        echo $l ? htmlspecialchars(lawyerDisplayName($l)) : '<span class="text-danger">محذوف</span>';
                    } else {
                        echo '<span class="text-muted">لا يوجد بعد</span>';
                    }
                    ?>
                    </span>
                </div>
                <?php if(!empty($consult['message'])): ?>
                <div class="mb-2">
                    <span class="consult-label"><i class="bi bi-chat-left-text consult-icon text-secondary"></i>التفاصيل:</span>
                    <span class="consult-value"><?= nl2br(htmlspecialchars($consult['message'])) ?></span>
                </div>
                <?php endif; ?>
                <?php if(!empty($consult['attachment'])): ?>
                <div class="mb-2">
                    <span class="consult-label"><i class="bi bi-paperclip consult-icon text-danger"></i>مرفق:</span>
                    <a href="uploads/<?= htmlspecialchars($consult['attachment']) ?>" target="_blank" class="btn btn-sm btn-outline-primary ms-2">تحميل المرفق</a>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- نموذج اختيار المحامي مع عرض عدد الاستشارات لهذا المحامي -->
        <form method="post">
            <div class="mb-3">
                <label for="lawyer_id" class="form-label">اختر المحامي المناسب:</label>
                <select name="lawyer_id" id="lawyer_id" class="form-select" required <?= $disable_assign ? 'disabled' : '' ?>>
                    <option value="">-- اختر محامي --</option>
                    <?php foreach ($lawyers as $l): ?>
                        <option value="<?= $l['id'] ?>" <?= ($consult['lawyer_id']==$l['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars(lawyerDisplayName($l)) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="lawyer-stats-info" id="lawyerStatsInfo" style="display:none;">
                <!-- سيتم تعبئتها بالجافا سكريبت -->
            </div>
            <?php if ($disable_assign): ?>
                <div class="alert alert-warning text-center">لا يمكن تعيين محامي لاستشارة مغلقة أو تم الرد عليها.</div>
            <?php endif; ?>
            <div class="mt-4 text-center">
                <button type="submit" class="btn btn-success px-4" <?= $disable_assign ? 'disabled' : '' ?>>
                    <i class="bi bi-person-check"></i> تعيين المحامي
                </button>
                <a href="admin_dashboard.php?tab=consultations" class="btn btn-secondary ms-2"><i class="bi bi-x"></i> إلغاء</a>
            </div>
        </form>
    </div>
</div>
<script>
    // عدد الاستشارات لكل محامي من الـPHP
    var lawyerStats = <?= json_encode($lawyerStats) ?>;
    document.addEventListener('DOMContentLoaded', function () {
        var select = document.getElementById('lawyer_id');
        var statsInfo = document.getElementById('lawyerStatsInfo');

        function updateLawyerStatsInfo() {
            var lawyerId = select.value;
            if (!lawyerId) {
                statsInfo.style.display = 'none';
                statsInfo.innerHTML = '';
                return;
            }
            var count = lawyerStats[lawyerId] ? lawyerStats[lawyerId] : 0;
            statsInfo.innerHTML = `
                <div class="alert alert-info d-flex align-items-center justify-content-center gap-2 shadow-sm my-2">
                    <i class="bi bi-bar-chart-steps fs-4 text-primary"></i>
                    <span>
                        هذا المحامي لديه
                        <span class="badge rounded-pill bg-primary fs-6 px-3 py-2 mx-1">${count}</span>
                        استشارة محالة إليه
                    </span>
                </div>
            `;
            statsInfo.style.display = 'block';
        }

        select.addEventListener('change', updateLawyerStatsInfo);
        // تحديث المعلومة فور فتح الصفحة إذا كان هناك محامي محدد مسبقًا
        updateLawyerStatsInfo();
    });
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

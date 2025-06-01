<?php
session_start();
require_once 'includes/db_connect.php';

$user_id   = $_SESSION['user_id'] ?? 0;
$user_role = $_SESSION['user_role'] ?? '';
$consultation_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($consultation_id <= 0) {
    die("<div class='alert alert-danger text-center mt-5'>رقم الاستشارة غير صالح.</div>");
}

// جلب الاستشارة من قاعدة البيانات
$stmt = $db->prepare("SELECT * FROM consultations WHERE id = ?");
$stmt->execute([$consultation_id]);
$consult = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$consult) {
    die("<div class='alert alert-danger text-center mt-5'>الاستشارة غير موجودة.</div>");
}

// جلب اسم المحامي إن وجد
$lawyer_name = '';
if (!empty($consult['lawyer_id'])) {
    $lawyer_stmt = $db->prepare("SELECT name, full_name FROM lawyers WHERE id = ?");
    $lawyer_stmt->execute([$consult['lawyer_id']]);
    $l = $lawyer_stmt->fetch(PDO::FETCH_ASSOC);
    if ($l) {
        $lawyer_name = !empty($l['full_name']) ? $l['full_name'] : $l['name'];
    }
}

// تحقق صلاحية العرض
$allowed = false;
if ($consult['user_id'] == $user_id) $allowed = true;
if ($user_role === 'lawyer' && isset($consult['lawyer_id']) && $consult['lawyer_id'] == $user_id) $allowed = true;
if (in_array($user_role, ['admin', 'super_admin', 'manager'])) $allowed = true;

if (!$allowed) {
    header("Location: login.php");
    exit;
}

// =======================
// 1. إغلاق الاستشارة مع تسجيل النشاط
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['close_consultation'])) {
    if (
        (($user_role === 'client' || $user_role === 'citizen') && $consult['user_id'] == $user_id) ||
        in_array($user_role, ['admin', 'super_admin', 'manager'])
    ) {
        if ($consult && $consult['status'] != 'closed' && $consult['status'] != 'completed') {
            $stmt = $db->prepare("UPDATE consultations SET status = 'closed' WHERE id = ?");
            $stmt->execute([$consultation_id]);

            // سجل النشاط
            $logStmt = $db->prepare("INSERT INTO activity_log (user_id, username, role, action_type, action_desc, created_at, ip_address) VALUES (?, ?, ?, ?, ?, NOW(), ?)");
            $logStmt->execute([
                $user_id,
                $_SESSION['user_name'] ?? 'غير معروف',
                $user_role,
                'close_consultation',
                "تم إغلاق الاستشارة رقم $consultation_id",
                $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0'
            ]);

            header("Location: view_consultation.php?id=".$consultation_id);
            exit;
        }
    }
}

// =======================
// 2. تعيين محامي (لو كان هناك زر تعيين محامي هنا)
// للتحقق فقط، إذا تريد تضمينه هنا فقط، يمكنك إضافة هذا الجزء إذا لديك زر تعيين محامي في نفس الصفحة

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['assign_lawyer_id'])) {
    $lawyer_id = intval($_POST['assign_lawyer_id']);
    // تحقق صلاحية التعيين (مشرف، مدير، مدير أعلى)
    if (in_array($user_role, ['admin', 'super_admin', 'manager'])) {
        if ($consult['lawyer_id'] != $lawyer_id) {
            // تحقق من وجود المحامي
            $stmt = $db->prepare("SELECT id FROM lawyers WHERE id = ?");
            $stmt->execute([$lawyer_id]);
            $lawyer_exists = $stmt->fetchColumn();

            if ($lawyer_exists) {
                // تحديث تعيين المحامي
                $stmt = $db->prepare("UPDATE consultations SET lawyer_id = ? WHERE id = ?");
                $stmt->execute([$lawyer_id, $consultation_id]);

                // سجل النشاط في الأرشيف
                $logStmt = $db->prepare("INSERT INTO activity_log (user_id, username, role, action_type, action_desc, created_at, ip_address) VALUES (?, ?, ?, ?, ?, NOW(), ?)");
                $logStmt->execute([
                    $user_id,
                    $_SESSION['user_name'] ?? 'غير معروف',
                    $user_role,
                    'assign_lawyer',
                    "تم تعيين المحامي رقم $lawyer_id للاستشارة رقم $consultation_id",
                    $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0'
                ]);

                header("Location: view_consultation.php?id=".$consultation_id."&assigned=1");
                exit;
            }
        }
    }
}

// =======================
// 3. الرد على الاستشارة مع تسجيل النشاط

// جلب الردود على الاستشارة
$stmt = $db->prepare("SELECT r.*, u.name AS lawyer_name, u.full_name AS lawyer_full_name FROM replies r LEFT JOIN users u ON r.lawyer_id = u.id WHERE r.consultation_id = ? ORDER BY r.created_at ASC");
$stmt->execute([$consultation_id]);
$replies = $stmt->fetchAll(PDO::FETCH_ASSOC);

// من هو آخر من رد؟
$last_reply_by = '';
if (!empty($replies)) {
    $last = end($replies);
    if ($last['lawyer_id']) {
        $last_reply_by = 'lawyer';
    } elseif ($last['user_id'] ?? false) {
        $last_reply_by = 'client';
    } elseif ($last['manager_id'] ?? false) {
        $last_reply_by = 'manager';
    }
}

// من يستطيع الرد الآن؟
$can_reply = false;
if ($consult['status'] != 'closed' && $consult['status'] != 'completed') {
    if ($user_role === 'manager' || $user_role === 'super_admin') {
        $can_reply = true;
    } elseif ($user_role === 'lawyer' && isset($consult['lawyer_id']) && $consult['lawyer_id'] == $user_id) {
        $can_reply = true;
    } elseif (($user_role === 'client' || $user_role === 'citizen') && $consult['user_id'] == $user_id) {
        if ($last_reply_by == 'lawyer' || $last_reply_by == 'manager') {
            $can_reply = true;
        } elseif (empty($replies)) {
            $can_reply = true;
        }
    }
}

$reply_error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reply_submit']) && $can_reply) {
    $reply_text = trim($_POST['reply_text'] ?? '');
    if (empty($reply_text)) {
        $reply_error = "يرجى إدخال نص الرد.";
    } elseif ($consult['status'] == 'closed' || $consult['status'] == 'completed') {
        $reply_error = "لا يمكن الرد على استشارة مغلقة أو مكتملة.";
    } else {
        $attachmentName = null;
        if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($_FILES['attachment']['name'], PATHINFO_EXTENSION));
            $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf'];
            if (in_array($ext, $allowedTypes)) {
                $uploadDir = 'uploads/';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
                $originalName = basename($_FILES['attachment']['name']);
                $attachmentName = time() . '_' . preg_replace("/[^a-zA-Z0-9.\-_]/", "_", $originalName);
                $targetPath = $uploadDir . $attachmentName;
                move_uploaded_file($_FILES['attachment']['tmp_name'], $targetPath);
            }
        }
        $lawyer_id = ($user_role === 'lawyer' && isset($consult['lawyer_id']) && $consult['lawyer_id'] == $user_id) ? $user_id : null;
        $manager_id = ($user_role === 'manager' || $user_role === 'super_admin') ? $user_id : null;
        $stmt = $db->prepare("INSERT INTO replies (consultation_id, reply_text, created_at, attachment, lawyer_id, manager_id, user_id) VALUES (?, ?, NOW(), ?, ?, ?, ?)");
        $stmt->execute([
            $consultation_id, $reply_text, $attachmentName,
            $lawyer_id, $manager_id, ($user_role === 'client' || $user_role === 'citizen') ? $user_id : null
        ]);

        // سجل النشاط عند الرد
        $lastReplyId = $db->lastInsertId();
        $logStmt = $db->prepare("INSERT INTO activity_log (user_id, username, role, action_type, action_desc, created_at, ip_address) VALUES (?, ?, ?, ?, ?, NOW(), ?)");
        $logStmt->execute([
            $user_id,
            $_SESSION['user_name'] ?? 'غير معروف',
            $user_role,
            'reply_sent',
            "تم إرسال رد رقم $lastReplyId على الاستشارة رقم $consultation_id",
            $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0'
        ]);

        header("Location: view_consultation.php?id=".$consultation_id);
        exit;
    }
}

// دالة عرض حالة الاستشارة
function statusLabel($status) {
    switch($status) {
        case 'pending':   return '<span class="badge bg-warning text-dark">قيد المراجعة</span>';
        case 'in_progress': return '<span class="badge bg-info text-dark">قيد التنفيذ</span>';
        case 'completed': return '<span class="badge bg-success">تم الرد</span>';
        case 'rejected':  return '<span class="badge bg-danger">مرفوضة</span>';
        case 'closed':    return '<span class="badge bg-secondary">مغلقة</span>';
        default:          return '<span class="badge bg-secondary">'.htmlspecialchars($status).'</span>';
    }
}

// زر العودة الذكي (يرجعك إلى تبويب الاستشارات ومكان الاستشارة نفسها)
if (in_array($user_role, ['admin', 'super_admin'])) {
    $back_url = 'admin_dashboard.php?tab=consultations#consultation-' . $consult['id'];
} elseif ($user_role === 'manager') {
    $back_url = 'manager_dashboard.php?tab=consultations#consultation-' . $consult['id'];
} elseif ($user_role === 'lawyer') {
    $back_url = 'lawyer_dashboard.php?tab=consultations#consultation-' . $consult['id'];
} else {
    $back_url = 'client_dashboard.php?tab=consultations#consultation-' . $consult['id'];
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>معاينة الاستشارة - حقك تعرف</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        body {background: #eaf0fa;}
        .main-card {
            background: linear-gradient(135deg, #f7c873 25%, #ffe6a2 100%);
            border-radius: 16px;
            box-shadow: 0 4px 24px #11213a24;
            padding: 34px 30px 32px 30px;
            max-width: 750px;
            margin: auto;
        }
        .dashboard-header {
            background: linear-gradient(90deg, #11213a 70%, #f7c873 110%);
            color: #fffbe5;
            padding: 24px 0 18px 0;
            text-align: center;
            font-size: 1.6rem;
            font-weight: bold;
            border-radius: 0 0 25px 25px;
            margin-bottom: 27px;
            letter-spacing: 1px;
            box-shadow: 0 2px 10px #11213a25;
        }
        .consult-card-box {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 1px 10px #23305422;
            padding: 25px 22px 16px 22px;
            margin-bottom: 18px;
        }
        .consult-label {
            font-weight: bold; color: #233054; min-width: 130px; display: inline-block;
            font-size: 1.13em;
        }
        .consult-icon {
            width: 1.25em; height: 1.25em; vertical-align: middle; margin-left: 7px; opacity: .8;
        }
        .consult-value { color: #3c4452; font-size: 1.12em;}
        .reply-bubble {border-radius: 14px; padding: 12px 18px; margin-bottom: 14px;}
        .reply-client {background: #e8f7ff; border: 1px solid #b5daef;}
        .reply-lawyer {background: #fff4ec; border: 1px solid #f2c8a6;}
        .reply-manager {background: #f3f5e5; border: 1px solid #c5d59c;}
        .btn-custom {
            background: linear-gradient(90deg, #233054 80%, #f7c873 120%);
            color: #fffbe7;
            font-weight: bold;
            border-radius: 14px;
            padding: 7px 22px;
            border: 0;
            box-shadow: 0 2px 9px #23305430;
            transition: all .17s;
        }
        .btn-custom:hover {
            background: linear-gradient(90deg, #f7c873 70%, #233054 130%);
            color: #233054;
        }
        .btn-close-consult {
            background: linear-gradient(90deg,#fe6e6e 60%,#fbcfaa 120%);
            color: #fff;
            border-radius: 11px;
            padding: 7px 20px;
            border: none;
            font-weight: bold;
            margin-bottom: 6px;
            box-shadow: 0 2px 7px #b8323231;
        }
        .btn-close-consult:hover {
            background: linear-gradient(90deg,#fbcfaa 60%,#fe6e6e 120%);
            color: #b83232;
        }
        .form-control, .form-select {
            border-radius: 10px;
        }
        .alert-secondary {
            background: #faf7ef;
        }
        .reply-bubble .btn { margin-right: 5px;}
        @media (max-width: 650px) {
            .main-card { padding: 6px;}
            .consult-card-box {padding: 7px;}
        }
    </style>
</head>
<body>
<?php include 'includes/navigation.php'; ?>
<div class="dashboard-header">
    معاينة الاستشارة القانونية
</div>
<div class="container my-5">
    <div class="main-card">
        <div class="consult-card-box">
            <h5 class="mb-3"><i class="bi bi-file-earmark-text"></i> الموضوع: <?= htmlspecialchars($consult['subject']) ?></h5>
            <p><span class="consult-label"><i class="bi bi-calendar consult-icon text-secondary"></i>تاريخ الإرسال:</span>
                <span class="consult-value"><?= htmlspecialchars($consult['created_at']) ?></span>
            </p>
            <p><span class="consult-label"><i class="bi bi-exclamation-circle consult-icon text-warning"></i>الحالة:</span>
                <span class="consult-value"><?= statusLabel($consult['status']) ?></span>
            </p>
            <?php if ($lawyer_name): ?>
            <p><span class="consult-label"><i class="bi bi-person-badge consult-icon text-success"></i>المحامي المعيّن:</span>
                <span class="consult-value"><?= htmlspecialchars($lawyer_name) ?></span>
            </p>
            <?php endif; ?>
            <p><span class="consult-label"><i class="bi bi-chat-left-text consult-icon text-secondary"></i>تفاصيل الاستشارة:</span></p>
            <div class="alert alert-secondary"><?= nl2br(htmlspecialchars($consult['message'])) ?></div>
            <?php if (!empty($consult['attachment'])): ?>
                <p>
                    <span class="consult-label"><i class="bi bi-paperclip consult-icon text-danger"></i>المرفق:</span>
                    <a href="<?= 'uploads/' . htmlspecialchars($consult['attachment']) ?>" target="_blank" class="btn btn-sm btn-outline-primary ms-2" download>تحميل المرفق</a>
                </p>
            <?php endif; ?>
        </div>
        <div class="mb-4">
            <?php if ($can_reply): ?>
                <form method="post" enctype="multipart/form-data" class="row g-2 align-items-end">
                    <div class="col-9 col-md-7">
                        <textarea name="reply_text" class="form-control" rows="2" placeholder="اكتب ردك..." required></textarea>
                    </div>
                    <div class="col-3 col-md-3">
                        <input type="file" name="attachment" class="form-control" accept=".jpg,.jpeg,.png,.pdf,.gif,.webp">
                    </div>
                    <div class="col-12 col-md-2">
                        <button type="submit" name="reply_submit" class="btn btn-custom"><i class="bi bi-reply"></i> رد</button>
                    </div>
                </form>
                <?php if($reply_error): ?>
                    <div class="alert alert-danger mt-2"><?= htmlspecialchars($reply_error) ?></div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
        <?php if ($consult['status'] != 'closed' && $consult['status'] != 'completed'): ?>
        <div class="mb-4">
            <?php if (
                (($user_role === 'client' || $user_role === 'citizen') && $consult['user_id'] == $user_id) ||
                in_array($user_role, ['admin', 'super_admin', 'manager'])
            ): ?>
            <form method="post" class="d-inline">
                <input type="hidden" name="close_consultation" value="1">
                <button type="submit" class="btn btn-close-consult">
                    <i class="bi bi-x-octagon"></i> غلق الاستشارة
                </button>
            </form>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <hr>
        <h6 class="mb-3">الردود على الاستشارة:</h6>
        <?php if (empty($replies)): ?>
            <div class="alert alert-info">لا توجد ردود حتى الآن.</div>
        <?php else: foreach ($replies as $rep): ?>
            <?php
            $who = "رد مواطن";
            $reply_class = "reply-client";
            if (!empty($rep['lawyer_id'])) {
                $who = "رد المحامي: " . htmlspecialchars($rep['lawyer_full_name'] ?: $rep['lawyer_name']);
                $reply_class = "reply-lawyer";
            }
            if (!empty($rep['manager_id'])) {
                $who = "رد المدير";
                $reply_class = "reply-manager";
            }
            ?>
            <div class="reply-bubble <?= $reply_class ?>">
                <div class="small mb-1 text-muted">
                    <?= $who ?>
                    <span class="ms-3"><?= htmlspecialchars($rep['created_at']) ?></span>
                </div>
                <div><?= nl2br(htmlspecialchars($rep['reply_text'])) ?></div>
                <?php if (!empty($rep['attachment'])): ?>
                    <div class="mt-2">
                        <a href="uploads/<?= htmlspecialchars($rep['attachment']) ?>" target="_blank" class="btn btn-sm btn-outline-secondary" download>تحميل مرفق الرد</a>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; endif; ?>

        <div class="mt-4 text-center">
            <a href="<?= $back_url ?>" class="btn btn-custom"><i class="bi bi-arrow-90deg-right"></i> العودة</a>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

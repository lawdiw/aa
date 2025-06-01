<?php
session_start();
require_once 'includes/db_connect.php';

// التأكد من وجود جلسة وتحديد الدور
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role'])) {
    header("Location: login.php");
    exit;
}

$user_id   = $_SESSION['user_id'];
$user_role = $_SESSION['user_role'];
$user_name = $_SESSION['user_name'] ?? '';

// توحيد دور "citizen" و"client" ليستخدمان نفس لوحة المواطن
if ($user_role === 'citizen') $user_role = 'client';

// ممنوع التلاعب: الدور المسموح فقط ما هو محفوظ بالجَلسة
$tab = $user_role;

// التبويب الفرعي المطلوب، وإلا الأول افتراضيًا
$sub = $_GET['sub'] ?? '';

// مصفوفة التبويبات لكل دور (أضف أو احذف حسب ما يناسب مشروعك)
$tabs = [
    'client' => [
        'consultations'    => ['الاستشارات القانونية', 'bi bi-chat-left-dots'],
        'messages'         => ['الرسائل', 'bi bi-envelope'],
        'courses'          => ['الدورات والورش', 'bi bi-easel2'],
        'balance'          => ['الرصيد', 'bi bi-wallet2'],
    ],
    'lawyer' => [
        'consultations'    => ['استشاراتي القانونية', 'bi bi-chat-left-dots'],
        'messages'         => ['الرسائل', 'bi bi-envelope'],
        'courses'          => ['الدورات والورش', 'bi bi-easel2'],
        'balance'          => ['الرصيد', 'bi bi-wallet2'],
    ],
    'manager' => [
        'stats'            => ['الإحصائيات', 'bi bi-graph-up'],
        'lawyers'          => ['المحامون', 'bi bi-person-badge'],
        'clients'          => ['المواطنون', 'bi bi-person'],
        'consultations'    => ['كل الاستشارات', 'bi bi-chat-left-dots'],
        'messages'         => ['الرسائل والشكاوى', 'bi bi-envelope'],
        'users'            => ['المستخدمون', 'bi bi-people'],
        'courses'          => ['الدورات والورش', 'bi bi-easel2'],
        'logs'             => ['أرشيف العمليات', 'bi bi-archive'],
        'roles'            => ['المالية والإدارية', 'bi bi-cash-coin'],
        'reports'          => ['التقارير', 'bi bi-bar-chart'],
        'settings'         => ['الإعدادات', 'bi bi-gear'],
    ],
    'admin' => [
        'consultations'    => ['الاستشارات', 'bi bi-chat-left-dots'],
        'users'            => ['المستخدمون', 'bi bi-people'],
        'lawyers'          => ['المحامون', 'bi bi-person-badge'],
        'clients'          => ['المواطنون', 'bi bi-person'],
        'messages'         => ['الرسائل', 'bi bi-envelope'],
        'courses'          => ['الدورات والورش', 'bi bi-easel2'],
        'reports'          => ['التقارير', 'bi bi-file-earmark-text'],
    ],
    'superadmin' => [
	    'stats'            => ['الإحصائيات', 'bi bi-graph-up'],
        'consultations'    => ['الاستشارات', 'bi bi-chat-left-dots'],
        'users'            => ['كل المستخدمين', 'bi bi-people'],
        'lawyers'          => ['المحامون', 'bi bi-person-badge'],
        'clients'          => ['المواطنون', 'bi bi-person'],
        'courses'          => ['الدورات والورش', 'bi bi-easel2'],
        'system'           => ['إعدادات النظام', 'bi bi-gear'],
        'logs'             => ['سجل النظام', 'bi bi-archive'],
        'reports'          => ['التقارير', 'bi bi-bar-chart'],
        'roles'            => ['المالية والإدارية', 'bi bi-cash-coin'],
        'settings'         => ['الإعدادات', 'bi bi-gear'],
    ],
];

// لو الدور غير معروف (دخول غير شرعي) يتم تسجيل الخروج
if (!isset($tabs[$tab])) {
    session_destroy();
    header("Location: login.php");
    exit;
}

// التبويب الفرعي الافتراضي (دائمًا أول عنصر)
if (!$sub || !isset($tabs[$tab][$sub])) {
    $sub = array_key_first($tabs[$tab]);
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>لوحة التحكم - حقك تعرف</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        body { background: #f7fafd; }
        .dashboard-header { background: #11213a; color: #f7c873; padding: 22px 0; text-align: center; font-size: 1.5rem; font-weight: bold; }
        .main-card { background: #fff; border-radius: 14px; box-shadow: 0 2px 14px #11213a15; padding: 28px; }
        .custom-tabs .nav-link {
            font-size: 1.18rem; font-weight: bold; color: #11213a !important; background: #fff !important;
            border-radius: 1.7rem 1.7rem 0 0 !important; margin-left: 6px; border: 2px solid #f7c873 !important;
            transition: all 0.2s; padding: 0.75rem 2rem 0.75rem 1rem; box-shadow: 0 1px 8px #11213a18;
            display: flex; align-items: center; gap: 0.7rem;
        }
        .custom-tabs .nav-link.active, .custom-tabs .nav-link:focus {
            background: linear-gradient(100deg, #f7c873 75%, #fff 100%) !important;
            color: #11213a !important;
            border-bottom: 4px solid #11213a !important;
            font-size: 1.24rem;
            box-shadow: 0 2px 16px #11213a18;
        }
        .custom-tabs .nav-link .tab-icon { font-size: 1.4em; margin-left: 0.5em; vertical-align: middle; }
        .custom-tabs .nav-link .tab-title { vertical-align: middle; }
        @media (max-width: 700px) {
            .custom-tabs .nav-link { font-size: 1rem; padding: 0.7rem 1.1rem; }
            .custom-tabs .nav-link .tab-icon { font-size: 1.1em; }
        }
    </style>
</head>
<body>
<?php include 'includes/navigation.php'; ?>
<div class="dashboard-header">
    مرحباً <?= htmlspecialchars($user_name) ?> في لوحة التحكم (<span class="text-warning"><?= htmlspecialchars($user_role) ?></span>)
</div>
<div class="container my-5">
    <!-- التبويبات -->
    <ul class="nav nav-tabs mb-4 custom-tabs">
        <?php foreach($tabs[$tab] as $key => $tabdata): ?>
        <li class="nav-item">
            <a class="nav-link<?= ($sub==$key)?' active':'' ?>" href="dashboard.php?sub=<?= $key ?>">
                <span class="tab-icon"><i class="<?= $tabdata[1] ?>"></i></span>
                <span class="tab-title"><?= $tabdata[0] ?></span>
            </a>
        </li>
        <?php endforeach; ?>
    </ul>
    <div class="main-card">
        <?php
        // تحميل التبويب المناسب لكل دور
        $include_path = "dashboard_tabs/{$tab}/{$sub}.php";
        if (file_exists($include_path)) {
            include $include_path;
        } else {
            echo "<div class='alert alert-warning'>لم يتم العثور على ملف التبويب المطلوب.<br>($include_path)</div>";
        }
        ?>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

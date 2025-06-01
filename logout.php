<?php
session_start();
require_once 'includes/db_connect.php';

function logActivity($db, $userId, $username, $role, $actionType, $actionDesc, $ipAddress = 'غير معروف') {
    $stmt = $db->prepare("INSERT INTO activity_log (user_id, username, role, action_type, action_desc, ip_address, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
    $stmt->execute([$userId, $username, $role, $actionType, $actionDesc, $ipAddress]);
}

if (isset($_SESSION['user_id'])) {
    $userId   = $_SESSION['user_id'];
    $username = $_SESSION['user_name'] ?? 'غير معروف';
    $role     = $_SESSION['user_role'] ?? 'غير محدد';
    $ipAddress = $_SERVER['REMOTE_ADDR'] ?? 'غير معروف';

    logActivity($db, $userId, $username, $role, 'logout', 'تم تسجيل الخروج', $ipAddress);
}

$_SESSION = [];
session_destroy();

header("Location: index.php");
exit;

<?php
// صفحة أرشيف العمليات activity_log.php

session_start();
require_once 'includes/db_connect.php';

// التحقق من الصلاحيات إن لزم

// جلب آخر 200 عملية من السجل
$stmt = $db->query("SELECT * FROM activity_log ORDER BY created_at DESC LIMIT 200");
$logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8" />
    <title>أرشيف العمليات</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet" />
    <style>
        body { background: #f6f8fa; }
        .main-card { background: #fff; border-radius: 14px; box-shadow: 0 2px 12px #0001; padding: 25px 12px; margin-top: 30px; }
        .table th, .table td { text-align: center; vertical-align: middle; }
        .table thead th { background: #2980b9; color: #fff; }
        .dashboard-title { font-size: 1.25rem; color: #2980b9; font-weight: bold; margin-bottom: 16px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="main-card">
            <div class="dashboard-title">أرشيف العمليات وسجل الأحداث</div>
            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>نوع العملية</th>
                            <th>الوصف</th>
                            <th>اسم المستخدم</th>
                            <th>الدور</th>
                            <th>IP</th>
                            <th>الحالة</th>
                            <th>التاريخ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($logs): ?>
                            <?php foreach($logs as $i => $log): ?>
                            <tr>
                                <td><?= $i+1 ?></td>
                                <td><?= htmlspecialchars($log['action_type']) ?></td>
                                <td><?= htmlspecialchars($log['action_desc']) ?></td>
                                <td><?= htmlspecialchars($log['username'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($log['role'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($log['ip_address'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($log['status'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($log['created_at']) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center text-muted">لا توجد عمليات مسجلة حاليًا.</td>
                            </tr>
                        <?php endif ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>

<?php
require_once 'includes/db_connect.php';

// جلب جميع الورشات/الدورات من جدول courses
$stmt = $db->query("SELECT * FROM courses ORDER BY date DESC");
$workshops = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>الدورات وورش العمل القانونية - حقك تعرف</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        body {
            background: #11213a;
            color: #fff;
            font-family: 'Cairo', 'Tajawal', Arial, sans-serif;
            min-height: 100vh;
        }
        .container-box {
            background: #192846;
            border-radius: 20px;
            padding: 2.5rem;
            max-width: 1100px;
            margin: 40px auto 40px auto;
            box-shadow: 0 6px 30px #0007;
        }
        h1, h2, .section-title {
            color: #f7c873;
            font-weight: bold;
            text-align: center;
            margin-bottom: 34px;
        }
        .workshop-card {
            background: linear-gradient(135deg, #203456, #11213a);
            border-radius: 18px;
            color: #fff;
            text-align: right;
            box-shadow: 0 8px 30px #0007;
            padding: 28px 20px 22px 20px;
            min-height: 210px;
            margin-bottom: 28px;
            transition: transform .15s, box-shadow .15s;
            position: relative;
            padding-top: 58px;
        }
        .workshop-card:hover {
            transform: translateY(-7px) scale(1.03);
            box-shadow: 0 18px 44px #000b;
        }
        .workshop-card h5 {
            font-size: 1.20rem;
            color: #f7c873;
            margin-bottom: 10px;
            font-weight: bold;
            letter-spacing: 1px;
            display: flex;
            align-items: center;
            gap: 7px;
        }
        .workshop-card p {
            color: #d6d6d6;
            font-size: 1.02rem;
            margin-bottom: 20px;
        }
        .btn-register {
            background: linear-gradient(90deg, #237cbf, #2ec5a3);
            border: none;
            border-radius: 8px;
            font-weight: bold;
            color: #fff;
            width: 100%;
            padding: 7px 0;
            font-size: 1.08rem;
            letter-spacing: 1px;
        }
        .btn-register:disabled,
        .btn-register[disabled] {
            background: #32416a;
            color: #bbb;
            cursor: not-allowed;
        }
        .coming-soon-strip {
            width: 100%;
            background: linear-gradient(90deg, #f7c873, #2ec5a3);
            color: #192846;
            text-align: center;
            font-weight: bold;
            font-size: 1.08rem;
            padding: 6px 0;
            border-radius: 15px 15px 0 0;
            position: absolute;
            top: 0;
            right: 0;
            left: 0;
            z-index: 2;
            box-shadow: 0 2px 8px #0002;
            letter-spacing: 2px;
        }
        @media (max-width: 700px) {
            .container-box { padding: 0.5rem; }
            .workshop-card { padding: 15px 6px 15px 6px; min-height: 190px; }
        }
    </style>
</head>
<body>
<?php include 'includes/navigation.php'; ?>

<div class="container-box">
    <h1>الدورات وورش العمل القانونية</h1>
    <div class="row">
        <?php if(count($workshops)): foreach ($workshops as $ws): ?>
            <div class="col-lg-4 col-md-6">
                <div class="workshop-card">
                    <?php
                        // شريط علوي للحالة
                        if($ws['status'] === 'upcoming') {
                            echo '<div class="coming-soon-strip">قريبًا جدًا</div>';
                        } elseif($ws['status'] === 'completed') {
                            echo '<div class="coming-soon-strip" style="background:#48bb78;color:#fff;">مكتملة</div>';
                        }
                    ?>
                    <h5>
                        <i class="bi <?= htmlspecialchars($ws['icon'] ?: 'bi-easel2') ?>"></i>
                        <?= htmlspecialchars($ws['title']) ?>
                    </h5>
                    <p><?= htmlspecialchars($ws['description']) ?></p>
                    <div style="margin-bottom:8px;">
                        <?php if(!empty($ws['instructor'])): ?>
                        <b>المحاضر:</b> <?= htmlspecialchars($ws['instructor']) ?><br>
                        <?php endif; ?>
                        <?php if(!empty($ws['date'])): ?>
                        <b>التاريخ:</b> <?= htmlspecialchars($ws['date']) ?>
                        <?php endif; ?>
                    </div>
                    <button class="btn btn-register" disabled>سيتاح التسجيل قريبًا</button>
                </div>
            </div>
        <?php endforeach; else: ?>
            <div class="alert alert-warning text-center" style="color:#192846; background:#f7c873; border-radius:12px;">
                لا توجد ورشات أو دورات متاحة حالياً.
            </div>
        <?php endif; ?>
    </div>
</div>
</body>
</html>

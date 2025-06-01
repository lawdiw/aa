<?php
session_start();
// تحديد نوع المستخدم من الجلسة (تم التعديل هنا)
$userType = $_SESSION['user_role'] ?? 'guest';

// تحديد مسار لوحة التحكم حسب نوع المستخدم
$dashboardUrl = 'login.php'; // الافتراضي لمن لم يسجل دخول
if ($userType === 'lawyer') {
    $dashboardUrl = 'lawyer_dashboard.php';
} elseif ($userType === 'client' || $userType === 'citizen') {
    $dashboardUrl = 'client_dashboard.php';
} elseif ($userType === 'admin') {
    $dashboardUrl = 'admin_dashboard.php';
} elseif ($userType === 'supervisor') {
    $dashboardUrl = 'supervisor_dashboard.php';
}
// أضف أنواع حسابات أخرى إذا وجدت في نظامك
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>خدماتنا - حقك تعرف</title>
  <link href="https://fonts.googleapis.com/css2?family=Cairo&family=Tajawal&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" />
  <link rel="stylesheet" href="css/style.css" />
  <style>
    body {
      background-color: #11213a;
      color: #fff;
      font-family: 'Cairo', 'Tajawal', sans-serif;
    }
    .container-box {
      background-color: #192846;
      border-radius: 20px;
      padding: 2.5rem;
      max-width: 1200px;
      margin: 40px auto;
      box-shadow: 0 6px 30px #0006;
    }
    h1, h2 {
      color: #f7c873;
      font-weight: bold;
      text-align: center;
      margin-bottom: 30px;
      letter-spacing: 1px;
    }
    .service-card {
      display: block;
      background: linear-gradient(135deg, #203456, #11213a);
      border-radius: 18px;
      color: #fff;
      text-decoration: none;
      box-shadow: 0 8px 30px #0007;
      text-align: center;
      padding: 30px 22px 26px 22px;
      margin-bottom: 24px;
      transition: transform .15s, box-shadow .15s, background .2s;
      min-height: 290px;
    }
    .service-card:hover {
      transform: translateY(-7px) scale(1.035);
      background: linear-gradient(135deg, #237cbf 60%, #2ec5a3 100%);
      color: #fff;
      box-shadow: 0 16px 40px #000a;
      text-decoration: none;
    }
    .service-card i {
      font-size: 2.7rem;
      color: #f7c873;
      margin-bottom: 18px;
      display: block;
    }
    .service-card h5 {
      font-weight: bold;
      margin-bottom: 11px;
      color: #f7c873;
      font-size: 1.23rem;
    }
    .service-card p {
      color: #fff;
      font-size: 1rem;
      min-height: 44px;
    }
    @media (max-width: 991px) {
      .service-card { min-height: 330px; }
    }
    @media (max-width: 767px) {
      .container-box { padding: 1rem; }
      .service-card { min-height: 220px; }
    }
  </style>
</head>
<body>

<?php include 'includes/navigation.php'; ?>

<div class="container-box">
  <h1 class="mb-4">خدماتنا</h1>
  <div class="row g-4">
    <div class="col-lg-4 col-md-6">
      <a href="consultation.php" class="service-card">
        <i class="bi bi-chat-dots"></i>
        <h5>الاستشارات القانونية</h5>
        <p>إمكانية طلب الاستشارة أونلاين من نخبة محامين معتمدين، مع متابعة حالة الاستشارة والرد عليها بشكل فوري وآمن.</p>
      </a>
    </div>
    <div class="col-lg-4 col-md-6">
      <a href="laws_library.php" class="service-card">
        <i class="bi bi-journal-text"></i>
        <h5>مكتبة القوانين</h5>
        <p>الاطلاع على التشريعات العراقية واللوائح التنفيذية بسهولة عبر مكتبة منظمة ومحرك بحث ذكي.</p>
      </a>
    </div>
    <div class="col-lg-4 col-md-6">
      <a href="simulation.php" class="service-card">
        <i class="bi bi-mic"></i>
        <h5>محاكاة القضايا</h5>
        <p>محاكاة مواقف قانونية حقيقية مع إمكانية إدخال سيناريوهات واقعية للحصول على التوجيه الأمثل.</p>
      </a>
    </div>
    <div class="col-lg-4 col-md-6">
      <a href="lawyers.php" class="service-card">
        <i class="bi bi-person-lines-fill"></i>
        <h5>التواصل مع محامي</h5>
        <p>إمكانية التراسل مع محامي مختص بخصوص قضاياك عبر المنصة مباشرة بكل سرية وسهولة.</p>
      </a>
    </div>
    <div class="col-lg-4 col-md-6">
      <a href="faq.php" class="service-card">
        <i class="bi bi-question-circle"></i>
        <h5>الأسئلة الشائعة</h5>
        <p>أكثر الأسئلة القانونية تكرارًا وإجابات مبسطة عليها لزيادة وعي المستخدمين بحقوقهم.</p>
      </a>
    </div>
    <div class="col-lg-4 col-md-6">
      <!-- تم تعديل هنا فقط -->
      <a href="<?= $dashboardUrl ?>" class="service-card">
        <i class="bi bi-envelope-check"></i>
        <h5>متابعة الطلبات</h5>
        <p>متابعة حالة جميع الطلبات والاستشارات الخاصة بك، مع إشعارات عند كل تحديث.</p>
      </a>
    </div>
    <div class="col-lg-4 col-md-6">
      <a href="contracts.php" class="service-card">
        <i class="bi bi-file-earmark-medical"></i>
        <h5>نماذج قانونية</h5>
        <p>توفير نماذج عقود ووثائق قانونية مع إمكانية ملء البيانات وطباعتها مباشرة.</p>
      </a>
    </div>
    <div class="col-lg-4 col-md-6">
      <a href="fees_calculator.php" class="service-card">
        <i class="bi bi-currency-exchange"></i>
        <h5>احتساب الأتعاب والرسوم</h5>
        <p>حاسبة متكاملة لأتعاب المحامي ورسوم الدعاوى والتسجيل العقاري.</p>
      </a>
    </div>
    <div class="col-lg-4 col-md-6">
      <a href="workshops.php" class="service-card">
        <i class="bi bi-easel2"></i>
        <h5>دورات وورش تدريبية</h5>
        <p>ورش عمل ودورات تدريبية قانونية متخصصة مقدمة عبر المنصة لتعزيز خبرتك القانونية.</p>
      </a>
    </div>
  </div>
</div>

<footer class="py-4 mt-5" style="background: linear-gradient(135deg,#192846,#f7c873); color:#192846;">
  <div class="container text-center">
    © 2025 حقك تعرف. جميع الحقوق محفوظة.
  </div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
session_start();
require_once 'includes/db_connect.php';

$isLoggedIn = isset($_SESSION['user_id']);
$userRole = $_SESSION['user_role'] ?? '';
$userName = $_SESSION['user_name'] ?? 'زائر';

try {
    $userCount = $db->query("SELECT COUNT(*) FROM users")->fetchColumn();
    $consultCount = $db->query("SELECT COUNT(*) FROM consultations")->fetchColumn();
    $increaseRate = '30%';

    $stmt = $db->query("SELECT name, email, phone FROM lawyers ORDER BY id DESC LIMIT 6");
    $lawyers_limited = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $userCount = 0; $consultCount = 0; $increaseRate = '0%';
    $lawyers_limited = [];
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>حقك تعرف - الصفحة الرئيسية</title>

<link href="https://fonts.googleapis.com/css2?family=Cairo&family=Tajawal&display=swap" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" />

<style>
:root {
  --bg-main: #11213a;
  --box-bg: #192846;
  --primary-color: #f7c873;
  --text-light: #ffffff;
  --card-bg-gradient: linear-gradient(135deg, #203456, #11213a);
  --btn-bg: linear-gradient(90deg, #237cbf, #2ec5a3);
  --btn-bg-hover: linear-gradient(90deg, #1b658c, #238c72);
}
body {
  background-color: var(--bg-main);
  color: var(--text-light);
  font-family: 'Cairo', 'Tajawal', sans-serif;
  min-height: 100vh;
  margin: 0; padding: 0;
}
.container-box {
  background-color: var(--box-bg);
  border-radius: 20px;
  padding: 2.5rem;
  max-width: 1140px;
  margin: 40px auto 80px auto;
  box-shadow: 0 6px 30px #0006;
}
h1, h2, h3, .title {
  color: var(--primary-color);
  font-weight: bold;
  letter-spacing: 1px;
  text-align: center;
  margin-bottom: 24px;
}
/* باقي تنسيقات الصفحة */
.banner {
  background: url('images/banner.jpg') center/cover no-repeat;
  border-radius: 20px;
  box-shadow: 0 5px 18px #0009;
  padding: 50px 20px;
  margin-bottom: 40px;
}
.banner .container {
  background-color: rgba(0,0,0,0.6);
  border-radius: 20px;
  padding: 40px 20px;
}
.banner h1 {
  font-size: 2.8rem;
  color: var(--primary-color);
  margin-bottom: 20px;
  font-weight: 700;
}
.banner p {
  font-size: 1.25rem;
  color: var(--text-light);
  margin-bottom: 30px;
}
.btn-primary {
  background: var(--btn-bg);
  border: none;
  border-radius: 10px;
  padding: 12px 36px;
  font-size: 1.15rem;
  color: var(--text-light);
  transition: background 0.3s ease;
}
.btn-primary:hover {
  background: var(--btn-bg-hover);
  color: var(--text-light);
}
.card-custom {
  background: var(--card-bg-gradient);
  border-radius: 15px;
  box-shadow: 0 10px 30px #0008;
  color: var(--text-light);
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  padding: 20px;
  height: 100%;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
}
.card-custom:hover {
  transform: translateY(-7px);
  box-shadow: 0 20px 40px #000d;
}
.card-custom .bi {
  font-size: 3rem;
  color: var(--primary-color);
  margin-bottom: 15px;
  transition: color 0.3s ease, transform 0.3s ease;
}
.card-custom:hover .bi {
  color: #fff;
  transform: scale(1.15);
}
.card-custom h5 {
  font-weight: 600;
  font-size: 1.4rem;
  margin-bottom: 10px;
  color: var(--primary-color);
}
.card-custom p {
  flex-grow: 1;
  font-size: 1rem;
  color: var(--text-light);
  margin-bottom: 15px;
}
.card-custom a.btn-custom {
  background: var(--btn-bg);
  color: var(--text-light);
  font-weight: 600;
  border-radius: 30px;
  padding: 10px 25px;
  align-self: center;
  text-decoration: none;
  transition: background 0.3s ease;
}
.card-custom a.btn-custom:hover {
  background: var(--btn-bg-hover);
  color: var(--text-light);
}
.lawyers-section {
  margin-bottom: 50px;
}
.lawyer-card {
  background: var(--card-bg-gradient);
  border-radius: 15px;
  padding: 15px;
  box-shadow: 0 10px 30px #0008;
  color: var(--text-light);
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  height: 100%;
}
.lawyer-card:hover {
  transform: translateY(-7px);
  box-shadow: 0 20px 40px #000d;
}
.lawyer-card h6 {
  color: var(--primary-color);
  font-weight: 700;
}
.lawyer-contact {
  margin-top: 8px;
  font-size: 0.9rem;
  color: var(--text-light);
}
.lawyer-contact i {
  margin-left: 8px;
  color: var(--primary-color);
}
footer {
  background: linear-gradient(135deg, var(--box-bg), var(--primary-color));
  color: var(--primary-color);
  padding: 18px 0;
  margin-top: 40px;
  text-align: center;
  font-weight: 600;
  font-size: 1rem;
}
footer a {
  color: var(--primary-color);
  text-decoration: underline;
  transition: color 0.3s;
}
footer a:hover {
  color: var(--text-light);
}
.quick-contact {
  position: fixed;
  top: 50%;
  left: 0;
  transform: translateY(-50%);
  z-index: 9999;
  display: flex;
  flex-direction: column;
  gap: 12px;
}
.quick-contact a {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 44px;
  height: 44px;
  background: var(--box-bg);
  color: var(--primary-color);
  border-radius: 50%;
  font-size: 1.8rem;
  box-shadow: 0 3px 8px rgba(0,0,0,0.6);
  transition: background-color 0.3s, color 0.3s;
}
.quick-contact a:hover {
  background: var(--primary-color);
  color: var(--text-light);
}
</style>
</head>
<body>

<?php include 'includes/navigation.php'; ?>

<section class="banner text-center text-white py-5">
  <div class="container">
    <h1 class="display-4 fw-bold">أهلاً بك في حقك تعرف</h1>
    <p class="lead">منصة استشارات قانونية تفاعلية لمساعدتك في معرفة حقوقك واتخاذ الخطوات الصحيحة.</p>
    <a href="consultation.php" class="btn btn-primary btn-lg">اطلب استشارتك الآن</a>
  </div>
</section>

<div class="container-box">
  <!-- خدماتنا الرئيسية -->
  <section id="main-services" class="mb-5">
    <h2 class="section-title text-center">خدماتنا الرئيسية</h2>
    <div class="row g-4 mt-4">
      <?php
        $services = [
          ['icon' => 'chat-dots', 'title' => 'الاستشارة القانونية', 'text' => 'تواصل مع محامين معتمدين بكل سهولة وسرية.', 'link' => 'consultation.php', 'btn' => 'ابدأ الآن'],
          ['icon' => 'journal-text', 'title' => 'مكتبة القوانين', 'text' => 'اطلع على أحدث التشريعات بسهولة.', 'link' => 'laws_library.php', 'btn' => 'تصفح القوانين'],
          ['icon' => 'mic', 'title' => 'محاكاة القضايا', 'text' => 'تعلم كيفية التصرف في مواقف قانونية.', 'link' => 'simulation.php', 'btn' => 'ابدأ التجربة'],
          ['icon' => 'question-circle', 'title' => 'الأسئلة الشائعة', 'text' => 'تعرف على إجابات أكثر الأسئلة القانونية انتشارًا.', 'link' => 'faq.php', 'btn' => 'عرض الأسئلة']
        ];
        foreach ($services as $svc) {
          echo '<div class="col-md-3">';
          echo '<div class="card-custom">';
          echo "<div class='service-icon text-center mb-4'><i class='bi bi-{$svc['icon']}'></i></div>";
          echo "<h5>{$svc['title']}</h5>";
          echo "<p>{$svc['text']}</p>";
          echo "<a href='{$svc['link']}' class='btn-custom'>{$svc['btn']}</a>";
          echo '</div></div>';
        }
      ?>
    </div>
  </section>

  <!-- إحصائيات الموقع -->
  <section id="statistics" class="mb-5">
    <h2 class="section-title text-center">إحصائيات الموقع</h2>
    <div class="row g-4">
      <?php
        $statistics = [
          ['icon' => 'people', 'title' => 'المستخدمين النشطين', 'value' => $userCount, 'description' => 'عدد المستخدمين الذين يستخدمون الموقع حاليًا'],
          ['icon' => 'file-earmark-text', 'title' => 'الاستشارات المقدمة', 'value' => $consultCount, 'description' => 'عدد الاستشارات القانونية التي تم تقديمها'],
          ['icon' => 'graph-up-arrow', 'title' => 'زيادة في الاستشارات', 'value' => $increaseRate, 'description' => 'نسبة زيادة الاستشارات مقارنة بالشهر الماضي'],
        ];
        foreach ($statistics as $stat) {
          echo '<div class="col-md-4">';
          echo '<div class="card-custom">';
          echo "<div class='stat-icon text-center mb-4'><i class='bi bi-{$stat['icon']}'></i></div>";
          echo "<h5 class='stat-title text-center'>{$stat['value']}</h5>";
          echo "<p class='stat-description text-center'>{$stat['description']}</p>";
          echo "<a href='statistics.php' class='btn-custom'>اذهب</a>";
          echo '</div></div>';
        }
      ?>
    </div>
  </section>

  <!-- محامون مسجلون -->
  <section id="lawyers" class="lawyers-section">
    <h2 class="section-title text-center mb-4">محامون مسجلون</h2>
    <div class="row g-4">
      <?php foreach($lawyers_limited as $lawyer): ?>
        <div class="col-md-4">
          <div class="lawyer-card">
            <h6><?= htmlspecialchars($lawyer['name']) ?></h6>
            <p class="lawyer-contact"><i class="bi bi-envelope-fill"></i> <?= htmlspecialchars($lawyer['email']) ?></p>
            <p class="lawyer-contact"><i class="bi bi-telephone-fill"></i> <?= htmlspecialchars($lawyer['phone']) ?></p>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
    <div class="text-center mt-3">
      <a href="lawyers.php" class="btn btn-primary">عرض كل المحامين</a>
    </div>
  </section>

  <!-- روابط تهمك -->
<section id="important-links" class="important-links-section mb-5">
    <h2 class="section-title text-center">روابط تهمك</h2>
    <div class="row g-4">
      <?php
        $links = [
          ['url' => 'https://www.moj.gov.iq/', 'label' => 'وزارة العدل', 'icon' => 'gavel-fill'],
          ['url' => 'https://mof.gov.iq/', 'label' => 'وزارة المالية', 'icon' => 'cash-stack'],
          ['url' => 'https://www.mod.mil.iq/', 'label' => 'وزارة الدفاع', 'icon' => 'shield-lock-fill'],
          ['url' => 'https://moi.gov.iq/', 'label' => 'وزارة الداخلية', 'icon' => 'shield-fill-exclamation'],
          ['url' => 'https://www.moh.gov.iq/', 'label' => 'وزارة الصحة', 'icon' => 'heart-pulse-fill'],
          ['url' => 'https://mop.gov.iq/', 'label' => 'وزارة التخطيط', 'icon' => 'bar-chart-fill'],
          ['url' => 'http://www.molsa.gov.iq/', 'label' => 'وزارة العمل والشؤون الاجتماعية', 'icon' => 'people-fill'],
          ['url' => 'https://moc.gov.iq/', 'label' => 'وزارة الاتصالات', 'icon' => 'antenna'],
          ['url' => 'https://www.motrans.gov.iq/', 'label' => 'وزارة النقل', 'icon' => 'truck-flatbed'],
          ['url' => 'https://mot.gov.iq/', 'label' => 'وزارة النفط', 'icon' => 'droplet-fill'],
          ['url' => 'https://iraqcas.e-sjc-services.iq/', 'label' => 'محكمة التمييز', 'icon' => 'gavel-fill'],
          ['url' => 'https://lawyers.gov.iq/', 'label' => 'نقابة المحامين العراقيين', 'icon' => 'shield-lock-fill'],
          ['url' => 'https://sjc.iq/', 'label' => 'القضاء العراقي', 'icon' => 'balance-scale'],
        ];
        foreach ($links as $ln) {
          echo '<div class="col-md-3">';
          echo '<a href="' . htmlspecialchars($ln['url']) . '" target="_blank" class="card-custom d-block text-decoration-none">';
          echo '<div class="card-body d-flex align-items-center justify-content-center" style="min-height:70px;">';
          echo '<i class="bi bi-' . htmlspecialchars($ln['icon']) . ' me-3" style="font-size:2rem;"></i>';
          echo '<span class="fw-bold">' . htmlspecialchars($ln['label']) . '</span>';
          echo '</div></a></div>';
        }
      ?>
    </div>
</section>
<section id="about-us" class="mb-5">
  <h2 class="section-title text-center">من نحن؟</h2>
  <div class="row justify-content-center">
    <div class="col-md-10">
      <div class="card-custom p-4" style="min-height:140px;">
        <p style="font-size:1.15rem; text-align: justify; color:#fff;">
          <b>منصة "حقك تعرف"</b> هي مبادرة قانونية رقمية تُعنى بتقديم الاستشارات والخدمات القانونية للمواطنين العراقيين بجودة عالية واحترافية، من خلال التعاون مع <b>محامين معتمدين ومسجلين رسميًا لدى نقابة المحامين العراقيين</b>. نلتزم بتقديم المعلومة القانونية الصحيحة استنادًا إلى التشريعات العراقية النافذة، مع ضمان سرية المعلومات وحماية خصوصية المستخدمين. وتهدف المنصة إلى رفع الوعي القانوني، وتسهيل الوصول إلى العدالة، وتوفير مكتبة معرفية من القوانين والإجراءات الرسمية التي تهم كل مواطن وموظف ومحامٍ في العراق.
        </p>
      </div>
    </div>
  </div>
</section>

<div class="quick-contact" aria-label="روابط التواصل السريع">
  <a href="https://wa.me/9647700151262" target="_blank" title="تواصل عبر واتساب" aria-label="واتساب"><i class="bi bi-whatsapp"></i></a>
  <a href="https://t.me/haquktaerif" target="_blank" title="تواصل عبر تلغرام" aria-label="تلغرام"><i class="bi bi-telegram"></i></a>
</div>

<footer>
  <div class="container text-center">
    © 2025 حقك تعرف. جميع الحقوق محفوظة.
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

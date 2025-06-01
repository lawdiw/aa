<?php
session_start();
require_once 'includes/db_connect.php';

$search = trim($_GET['search'] ?? '');
$filter_specialty = $_GET['specialty'] ?? '';
$filter_city = $_GET['city'] ?? '';

// جلب التخصصات والمدن الفريدة من جدول lawyers
$specialties = $db->query("SELECT DISTINCT specialty FROM lawyers WHERE specialty IS NOT NULL AND specialty <> '' ORDER BY specialty ASC")->fetchAll(PDO::FETCH_COLUMN);
$cities = $db->query("SELECT DISTINCT city FROM lawyers WHERE city IS NOT NULL AND city <> '' ORDER BY city ASC")->fetchAll(PDO::FETCH_COLUMN);

// بناء شرط WHERE ديناميكي
$whereClauses = [];
$params = [];

if ($search) {
    $whereClauses[] = "name LIKE :search";
    $params['search'] = "%$search%";
}
if ($filter_specialty) {
    $whereClauses[] = "specialty = :specialty";
    $params['specialty'] = $filter_specialty;
}
if ($filter_city) {
    $whereClauses[] = "city = :city";
    $params['city'] = $filter_city;
}

$whereSQL = "";
if (count($whereClauses) > 0) {
    $whereSQL = " WHERE " . implode(" AND ", $whereClauses);
}

try {
    $stmt = $db->prepare("SELECT name, email, phone, specialty, city, profile_photo FROM lawyers $whereSQL ORDER BY name ASC");
    $stmt->execute($params);
    $lawyers = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $lawyers = [];
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>المحامون - حقك تعرف</title>
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
  margin: 0; padding: 20px;
}
h1, h2 {
  color: var(--primary-color);
  font-weight: 700;
  text-align: center;
  margin-bottom: 30px;
}
.container-box {
  background-color: var(--box-bg);
  border-radius: 20px;
  padding: 2rem;
  max-width: 1000px;
  margin: auto;
  box-shadow: 0 6px 30px #0006;
}
.lawyer-card {
  background: var(--card-bg-gradient);
  border-radius: 15px;
  padding: 15px;
  box-shadow: 0 10px 30px #0008;
  color: var(--text-light);
  margin-bottom: 20px;
  display: flex;
  gap: 15px;
  align-items: center;
  opacity: 0;
  transform: translateY(30px);
  animation: fadeinup 0.7s forwards;
}
.lawyers-list .lawyer-card { animation-delay: 0.08s; }
@keyframes fadeinup {
  to { opacity: 1; transform: translateY(0); }
}
.lawyer-photo {
  width: 80px;
  height: 80px;
  border-radius: 50%;
  object-fit: cover;
  border: 2px solid var(--primary-color);
}
.lawyer-info {
  flex-grow: 1;
}
.lawyer-card h6 {
  color: var(--primary-color);
  font-weight: 700;
  margin-bottom: 8px;
  font-size: 1.25rem;
}
.lawyer-contact-wrap { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 12px; }
.lawyer-contact i {
  margin-left: 8px;
  color: var(--primary-color);
}
.lawyer-contact, .lawyer-meta {
  font-size: 0.9rem;
  margin-bottom: 6px;
}
.filter-form {
  max-width: 700px;
  margin: 0 auto 30px auto;
  display: flex;
  gap: 12px;
  flex-wrap: wrap;
}
.filter-form select, .filter-form input[type="text"] {
  flex: 1 1 200px;
  background-color: var(--box-bg);
  border: 1px solid var(--primary-color);
  color: var(--text-light);
  border-radius: 8px;
  padding: 8px 12px;
}
.filter-form button {
  background: var(--btn-bg);
  border: none;
  border-radius: 8px;
  color: var(--text-light);
  padding: 8px 20px;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.3s ease;
}
.filter-form button:hover {
  background: var(--btn-bg-hover);
}
.btn-contact {
  background-color: transparent;
  border: 1.5px solid var(--primary-color);
  border-radius: 8px;
  color: var(--primary-color);
  padding: 7px 15px;
  font-weight: 600;
  font-size: 1rem;
  margin-left: 8px;
  cursor: pointer;
  transition: all 0.3s ease;
  text-decoration: none;
  display: flex;
  align-items: center;
  gap: 7px;
}
.btn-contact:hover,
.btn-whatsapp:hover {
  background: var(--primary-color);
  color: #fff;
  border-color: #fff;
}
.btn-whatsapp i { color: #27d045; font-size: 1.3rem; }
/* زر الاستشارة الرئيسي */
.btn-consult-main {
  display: inline-block;
  background: var(--btn-bg);
  color: var(--text-light);
  padding: 10px 25px;
  font-size: 1.15rem;
  border-radius: 10px;
  font-weight: 700;
  box-shadow: 0 2px 18px #0004;
  transition: background .2s, box-shadow .2s;
  border: none;
  text-decoration: none;
  margin-bottom: 8px;
}
.btn-consult-main:hover {
  background: var(--btn-bg-hover);
  color: #fff;
  box-shadow: 0 6px 25px #0005;
  text-decoration: none;
}
/* Responsive adjustments */
@media (max-width: 600px) {
  .lawyer-card { flex-direction: column; align-items: stretch; text-align: center; }
  .lawyer-photo { margin: 0 auto 10px auto; }
}
</style>
</head>
<body>
<?php include 'includes/navigation.php'; ?>

<div class="container-box">
  <h1>قائمة المحامين المسجلين</h1>

  <!-- قسم تعريفي صغير -->
  <p style="text-align:center; color:#d3e3fa; margin-bottom:22px; font-size:1.05rem;">
      تصفح قائمة المحامين المعتمدين في منصتنا وابحث عن محامٍ حسب التخصص أو المدينة، أو 
      <a href="consultation.php" style="color:#f7c873;text-decoration:underline;font-weight:600;">اطلب استشارة قانونية الآن</a>.
  </p>
  
  <!-- زر استشارة واضح في الأعلى -->
  <div style="text-align:center; margin-bottom:18px;">
    <a href="consultation.php" class="btn-consult-main">
      <i class="bi bi-chat-dots-fill"></i> اطلب استشارة قانونية
    </a>
  </div>
  
  <form method="GET" class="filter-form" role="search" aria-label="تصفية المحامين">
    <input type="text" name="search" placeholder="ابحث باسم المحامي..." value="<?= htmlspecialchars($search) ?>" aria-label="بحث باسم المحامي" />
    <select name="specialty" aria-label="تصفية حسب التخصص">
      <option value="">كل التخصصات</option>
      <?php foreach ($specialties as $spec): ?>
        <option value="<?= htmlspecialchars($spec) ?>" <?= $filter_specialty === $spec ? 'selected' : '' ?>><?= htmlspecialchars($spec) ?></option>
      <?php endforeach; ?>
    </select>
    <select name="city" aria-label="تصفية حسب المدينة">
      <option value="">كل المدن</option>
      <?php foreach ($cities as $city): ?>
        <option value="<?= htmlspecialchars($city) ?>" <?= $filter_city === $city ? 'selected' : '' ?>><?= htmlspecialchars($city) ?></option>
      <?php endforeach; ?>
    </select>
    <button type="submit">تصفية</button>
  </form>

  <?php if (count($lawyers) === 0): ?>
    <p style="text-align:center; color:#f7c873;">لا توجد نتائج مطابقة للبحث أو التصفية.</p>
  <?php else: ?>
    <div class="lawyers-list">
    <?php foreach ($lawyers as $lawyer): ?>
      <div class="lawyer-card" role="article" aria-label="معلومات محامي">
        <?php if (!empty($lawyer['profile_photo'])): ?>
          <img src="<?= htmlspecialchars($lawyer['profile_photo']) ?>" alt="صورة المحامي <?= htmlspecialchars($lawyer['name']) ?>" class="lawyer-photo" loading="lazy" />
        <?php else: ?>
          <img src="images/default-profile.png" alt="صورة افتراضية للمحامي" class="lawyer-photo" loading="lazy" />
        <?php endif; ?>
        <div class="lawyer-info">
          <h6><?= htmlspecialchars($lawyer['name']) ?></h6>
          <div class="lawyer-meta">
            <span><i class="bi bi-bookmark-fill"></i> <?= htmlspecialchars($lawyer['specialty'] ?: 'غير محدد') ?></span> |
            <span><i class="bi bi-geo-alt-fill"></i> <?= htmlspecialchars($lawyer['city'] ?: 'غير محدد') ?></span>
          </div>
          <div class="lawyer-contact-wrap">
            <a href="mailto:<?= htmlspecialchars($lawyer['email']) ?>" class="btn-contact" title="إرسال بريد إلكتروني">
              <i class="bi bi-envelope-fill"></i> <?= htmlspecialchars($lawyer['email']) ?>
            </a>
            <a href="tel:<?= preg_replace('/[^0-9+]/', '', $lawyer['phone']) ?>" class="btn-contact" title="اتصال هاتفي">
              <i class="bi bi-telephone-fill"></i> <?= htmlspecialchars($lawyer['phone']) ?>
            </a>
            <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $lawyer['phone']) ?>" target="_blank" rel="noopener" class="btn-contact btn-whatsapp" title="واتساب">
              <i class="bi bi-whatsapp"></i> واتساب
            </a>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>
</body>
</html>

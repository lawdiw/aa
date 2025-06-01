<?php
session_start();
require_once 'includes/db_connect.php';

// نموذج البحث
$q = trim($_GET['q'] ?? '');

// إعداد الاستعلام
if ($q) {
    $where = "WHERE title LIKE :q OR category LIKE :q OR description LIKE :q";
    $stmt = $db->prepare("SELECT id, title, category, description FROM laws $where ORDER BY category, title");
    $stmt->execute([':q' => "%$q%"]);
} else {
    $stmt = $db->query("SELECT id, title, category, description FROM laws ORDER BY category, title");
}
$laws = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>مكتبة القوانين - حقك تعرف</title>
  <link href="https://fonts.googleapis.com/css2?family=Cairo&family=Tajawal&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" />
  <link rel="stylesheet" href="css/style.css" />
  <style>
    body { background: #11213a; color: #fff; font-family: 'Cairo','Tajawal',sans-serif; }
    .laws-container { max-width: 1050px; margin: 38px auto 40px; }
    .library-title { color: #f7c873; font-weight: bold; text-align: center; margin-bottom: 30px; }
    .law-card {
      background: #192846;
      color: #fff;
      border-radius: 16px;
      box-shadow: 0 6px 24px #0007;
      border: none;
      margin-bottom: 22px;
      min-height: 170px;
      transition: transform 0.2s;
    }
    .law-card:hover { transform: translateY(-5px) scale(1.02);}
    .law-card .card-header {
      background: #203456;
      color: #f7c873;
      border-radius: 16px 16px 0 0;
      font-size: 1.15rem;
      font-weight: bold;
      border-bottom: 0;
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 10px;
    }
    .law-badge {
      background: #2ec5a3;
      color: #fff;
      border-radius: 20px;
      padding: 4px 18px;
      font-size: 0.95rem;
      font-weight: 500;
      margin-right: 8px;
    }
    .law-desc { font-size: 1rem; color: #eaeaea; }
    .search-form { max-width: 380px; margin: 0 auto 34px; }
    .form-control { background: #233254; color: #fff; border: 1px solid #2ec5a3; }
    .form-control:focus { background: #253360; border-color: #f7c873; color: #fff;}
    .btn-primary { background: linear-gradient(90deg, #237cbf, #2ec5a3); border: none; }
    .btn-primary:hover { background: linear-gradient(90deg, #1b658c, #238c72);}
  </style>
</head>
<body>
<?php include 'includes/navigation.php'; ?>

<div class="laws-container">
  <h2 class="library-title">مكتبة القوانين والدعاوى</h2>

  <!-- نموذج البحث -->
  <form method="GET" class="search-form mb-4 d-flex">
    <input type="text" name="q" class="form-control me-2" value="<?=htmlspecialchars($q)?>" placeholder="ابحث عن قانون أو دعوى أو تصنيف..." autofocus>
    <button type="submit" class="btn btn-primary">بحث</button>
  </form>

  <div class="row">
    <?php if ($laws && count($laws) > 0): ?>
      <?php foreach($laws as $law): ?>
        <div class="col-md-6 col-lg-4 mb-4">
          <div class="card law-card h-100">
            <div class="card-header">
              <span><?= htmlspecialchars($law['title']) ?></span>
              <?php if($law['category']): ?>
                <span class="law-badge"><?= htmlspecialchars($law['category']) ?></span>
              <?php endif; ?>
            </div>
            <div class="card-body law-desc">
              <?= nl2br(htmlspecialchars($law['description'])) ?>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <div class="col-12 text-center py-4">
        <div class="alert alert-warning" style="background:#f7c873;color:#11213a;">لا توجد نتائج مطابقة لبحثك.</div>
      </div>
    <?php endif; ?>
  </div>
</div>

<footer style="background: linear-gradient(135deg, #192846, #f7c873); color: #fff; padding: 18px 0; margin-top: 30px; text-align:center; font-weight:600; font-size:1rem;">
  <div class="container text-center">
    © 2025 حقك تعرف. جميع الحقوق محفوظة.
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

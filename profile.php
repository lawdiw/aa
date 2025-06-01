<?php
session_start();
require_once 'includes/db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['user_role'];
$error = '';
$success = '';
$return_to = $_GET['return_to'] ?? 'index.php';

// جلب بيانات المستخدم
$stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) die("المستخدم غير موجود.");

// جلب بيانات المحامي إذا كان المستخدم محامي
if ($user_role === 'lawyer') {
    $stmtLawyer = $db->prepare("SELECT specialty, city, profile_photo, license_photo FROM lawyers WHERE email = ?");
    $stmtLawyer->execute([$user['email']]);
    $lawyerData = $stmtLawyer->fetch(PDO::FETCH_ASSOC);
} else {
    $lawyerData = [];
}

$upload_dir = 'uploads/';
if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);

function uploadFile($fileInputName, $existingFile = null) {
    global $upload_dir;
    if (isset($_FILES[$fileInputName]) && $_FILES[$fileInputName]['error'] === UPLOAD_ERR_OK) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($_FILES[$fileInputName]['type'], $allowed_types)) {
            return ['error' => "نوع الملف المرفوع غير مدعوم."];
        }
        $ext = pathinfo($_FILES[$fileInputName]['name'], PATHINFO_EXTENSION);
        $filename = $upload_dir . uniqid($fileInputName . '_') . '.' . $ext;
        if (move_uploaded_file($_FILES[$fileInputName]['tmp_name'], $filename)) {
            // حذف الملف القديم إن وجد
            if ($existingFile && file_exists($existingFile)) unlink($existingFile);
            return ['path' => $filename];
        } else {
            return ['error' => "فشل رفع الملف."];
        }
    }
    return ['path' => $existingFile];
}

// الأدوار (لشارة الدور)
$roles_config = [
    'admin'   => ['name'=>'مشرف',    'badge'=>'bg-primary'],
    'lawyer'  => ['name'=>'محامي',   'badge'=>'bg-success'],
    'client'  => ['name'=>'مواطن',   'badge'=>'bg-info text-dark'],
    'manager' => ['name'=>'مدير',    'badge'=>'bg-danger'],
    'superadmin' => ['name'=>'مدير أعلى', 'badge'=>'bg-warning text-dark'],
    'super_admin' => ['name'=>'مدير أعلى', 'badge'=>'bg-warning text-dark'],
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['full_name'] ?? $user['name']);
    $email = trim($_POST['email'] ?? $user['email']);
    $phone = trim($_POST['phone'] ?? $user['phone']);
    $specialty = trim($_POST['specialty'] ?? $lawyerData['specialty'] ?? '');
    $city = trim($_POST['city'] ?? $lawyerData['city'] ?? '');

    // منع تكرار الاسم الكامل
    if ($name && $name != $user['name']) {
        $stmtCheck = $db->prepare("SELECT COUNT(*) FROM users WHERE name=? AND id<>?");
        $stmtCheck->execute([$name, $user_id]);
        if ($stmtCheck->fetchColumn() > 0) {
            $error = "الاسم الكامل مستخدم من قبل.";
        }
    }

    // منع تكرار البريد الإلكتروني
    if (!$error && $email && $email != $user['email']) {
        $stmtCheck = $db->prepare("SELECT COUNT(*) FROM users WHERE email=? AND id<>?");
        $stmtCheck->execute([$email, $user_id]);
        if ($stmtCheck->fetchColumn() > 0) {
            $error = "البريد الإلكتروني مستخدم من قبل.";
        }
    }

    // تحديث الحقول فقط إذا تغيّر شيء
    $fields = [];
    $params = [];

    if (!$error) {
        if ($name && $name != $user['name']) {
            $fields[] = 'name=?'; $params[] = $name; $_SESSION['user_name'] = $name;
        }
        if ($email && $email != $user['email']) {
            $fields[] = 'email=?'; $params[] = $email;
        }
        if ($phone && $phone != $user['phone']) {
            $fields[] = 'phone=?'; $params[] = $phone;
        }

        // رفع الصورة الشخصية
        $uploadResult = uploadFile('profile_photo', $user['profile_photo'] ?? null);
        if (isset($uploadResult['error'])) {
            $error = $uploadResult['error'];
        } else {
            $profile_photo_path = $uploadResult['path'];
            if ($profile_photo_path != $user['profile_photo']) {
                $fields[] = 'profile_photo=?'; $params[] = $profile_photo_path;
                $_SESSION['user_photo'] = $profile_photo_path ?: 'images/default-profile.png';
            }
        }

        // كلمة المرور (اختياري)
        if (!empty($_POST['new_password'])) {
            if ($_POST['new_password'] === $_POST['confirm_password'] && strlen($_POST['new_password']) >= 6) {
                $fields[] = "password=?";
                $params[] = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
            } else {
                $error = "كلمتا المرور غير متطابقتين أو قصيرة جدًا.";
            }
        }

        // تحديث فقط إذا لم توجد أخطاء ويوجد تغيير
        if (!$error && count($fields) > 0) {
            $params[] = $user_id;
            $stmtUpdate = $db->prepare("UPDATE users SET " . implode(',', $fields) . " WHERE id = ?");
            $stmtUpdate->execute($params);

            // تحديث بيانات الجلسة (الصورة + الاسم)
            $user['name'] = $name;
            $user['email'] = $email;
            $user['phone'] = $phone;
            $user['profile_photo'] = $_SESSION['user_photo'];

            $success = "تم تحديث البيانات بنجاح.";
        }

        // تحديث بيانات المحامي إذا كان محامي
        if (!$error && $user_role === 'lawyer') {
            $lawyerFields = [];
            $lawyerParams = [];
            if ($specialty && $specialty != $lawyerData['specialty']) {
                $lawyerFields[] = 'specialty=?'; $lawyerParams[] = $specialty;
            }
            if ($city && $city != $lawyerData['city']) {
                $lawyerFields[] = 'city=?'; $lawyerParams[] = $city;
            }
            $uploadLicenseResult = uploadFile('license_photo', $lawyerData['license_photo'] ?? null);
            if (!isset($uploadLicenseResult['error']) && $uploadLicenseResult['path'] != $lawyerData['license_photo']) {
                $lawyerFields[] = 'license_photo=?'; $lawyerParams[] = $uploadLicenseResult['path'];
            }
            if (count($lawyerFields) > 0) {
                $lawyerParams[] = $user['email'];
                $stmtLawyerUpdate = $db->prepare("UPDATE lawyers SET " . implode(',', $lawyerFields) . " WHERE email = ?");
                $stmtLawyerUpdate->execute($lawyerParams);
            }
        }

        // تحديث المتغيرات المحلية لعرض البيانات الجديدة في الفورم
        $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user_role === 'lawyer') {
            $stmtLawyer = $db->prepare("SELECT specialty, city, profile_photo, license_photo FROM lawyers WHERE email = ?");
            $stmtLawyer->execute([$user['email']]);
            $lawyerData = $stmtLawyer->fetch(PDO::FETCH_ASSOC);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>الملف الشخصي - حقك تعرف</title>
<link href="https://fonts.googleapis.com/css2?family=Cairo&family=Tajawal&display=swap" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" />
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
<style>
body {
  background-color: #11213a;
  color: #fff;
  font-family: 'Cairo', 'Tajawal', sans-serif;
  min-height: 100vh;
  padding: 20px;
}
.container-box {
  background-color: #192846;
  border-radius: 20px;
  padding: 30px;
  max-width: 600px;
  margin: 40px auto;
  box-shadow: 0 6px 30px #0008;
}
h1 {
  color: #f7c873;
  margin-bottom: 30px;
  text-align: center;
}
form label {
  font-weight: 600;
}
.form-control {
  background-color: #243455;
  border: 1px solid #32416a;
  color: #fff;
  border-radius: 8px;
}
.form-control:focus {
  background-color: #233054;
  border-color: #f7c873;
  color: #fff;
  box-shadow: none;
}
.btn-primary {
  background: linear-gradient(90deg, #237cbf, #2ec5a3);
  border: none;
  font-weight: 600;
  padding: 10px 25px;
  border-radius: 10px;
  width: 100%;
  font-size: 1.1rem;
  transition: background 0.3s ease;
}
.btn-primary:hover {
  background: linear-gradient(90deg, #1b658c, #238c72);
}
.profile-photo, .license-photo {
  width: 120px;
  height: 120px;
  border-radius: 10px;
  object-fit: cover;
  border: 3px solid #f7c873;
  display: block;
  margin: 10px auto 20px auto;
}
.alert {
  margin-top: 20px;
}
.badge-role-preview {
  font-size: 1em;
  padding: .4em 1em;
  margin: 0 2px;
  background: #32416a;
  color: #f7c873;
  border-radius: 10px;
  font-weight: bold;
}
</style>
</head>
<body>
<?php include 'includes/navigation.php'; ?>

<div class="container-box">
  <h1>الملف الشخصي</h1>
  <div class="text-center mb-3">
    <span class="badge badge-role-preview <?= $roles_config[$user['role']]['badge'] ?? 'bg-secondary' ?>">
      <?= $roles_config[$user['role']]['name'] ?? 'غير محدد' ?>
    </span>
    <?php if (($user['status'] ?? '') == 'active'): ?>
      <span class="badge bg-success ms-2"><i class="bi bi-check-circle"></i> مفعل</span>
    <?php else: ?>
      <span class="badge bg-danger ms-2"><i class="bi bi-x-circle"></i> معطل</span>
    <?php endif ?>
  </div>
  <?php if ($error): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
  <?php elseif ($success): ?>
    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <script>
      setTimeout(function() {
        window.location.href = "<?= htmlspecialchars($return_to) ?>";
      }, 2000);
    </script>
  <?php endif; ?>
  <form method="POST" enctype="multipart/form-data" novalidate>
    <div class="mb-3">
      <label for="full_name" class="form-label">الاسم الكامل</label>
      <input type="text" id="full_name" name="full_name" class="form-control" value="<?= htmlspecialchars($user['name'] ?? '') ?>" required />
    </div>
    <div class="mb-3">
      <label for="email" class="form-label">البريد الإلكتروني</label>
      <input type="email" id="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email'] ?? '') ?>" required />
    </div>
    <div class="mb-3">
      <label for="phone" class="form-label">رقم الهاتف</label>
      <input type="tel" id="phone" name="phone" class="form-control" value="<?= htmlspecialchars($user['phone'] ?? '') ?>" required />
    </div>
    <div class="mb-3">
      <label for="profile_photo" class="form-label">الصورة الشخصية (اختياري)</label>
      <?php if (!empty($user['profile_photo']) && file_exists($user['profile_photo'])): ?>
        <img src="<?= htmlspecialchars($user['profile_photo']) ?>" alt="الصورة الشخصية" class="profile-photo" loading="lazy" />
      <?php else: ?>
        <img src="images/default-profile.png" alt="صورة افتراضية" class="profile-photo" loading="lazy" />
      <?php endif; ?>
      <input type="file" id="profile_photo" name="profile_photo" accept="image/*" class="form-control" />
    </div>
    <div class="mb-3">
      <label for="new_password" class="form-label">كلمة المرور الجديدة</label>
      <input type="password" id="new_password" name="new_password" class="form-control" placeholder="اتركه فارغًا إذا لا تريد تغيير كلمة المرور" />
    </div>
    <div class="mb-3">
      <label for="confirm_password" class="form-label">تأكيد كلمة المرور</label>
      <input type="password" id="confirm_password" name="confirm_password" class="form-control" placeholder="أعد كتابة كلمة المرور" />
    </div>
    <?php if ($user_role === 'lawyer'): ?>
      <hr />
      <h4>بيانات المحامي</h4>
      <div class="mb-3">
        <label for="specialty" class="form-label">التخصص</label>
        <input type="text" id="specialty" name="specialty" class="form-control" value="<?= htmlspecialchars($lawyerData['specialty'] ?? '') ?>" />
      </div>
      <div class="mb-3">
        <label for="city" class="form-label">المدينة</label>
        <input type="text" id="city" name="city" class="form-control" value="<?= htmlspecialchars($lawyerData['city'] ?? '') ?>" />
      </div>
      <div class="mb-3">
        <label for="license_photo" class="form-label">صورة هوية النقابة (اختياري)</label>
        <?php if (!empty($lawyerData['license_photo']) && file_exists($lawyerData['license_photo'])): ?>
          <img src="<?= htmlspecialchars($lawyerData['license_photo']) ?>" alt="هوية النقابة" class="license-photo" loading="lazy" />
        <?php else: ?>
          <p class="text-warning">لم يتم رفع صورة هوية النقابة بعد.</p>
        <?php endif; ?>
        <input type="file" id="license_photo" name="license_photo" accept="image/*" class="form-control" />
        <small class="text-light">يجب أن تكون صورة الهوية سارية وغير منتهية.</small>
      </div>
    <?php endif; ?>
    <button type="submit" class="btn btn-primary">تحديث الملف الشخصي</button>
    <a href="<?= htmlspecialchars($return_to) ?>" class="btn btn-secondary mt-2 w-100">عودة للصفحة السابقة</a>
  </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
session_start();
require_once 'includes/db_connect.php';

// قائمة معرفات العقود الموقوفة (تحت الصيانة والتدقيق)
$disabledTemplates = [
    62, 63, 64, 65, 66, 67, 68, 69, 70, 71, 72, 73, 74, 75, 76, 77, 78, 79, 80, 81, 82, 83, 84, 85, 86
];

// جلب القوالب
$templatesStmt = $db->query("SELECT id, name FROM contract_templates ORDER BY name ASC");
$templates = $templatesStmt->fetchAll(PDO::FETCH_ASSOC);

// رقم العقد الحالي
$template_id = isset($_GET['template_id']) ? (int)$_GET['template_id'] : null;
$template = null;
$fields = [];

if ($template_id) {
    $stmt = $db->prepare("SELECT * FROM contract_templates WHERE id = :id");
    $stmt->execute(['id' => $template_id]);
    $template = $stmt->fetch(PDO::FETCH_ASSOC);

    $fieldsStmt = $db->prepare("SELECT * FROM contract_fields WHERE template_id = :tid ORDER BY id ASC");
    $fieldsStmt->execute(['tid' => $template_id]);
    $fields = $fieldsStmt->fetchAll(PDO::FETCH_ASSOC);
}

// تحقق من حالة التعطيل
$isDisabled = $template_id && in_array($template_id, $disabledTemplates);

$contract_final = '';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $template && !$isDisabled) {
    $data = [];
    foreach ($fields as $field) {
        $key = $field['field_name'];
        $value = trim($_POST[$key] ?? '');
        if ($field['required'] && $value === '') {
            $error = "يرجى ملء حقل: " . $field['label'];
            break;
        }
        $data[$key] = $value;
    }
    if (!$error) {
        $contract_final = $template['content'];
        foreach ($data as $key => $val) {
            $contract_final = str_replace("{{{$key}}}", $val, $contract_final);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>نماذج العقود - حقك تعرف</title>
<link href="https://fonts.googleapis.com/css2?family=Cairo&family=Tajawal&display=swap" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet" />
<style>
body {
  background-color: #0d1b3d;
  color: #f7c873;
  font-family: 'Cairo', 'Tajawal', sans-serif;
  padding: 20px 15px 50px;
  min-height: 100vh;
}
.container {
  max-width: 900px;
  margin: 0 auto;
}
label {
  font-weight: 700;
  margin-top: 15px;
  display: block;
}
input, textarea, select {
  background-color: #1e2a59;
  border: 1px solid #f7c873;
  color: #f7c873;
  border-radius: 6px;
  padding: 8px 12px;
  width: 100%;
  margin-bottom: 15px;
  font-size: 1rem;
}
textarea {
  resize: vertical;
}
button {
  background: linear-gradient(90deg, #237cbf, #2ec5a3);
  border: none;
  padding: 10px 25px;
  font-weight: 700;
  border-radius: 8px;
  color: #fff;
  cursor: pointer;
  font-size: 1.1rem;
  transition: background 0.3s ease;
  width: 100%;
}
button:disabled,
button[disabled] {
  background: #888 !important;
  color: #ccc !important;
  cursor: not-allowed;
}
button:hover:enabled {
  background: linear-gradient(90deg, #1b658c, #238c72);
}
.contract-output {
  background-color: #1a2a5a;
  white-space: pre-wrap;
  font-family: 'Courier New', monospace;
  color: #f7c873;
  padding: 20px;
  border-radius: 12px;
  margin-top: 30px;
  max-height: 600px;
  overflow-y: auto;
  user-select: all;
}
.error {
  color: #ff6b6b;
  font-weight: 700;
  margin-bottom: 15px;
  font-size: 1.1rem;
  text-align: center;
}
</style>
</head>
<body>

<?php include 'includes/navigation.php'; ?>

<div class="container">
  <h1 class="mb-4 text-center" style="font-weight:800;">نماذج العقود</h1>

  <form method="get" class="mb-4 text-center">
    <select name="template_id" onchange="this.form.submit()" required style="max-width: 400px; margin: 0 auto; display: inline-block;">
      <option value="">-- اختر عقداً --</option>
      <?php foreach ($templates as $tpl): ?>
        <option value="<?= $tpl['id'] ?>" <?= ($tpl['id'] == $template_id) ? 'selected' : '' ?>>
          <?= htmlspecialchars($tpl['name']) ?>
        </option>
      <?php endforeach; ?>
    </select>
  </form>

  <?php if ($template): ?>
    <h2 class="mb-3" style="font-weight:700;"><?= htmlspecialchars($template['name']) ?></h2>

    <?php if ($error): ?>
      <div class="error"><?= $error ?></div>
    <?php endif; ?>

    <form method="post" novalidate>
      <?php foreach ($fields as $field): ?>
        <label for="<?= htmlspecialchars($field['field_name']) ?>"><?= htmlspecialchars($field['label']) ?><?php if ($field['required']) echo ' *'; ?></label>
        <?php
          $name = htmlspecialchars($field['field_name']);
          $value = $_POST[$name] ?? '';
          $required = $field['required'] ? 'required' : '';
          if ($field['input_type'] === 'textarea'): ?>
            <textarea id="<?= $name ?>" name="<?= $name ?>" <?= $required ?>><?= htmlspecialchars($value) ?></textarea>
          <?php elseif ($field['input_type'] === 'select' && $field['options']):
            $opts = explode(',', $field['options']);
          ?>
            <select id="<?= $name ?>" name="<?= $name ?>" <?= $required ?>>
              <option value="">-- اختر --</option>
              <?php foreach ($opts as $opt): ?>
                <option value="<?= htmlspecialchars(trim($opt)) ?>" <?= (trim($opt) === $value) ? 'selected' : '' ?>>
                  <?= htmlspecialchars(trim($opt)) ?>
                </option>
              <?php endforeach; ?>
            </select>
          <?php else: ?>
            <input type="<?= htmlspecialchars($field['input_type']) ?>" id="<?= $name ?>" name="<?= $name ?>" value="<?= htmlspecialchars($value) ?>" <?= $required ?> />
          <?php endif; ?>
      <?php endforeach; ?>

      <?php if ($isDisabled): ?>
        <div class="alert alert-warning" role="alert" style="margin-top:15px;">
          هذا العقد يخضع للتدقيق من قبل كوادرنا القانونية، وسيكون متاحًا قريبًا.
        </div>
      <?php endif; ?>

      <button type="submit" <?= $isDisabled ? 'disabled' : '' ?>>توليد العقد وطباعته</button>
    </form>

    <?php if ($contract_final && !$isDisabled): ?>
      <div class="contract-output" tabindex="0" aria-label="نص العقد النهائي" role="region" aria-live="polite" aria-atomic="true">
        <?= nl2br(htmlspecialchars($contract_final)) ?>
      </div>
      <form action="generate_pdf.php" method="post" target="_blank" style="margin-top: 10px;">
        <?php
        foreach ($fields as $field) {
          $name = $field['field_name'];
          $val = $_POST[$name] ?? '';
          echo '<input type="hidden" name="'.htmlspecialchars($name).'" value="'.htmlspecialchars($val).'">';
        }
        ?>
        <input type="hidden" name="template_id" value="<?= htmlspecialchars($template_id) ?>">
        <button type="submit"
          style="width:100%; background: linear-gradient(90deg, #237cbf, #2ec5a3); border:none; padding: 10px; color:#fff; font-weight:700; border-radius:8px; cursor:pointer;"
          <?= $isDisabled ? 'disabled title="الخدمة غير متاحة حالياً لهذا العقد."' : '' ?>>
          تحميل العقد PDF
        </button>
      </form>
    <?php endif; ?>

  <?php else: ?>
    <p class="text-center" style="color:#f7c873; font-size:1.1rem;">يرجى اختيار نموذج عقد للمتابعة.</p>
  <?php endif; ?>
</div>

</body>
</html>

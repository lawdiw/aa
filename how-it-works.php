<?php
session_start();
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>كيف تعمل المنصة | حقك تعرف</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="css/style.css">
<style>
body {
  background-color: #11213a;
  color: #fff;
  font-family: 'Cairo', 'Tajawal', sans-serif;
  min-height: 100vh;
}
.container-box {
  background-color: #192846;
  border-radius: 20px;
  padding: 2.5rem 1.5rem;
  margin: 40px auto 60px auto;
  box-shadow: 0 6px 30px #0007;
  max-width: 900px;
}
h1, h2, h3 {
  color: #f7c873;
  font-weight: bold;
  text-align: center;
  margin-bottom: 30px;
}
hr {
  border-top: 2px solid #f7c873;
  margin: 2rem 0;
}
.card {
  background: linear-gradient(135deg, #203456, #11213a);
  border: none;
  color: #fff;
  border-radius: 14px;
  margin-bottom: 24px;
}
.card .card-header {
  background: transparent;
  border-bottom: 1px solid #f7c87388;
  color: #f7c873;
  font-weight: bold;
}
.important-note {
  color: #fff3cd;
  background: #b5832a;
  border-radius: 9px;
  padding: 10px 20px;
  font-weight: bold;
  margin: 20px 0 35px 0;
  font-size: 1.15rem;
  text-align: center;
}
</style>
</head>
<body>
<?php include 'includes/navigation.php'; ?>

<div class="container-box">
    <h1>كيف تعمل المنصة؟</h1>
    <div class="important-note">
        <i class="bi bi-exclamation-circle"></i>
        تنويه مهم: <b>الاستشارات القانونية على المنصة ليست مجانية،</b> ويجب دفع الأتعاب المحددة قبل استلام الرد من المحامي المختص. جميع الأسعار تظهر بوضوح قبل تأكيد الطلب.
    </div>
    <hr>
    <div class="card">
        <div class="card-header">الخطوة الأولى: التسجيل في المنصة</div>
        <div class="card-body">
            <p>أنشئ حسابك كمواطن أو محامي واملأ البيانات المطلوبة بدقة تامة، مع رفع المستندات الأساسية إن لزم.</p>
        </div>
    </div>
    <div class="card">
        <div class="card-header">الخطوة الثانية: اختيار نوع الخدمة القانونية</div>
        <div class="card-body">
            <p>حدد ما إذا كنت تريد طلب استشارة قانونية، أو تصفح مكتبة القوانين، أو استخدام خدمات محاكاة القضايا أو الأسئلة الشائعة.</p>
        </div>
    </div>
    <div class="card">
        <div class="card-header">الخطوة الثالثة: تعبئة طلب الاستشارة أو الخدمة</div>
        <div class="card-body">
            <p>املأ نموذج الاستشارة أو الطلب، مع ذكر التفاصيل اللازمة (نوع القضية، الشرح، المرفقات إن وجدت).</p>
        </div>
    </div>
    <div class="card">
        <div class="card-header">الخطوة الرابعة: دفع الأتعاب القانونية</div>
        <div class="card-body">
            <p>بعد تقديم الطلب ستظهر لك تفاصيل المبلغ المطلوب دفعه حسب نوع الخدمة. يمكنك الدفع عبر وسائل دفع آمنة (أونلاين أو تحويل مصرفي).</p>
        </div>
    </div>
    <div class="card">
        <div class="card-header">الخطوة الخامسة: مراجعة الطلب والرد من المحامي</div>
        <div class="card-body">
            <p>يتم تحويل طلبك إلى أحد المحامين المعتمدين في نقابة المحامين العراقيين لمراجعته والرد عليك ضمن المدة الزمنية المحددة.</p>
        </div>
    </div>
    <div class="card">
        <div class="card-header">الخطوة السادسة: استلام الرد والمتابعة</div>
        <div class="card-body">
            <p>ستتلقى الرد القانوني المناسب عبر المنصة، ويمكنك التواصل مجددًا مع المحامي إذا احتجت لتوضيح أو استفسار إضافي.</p>
        </div>
    </div>
    <div class="card">
        <div class="card-header">الخطوة السابعة: سرية المعلومات وحماية خصوصيتك</div>
        <div class="card-body">
            <p>جميع بياناتك ومراسلاتك محمية بسرية تامة، ويخضع المحامون في المنصة لأحكام قانون المحاماة العراقي وضوابط نقابة المحامين العراقيين.</p>
        </div>
    </div>
</div>

<footer class="text-center py-3" style="background:linear-gradient(135deg, #192846, #f7c873);color:#192846;font-weight:600;">
    © 2025 حقك تعرف. جميع الحقوق محفوظة.
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

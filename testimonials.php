<?php
session_start();
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>آراء العملاء | حقك تعرف</title>
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
  max-width: 1200px;
}
h1, h2, h3 {
  color: #f7c873;
  font-weight: bold;
  text-align: center;
  margin-bottom: 30px;
}
.testimonial-card {
  background: linear-gradient(135deg, #203456, #11213a);
  border-radius: 14px;
  padding: 1.8rem 1.2rem 1.2rem 1.2rem;
  margin-bottom: 22px;
  box-shadow: 0 7px 24px #0006;
  color: #fff;
  min-height: 220px;
  position: relative;
  display: flex;
  flex-direction: column;
  justify-content: flex-start;
}
.testimonial-card .icon-bubble {
  width: 46px;
  height: 46px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #f7c873;
  color: #192846;
  border-radius: 50%;
  font-size: 2rem;
  position: absolute;
  top: -23px;
  right: 22px;
  border: 3px solid #203456;
  box-shadow: 0 4px 16px #0004;
}
.testimonial-message {
  margin-top: 40px;
  font-size: 1.03rem;
  min-height: 70px;
}
.testimonial-author {
  font-weight: bold;
  color: #2ec5a3;
  margin-top: 15px;
  font-size: 1.04rem;
}
.testimonial-role {
  color: #f7c873;
  font-size: 0.98rem;
  margin-top: 2px;
}
</style>
</head>
<body>
<?php include 'includes/navigation.php'; ?>

<div class="container-box">
  <h1>آراء العملاء</h1>
  <div class="row">
    <?php
    // مصفوفة الآراء (يمكنك زيادة أو تعديل المحتوى هنا)
    $testimonials = [
      [
        "msg" => "المنصة وفرت لي استشارة قانونية عاجلة خلال أقل من 24 ساعة، كل الاحترام للمحامين.",
        "name" => "فراس شاكر",
        "role" => "موظف حكومي",
        "icon" => "alarm"
      ],
      [
        "msg" => "بعد التسجيل حصلت على شرح واضح لخطوات قضيتي. فريق الدعم كان متعاونًا جدًا.",
        "name" => "سهى محمد",
        "role" => "ربة منزل",
        "icon" => "people"
      ],
      [
        "msg" => "دفع الأتعاب كان واضحًا وسهلًا. حصلت على إيصال رسمي بعد الدفع فورًا.",
        "name" => "باسم الجبوري",
        "role" => "صاحب شركة",
        "icon" => "credit-card"
      ],
      [
        "msg" => "أعجبني وضوح الأسعار قبل إرسال الاستشارة، ولم أتعرض لأي تكاليف مخفية.",
        "name" => "زهراء عبد الرضا",
        "role" => "طالبة جامعية",
        "icon" => "currency-dollar"
      ],
      [
        "msg" => "تلقيت رداً قانونياً مفصلاً ومرفقاً بنصوص قانونية عراقية تخص قضيتي.",
        "name" => "علي المالكي",
        "role" => "تاجر",
        "icon" => "file-earmark-text"
      ],
      [
        "msg" => "الأمانة والسرية في المنصة جعلتني أثق بالتعامل معهم بكل راحة.",
        "name" => "شهد عبد المهدي",
        "role" => "موظفة قطاع خاص",
        "icon" => "shield-lock"
      ],
      [
        "msg" => "الرد على استشارتي كان سريعًا جدًا مع دعم كامل للمرفقات والوثائق.",
        "name" => "سلمان حسين",
        "role" => "طالب دراسات عليا",
        "icon" => "envelope-arrow-up"
      ],
      [
        "msg" => "أوصي المنصة لكل من يحتاج لدعم قانوني فوري وموثوق في العراق.",
        "name" => "ابتسام كاظم",
        "role" => "أم لثلاثة أطفال",
        "icon" => "hand-thumbs-up"
      ],
      [
        "msg" => "استشارة المحامي عبر المنصة كانت أفضل من الذهاب للمكتب وأكثر توفيرًا للوقت.",
        "name" => "عبدالله صادق",
        "role" => "مهندس مدني",
        "icon" => "clock-history"
      ],
      [
        "msg" => "التنقل في الموقع سهل جدًا، وجدت كل ما أحتاجه من خدمات قانونية في دقائق.",
        "name" => "آمنة جبار",
        "role" => "معلمة",
        "icon" => "globe"
      ],
      [
        "msg" => "طلبت رأي قانوني وتم تزويدي بكل المستندات والنماذج المطلوبة بسهولة.",
        "name" => "سعد عدنان",
        "role" => "صاحب مكتب عقاري",
        "icon" => "files"
      ],
      [
        "msg" => "فريق المحامين يتمتع بكفاءة عالية ومعرفة تفصيلية بقوانين العراق.",
        "name" => "ميساء علوان",
        "role" => "محامية متدربة",
        "icon" => "journal-bookmark"
      ],
      [
        "msg" => "منصة موثوقة وأسعارها عادلة مقارنة بالخدمات المقدمة.",
        "name" => "مهند نوري",
        "role" => "موظف في البنك",
        "icon" => "badge-ad"
      ],
      [
        "msg" => "وفرت لي المنصة إجابة سريعة ساعدتني في اتخاذ قرار مهم بشأن قضيتي.",
        "name" => "ياسمين طالب",
        "role" => "صاحبة أعمال حرة",
        "icon" => "check-circle"
      ],
      [
        "msg" => "الدعم الفني متعاون وساعدني في إكمال الطلب بدون أي تعقيد.",
        "name" => "كرار عباس",
        "role" => "مبرمج",
        "icon" => "headset"
      ],
      [
        "msg" => "المنصة أرسلت لي إشعارًا بكل تحديث على الاستشارة حتى استلام الرد النهائي.",
        "name" => "صفاء هادي",
        "role" => "معلم",
        "icon" => "bell"
      ],
      [
        "msg" => "الخدمات متاحة على مدار الساعة ووجدت الإجابة حتى في يوم العطلة.",
        "name" => "هناء عبد الواحد",
        "role" => "ممرضة",
        "icon" => "calendar-check"
      ],
      [
        "msg" => "حصلت على موعد مع محامي متخصص بسهولة وبدون عناء الانتظار.",
        "name" => "محمد راضي",
        "role" => "طالب ثانوي",
        "icon" => "calendar-event"
      ],
      [
        "msg" => "أعجبتني ميزة إرفاق الملفات والاستشارات الإلكترونية مباشرة عبر الموقع.",
        "name" => "نور حامد",
        "role" => "مصممة جرافيك",
        "icon" => "cloud-arrow-up"
      ],
      [
        "msg" => "هذه أول تجربة لي في الاستشارات القانونية الإلكترونية وكانت ممتازة بكل المقاييس.",
        "name" => "سلمان عليوي",
        "role" => "موظف بلدية",
        "icon" => "emoji-laughing"
      ],
      [
        "msg" => "شكرًا على المصداقية وسرعة التواصل. أوصي الجميع بالمنصة.",
        "name" => "إيلاف علاء",
        "role" => "مدرسة لغة عربية",
        "icon" => "star"
      ]
    ];

    foreach ($testimonials as $t):
    ?>
      <div class="col-md-4">
        <div class="testimonial-card">
          <div class="icon-bubble"><i class="bi bi-<?= $t['icon'] ?>"></i></div>
          <div class="testimonial-message"><?= htmlspecialchars($t['msg']) ?></div>
          <div class="testimonial-author"><?= htmlspecialchars($t['name']) ?></div>
          <div class="testimonial-role"><?= htmlspecialchars($t['role']) ?></div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>

<footer class="text-center py-3" style="background:linear-gradient(135deg, #192846, #f7c873);color:#192846;font-weight:600;">
    © 2025 حقك تعرف. جميع الحقوق محفوظة.
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

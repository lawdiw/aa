<?php
session_start();
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>الأسئلة الشائعة - حقك تعرف</title>
<link href="https://fonts.googleapis.com/css2?family=Cairo&family=Tajawal&display=swap" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" />
<style>
:root {
  --bg-main: #11213a;
  --box-bg: #192846;
  --primary-color: #f7c873;
  --text-light: #ffffff;
}
body {
  background-color: var(--bg-main);
  color: var(--text-light);
  font-family: 'Cairo', 'Tajawal', sans-serif;
  min-height: 100vh;
  margin: 0; padding: 0 10px 40px 10px;
}
h1 {
  color: var(--primary-color);
  font-weight: 700;
  text-align: center;
  margin: 38px 0 20px 0;
  letter-spacing: 1px;
}
h2.section-title {
  color: var(--primary-color);
  margin-top: 40px;
  margin-bottom: 15px;
  font-weight: 700;
  border-bottom: 2px solid var(--primary-color);
  padding-bottom: 6px;
}
.container-faq {
  background-color: var(--box-bg);
  border-radius: 18px;
  padding: 2.5rem 1.3rem;
  max-width: 900px;
  margin: 0 auto 50px auto;
  box-shadow: 0 4px 24px #0006;
}
.accordion-item {
  background: #203456;
  border: none;
  border-radius: 12px;
  margin-bottom: 13px;
  box-shadow: 0 2px 7px #0002;
}
.accordion-button {
  background: none;
  color: var(--primary-color);
  font-size: 1.1rem;
  font-weight: 700;
  border-radius: 12px !important;
  box-shadow: none;
  transition: background 0.2s;
}
.accordion-button:not(.collapsed) {
  background: #16253c;
  color: #ffe4a7;
}
.accordion-body {
  background: none;
  color: var(--text-light);
  font-size: 1rem;
  border-radius: 0 0 12px 12px;
}
.faq-legal-ref {
  color: #ffe194;
  font-weight: 700;
  margin-top: 6px;
  display: block;
}
#faqSearch {
  width: 100%;
  max-width: 400px;
  margin: 0 auto 30px auto;
  padding: 10px 15px;
  border-radius: 8px;
  border: 2px solid var(--primary-color);
  background-color: var(--box-bg);
  color: var(--text-light);
  font-size: 1rem;
  outline: none;
  transition: border-color 0.3s;
}
#faqSearch:focus {
  border-color: var(--btn-bg-hover);
}
.buttons-control {
  text-align: center;
  margin-bottom: 20px;
}
.btn-control {
  background: var(--btn-bg);
  border: none;
  color: var(--text-light);
  padding: 8px 22px;
  font-weight: 700;
  margin: 0 6px;
  border-radius: 8px;
  cursor: pointer;
  transition: background 0.3s;
}
.btn-control:hover {
  background: var(--btn-bg-hover);
}
@media (max-width: 600px) {
  .container-faq { padding: 1.3rem 0.4rem; }
  h1 { font-size: 1.5rem; }
}
</style>
</head>
<body>
<?php include 'includes/navigation.php'; ?>

<div class="container-faq">
  <h1><i class="bi bi-question-circle-fill"></i> الأسئلة الشائعة</h1>

  <!-- بحث سريع -->
  <input type="text" id="faqSearch" placeholder="ابحث في الأسئلة...">

  <!-- أزرار التحكم بالتوسيع والطي -->
  <div class="buttons-control">
    <button class="btn-control" id="expandAllBtn">توسيع الكل</button>
    <button class="btn-control" id="collapseAllBtn">طي الكل</button>
  </div>

  <!-- قسم الأحوال الشخصية -->
  <h2 class="section-title">أسئلة عن الأحوال الشخصية</h2>
  <div class="accordion" id="faqAccordion">

    <div class="accordion-item" data-search-terms="نفقة الزوجة حالات الزوجة">
      <h2 class="accordion-header" id="faq1-header">
        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq1" aria-expanded="false" aria-controls="faq1">
          ما هي الحالات التي تستحق فيها الزوجة النفقة؟ وما سندها القانوني؟
        </button>
      </h2>
      <div id="faq1" class="accordion-collapse collapse" aria-labelledby="faq1-header" data-bs-parent="#faqAccordion">
        <div class="accordion-body">
          تستحق الزوجة النفقة على زوجها إذا كان عقد الزواج صحيحًا ولم تكن ناشزًا (خارجة عن طاعته دون سبب مشروع).
          <span class="faq-legal-ref">المادة (23) من قانون الأحوال الشخصية العراقي رقم 188 لسنة 1959 المعدل.</span>
        </div>
      </div>
    </div>

    <div class="accordion-item" data-search-terms="طلاق نفقة الزوجة">
      <h2 class="accordion-header" id="faq2-header">
        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2" aria-expanded="false" aria-controls="faq2">
          هل يجوز للزوجة طلب الطلاق إذا لم ينفق الزوج عليها؟
        </button>
      </h2>
      <div id="faq2" class="accordion-collapse collapse" aria-labelledby="faq2-header" data-bs-parent="#faqAccordion">
        <div class="accordion-body">
          نعم، للزوجة الحق في طلب التفريق للضرر إذا امتنع الزوج عن الإنفاق عليها دون عذر مشروع.
          <span class="faq-legal-ref">المادة (40/1) من قانون الأحوال الشخصية العراقي رقم 188 لسنة 1959 المعدل.</span>
        </div>
      </div>
    </div>

    <div class="accordion-item" data-search-terms="نسب إثبات النسب القانون العراقي">
      <h2 class="accordion-header" id="faq3-header">
        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3" aria-expanded="false" aria-controls="faq3">
          ما هي شروط إثبات النسب في القانون العراقي؟
        </button>
      </h2>
      <div id="faq3" class="accordion-collapse collapse" aria-labelledby="faq3-header" data-bs-parent="#faqAccordion">
        <div class="accordion-body">
          يثبت النسب بالفراش أو بالإقرار أو بالبينة (الشهود أو الوثائق الرسمية).
          <span class="faq-legal-ref">المواد (51 - 52) من قانون الأحوال الشخصية العراقي رقم 188 لسنة 1959.</span>
        </div>
      </div>
    </div>

    <div class="accordion-item" data-search-terms="حضانة سن الحضانة">
      <h2 class="accordion-header" id="faq4-header">
        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4" aria-expanded="false" aria-controls="faq4">
          ما هو سن الحضانة في العراق؟
        </button>
      </h2>
      <div id="faq4" class="accordion-collapse collapse" aria-labelledby="faq4-header" data-bs-parent="#faqAccordion">
        <div class="accordion-body">
          تنتهي حضانة الأم للطفل ببلوغه سن العاشرة، وللمحكمة أن تقرر تمديد الحضانة حتى سن الخامسة عشرة إذا رأت مصلحة في ذلك.
          <span class="faq-legal-ref">المادة (57/1) من قانون الأحوال الشخصية رقم 188 لسنة 1959.</span>
        </div>
      </div>
    </div>

    <div class="accordion-item" data-search-terms="زواج شروط الزواج قانون">
      <h2 class="accordion-header" id="faq5-header">
        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq5" aria-expanded="false" aria-controls="faq5">
          ما هي شروط الزواج في القانون العراقي؟
        </button>
      </h2>
      <div id="faq5" class="accordion-collapse collapse" aria-labelledby="faq5-header" data-bs-parent="#faqAccordion">
        <div class="accordion-body">
          يشترط في الزواج: الرضا، الأهلية، عدم وجود موانع شرعية أو قانونية، وتسجيل العقد في المحكمة المختصة.
          <span class="faq-legal-ref">المواد (6) و(7) من قانون الأحوال الشخصية رقم 188 لسنة 1959.</span>
        </div>
      </div>
    </div>

  </div>

  <!-- قسم القانون المدني -->
  <h2 class="section-title">أسئلة عن القانون المدني</h2>
  <div class="accordion" id="faqAccordion2">

    <div class="accordion-item" data-search-terms="هبة عطية رجوع قانون مدني">
      <h2 class="accordion-header" id="faq6-header">
        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq6" aria-expanded="false" aria-controls="faq6">
          هل يجوز الرجوع في الهبة أو العطية في القانون المدني العراقي؟
        </button>
      </h2>
      <div id="faq6" class="accordion-collapse collapse" aria-labelledby="faq6-header" data-bs-parent="#faqAccordion2">
        <div class="accordion-body">
          الأصل أنه لا يجوز للواهب الرجوع في الهبة بعد إتمامها، إلا في حالات معينة يقرها القانون.
          <span class="faq-legal-ref">المادة (594) من القانون المدني العراقي رقم 40 لسنة 1951.</span>
        </div>
      </div>
    </div>

    <div class="accordion-item" data-search-terms="مدة سقوط التقادم تعويض ضرر">
      <h2 class="accordion-header" id="faq7-header">
        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq7" aria-expanded="false" aria-controls="faq7">
          ما هي المدة القانونية لسقوط دعوى التعويض عن الضرر؟
        </button>
      </h2>
      <div id="faq7" class="accordion-collapse collapse" aria-labelledby="faq7-header" data-bs-parent="#faqAccordion2">
        <div class="accordion-body">
          تسقط دعوى التعويض عن الضرر بمرور ثلاث سنوات من اليوم الذي علم فيه المتضرر بالضرر وبالمسؤول عنه، وتسقط على كل حال بمرور خمس عشرة سنة من وقوع العمل غير المشروع.
          <span class="faq-legal-ref">المادة (232) من القانون المدني العراقي رقم 40 لسنة 1951.</span>
        </div>
      </div>
    </div>

    <div class="accordion-item" data-search-terms="حجز احتياطي حجز تنفيذي قانون">
      <h2 class="accordion-header" id="faq8-header">
        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq8" aria-expanded="false" aria-controls="faq8">
          ما الفرق بين الحجز الاحتياطي والحجز التنفيذي؟
        </button>
      </h2>
      <div id="faq8" class="accordion-collapse collapse" aria-labelledby="faq8-header" data-bs-parent="#faqAccordion2">
        <div class="accordion-body">
          الحجز الاحتياطي إجراء وقائي لمنع التصرف بالمال محل النزاع، أما الحجز التنفيذي فهو لتنفيذ حكم المحكمة باستيفاء حق الدائن.
          <span class="faq-legal-ref">المواد (231) وما بعدها من قانون المرافعات المدنية رقم 83 لسنة 1969.</span>
        </div>
      </div>
    </div>

    <div class="accordion-item" data-search-terms="تصالح قضايا مدنية">
      <h2 class="accordion-header" id="faq9-header">
        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq9" aria-expanded="false" aria-controls="faq9">
          هل يجوز التصالح في القضايا المدنية؟
        </button>
      </h2>
      <div id="faq9" class="accordion-collapse collapse" aria-labelledby="faq9-header" data-bs-parent="#faqAccordion2">
        <div class="accordion-body">
          نعم، يجوز للخصوم التصالح في أي مرحلة من مراحل الدعوى المدنية.
          <span class="faq-legal-ref">المادة (58) من قانون المرافعات المدنية رقم 83 لسنة 1969.</span>
        </div>
      </div>
    </div>

    <div class="accordion-item" data-search-terms="تقادم حق مطالبة دين">
      <h2 class="accordion-header" id="faq10-header">
        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq10" aria-expanded="false" aria-controls="faq10">
          متى يسقط الحق في المطالبة بالدين (التقادم)؟
        </button>
      </h2>
      <div id="faq10" class="accordion-collapse collapse" aria-labelledby="faq10-header" data-bs-parent="#faqAccordion2">
        <div class="accordion-body">
          تختلف مدة التقادم حسب نوع الدين، لكن بشكل عام تسقط أغلب الديون بمرور 15 سنة دون مطالبة رسمية أو إجراء قانوني من الدائن، إلا إذا كان هناك اتفاق أو نص خاص.
          <span class="faq-legal-ref">المادة (193) من قانون أصول المحاكمات المدنية رقم 83 لسنة 1969.</span>
        </div>
      </div>
    </div>

  </div>

  <!-- قسم التحقيقات والإجراءات -->
  <h2 class="section-title">أسئلة عن التحقيقات والإجراءات القانونية</h2>
  <div class="accordion" id="faqAccordion3">

    <div class="accordion-item" data-search-terms="تحقيق إداري موظف عقوبة اعتراض">
      <h2 class="accordion-header" id="faq11-header">
        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq11" aria-expanded="false" aria-controls="faq11">
          ما هي إجراءات التحقيق الإداري مع الموظف؟
        </button>
      </h2>
      <div id="faq11" class="accordion-collapse collapse" aria-labelledby="faq11-header" data-bs-parent="#faqAccordion3">
        <div class="accordion-body">
          يجب على الجهة الإدارية فتح تحقيق رسمي مع الموظف المخالف قبل توقيع أي عقوبة، ويجب تبليغ الموظف بالتهم وتمكينه من الدفاع عن نفسه.
          <span class="faq-legal-ref">المادة (10) من قانون انضباط موظفي الدولة والقطاع العام رقم 14 لسنة 1991 المعدل.</span>
        </div>
      </div>
    </div>

    <div class="accordion-item" data-search-terms="اعتراض قرار عقوبة إدارية موظف">
      <h2 class="accordion-header" id="faq12-header">
        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq12" aria-expanded="false" aria-controls="faq12">
          هل يحق للموظف الاعتراض على قرار العقوبة الإدارية؟
        </button>
      </h2>
      <div id="faq12" class="accordion-collapse collapse" aria-labelledby="faq12-header" data-bs-parent="#faqAccordion3">
        <div class="accordion-body">
          يحق للموظف الاعتراض على القرار خلال 30 يومًا من تاريخ التبليغ أمام الجهة الإدارية الأعلى أو القضاء الإداري.
          <span class="faq-legal-ref">المادة (17) من قانون انضباط موظفي الدولة رقم 14 لسنة 1991.</span>
        </div>
      </div>
    </div>

    <div class="accordion-item" data-search-terms="حقوق المتهم تحقيق محام اعتراف إكراه">
      <h2 class="accordion-header" id="faq13-header">
        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq13" aria-expanded="false" aria-controls="faq13">
          ما هي حقوق المتهم أثناء التحقيق؟
        </button>
      </h2>
      <div id="faq13" class="accordion-collapse collapse" aria-labelledby="faq13-header" data-bs-parent="#faqAccordion3">
        <div class="accordion-body">
          للمتهم الحق في حضور محامٍ أثناء التحقيق، وعدم إجباره على الاعتراف، وعدم تعريضه لأي إكراه أو تعذيب.
          <span class="faq-legal-ref">المادة (123) من قانون أصول المحاكمات الجزائية رقم 23 لسنة 1971، والمادة (19/ثالثًا) من الدستور العراقي.</span>
        </div>
      </div>
    </div>

    <div class="accordion-item" data-search-terms="توقيف الشرطة مذكرة قبض">
      <h2 class="accordion-header" id="faq14-header">
        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq14" aria-expanded="false" aria-controls="faq14">
          ما هو الإجراء إذا تم توقيف الشخص من قبل الشرطة دون مذكرة قبض؟
        </button>
      </h2>
      <div id="faq14" class="accordion-collapse collapse" aria-labelledby="faq14-header" data-bs-parent="#faqAccordion3">
        <div class="accordion-body">
          لا يجوز توقيف أي شخص أو حبسه إلا بموجب أمر صادر عن جهة قضائية مختصة.
          <span class="faq-legal-ref">المادة (15) من الدستور العراقي لسنة 2005 والمادة (92) من قانون أصول المحاكمات الجزائية رقم 23 لسنة 1971.</span>
        </div>
      </div>
    </div>

    <div class="accordion-item" data-search-terms="اعتراض حكم استئناف محكمة">
      <h2 class="accordion-header" id="faq15-header">
        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq15" aria-expanded="false" aria-controls="faq15">
          كيف يمكن الاعتراض على حكم صادر عن محكمة أول درجة؟
        </button>
      </h2>
      <div id="faq15" class="accordion-collapse collapse" aria-labelledby="faq15-header" data-bs-parent="#faqAccordion3">
        <div class="accordion-body">
          يمكن الاعتراض على الحكم بطريق الاستئناف خلال 30 يومًا من تاريخ التبليغ أمام محكمة الاستئناف المختصة.
          <span class="faq-legal-ref">المادة (193) من قانون أصول المحاكمات المدنية رقم 83 لسنة 1969.</span>
        </div>
      </div>
    </div>

    <div class="accordion-item" data-search-terms="نسخة قرار محكمة">
      <h2 class="accordion-header" id="faq16-header">
        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq16" aria-expanded="false" aria-controls="faq16">
          هل يحق لي الحصول على نسخة من قرار المحكمة الصادر بحقي؟
        </button>
      </h2>
      <div id="faq16" class="accordion-collapse collapse" aria-labelledby="faq16-header" data-bs-parent="#faqAccordion3">
        <div class="accordion-body">
          يحق للخصوم أو وكلائهم القانونيين الحصول على نسخة رسمية من قرار المحكمة بعد دفع الرسوم المقررة.
          <span class="faq-legal-ref">المادة (56) من قانون أصول المحاكمات المدنية رقم 83 لسنة 1969.</span>
        </div>
      </div>
    </div>

    <div class="accordion-item" data-search-terms="حقوق توقيف استجواب التزام صمت">
      <h2 class="accordion-header" id="faq17-header">
        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq17" aria-expanded="false" aria-controls="faq17">
          ما هي حقوق المواطن عند توقيفه أو استجوابه؟
        </button>
      </h2>
      <div id="faq17" class="accordion-collapse collapse" aria-labelledby="faq17-header" data-bs-parent="#faqAccordion3">
        <div class="accordion-body">
          للمواطن حق معرفة سبب الاستجواب، والحق في التزام الصمت، وعدم توقيع أي أقوال إلا بحضور محامٍ، ويجب عدم تعريضه لأي إكراه أو تهديد.
          <span class="faq-legal-ref">المادة (19/ثالثًا) من الدستور العراقي، والمادة (123) من قانون أصول المحاكمات الجزائية.</span>
        </div>
      </div>
    </div>

    <div class="accordion-item" data-search-terms="شكوى موظف حكومي جهة رقابية">
      <h2 class="accordion-header" id="faq18-header">
        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq18" aria-expanded="false" aria-controls="faq18">
          كيف يمكن تقديم شكوى ضد موظف حكومي؟
        </button>
      </h2>
      <div id="faq18" class="accordion-collapse collapse" aria-labelledby="faq18-header" data-bs-parent="#faqAccordion3">
        <div class="accordion-body">
          يمكن تقديم شكوى إلى الجهات الرقابية (مثل هيئة النزاهة أو ديوان الرقابة المالية)، أو التظلم أمام الدائرة ذاتها، أو رفع دعوى إدارية أمام محكمة القضاء الإداري.
          <span class="faq-legal-ref">المواد 11، 12 من قانون النزاهة رقم 30 لسنة 2004.</span>
        </div>
      </div>
    </div>

    <div class="accordion-item" data-search-terms="فصل تعسفي عامل حقوق تعويض">
      <h2 class="accordion-header" id="faq19-header">
        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq19" aria-expanded="false" aria-controls="faq19">
          ما هي حقوق العامل في حالة الفصل التعسفي؟
        </button>
      </h2>
      <div id="faq19" class="accordion-collapse collapse" aria-labelledby="faq19-header" data-bs-parent="#faqAccordion3">
        <div class="accordion-body">
          يحق للعامل الطعن في قرار الفصل التعسفي أمام المحاكم العمالية، والحصول على تعويض أو إعادة التعيين.
          <span class="faq-legal-ref">المادة (76) من قانون العمل رقم 71 لسنة 1987.</span>
        </div>
      </div>
    </div>

    <div class="accordion-item" data-search-terms="عقوبات جزائية إعدام سجن غرامة">
      <h2 class="accordion-header" id="faq20-header">
        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq20" aria-expanded="false" aria-controls="faq20">
          ما هي أنواع العقوبات الجزائية في القانون العراقي؟
        </button>
      </h2>
      <div id="faq20" class="accordion-collapse collapse" aria-labelledby="faq20-header" data-bs-parent="#faqAccordion3">
        <div class="accordion-body">
          تشمل العقوبات: الإعدام، السجن المؤبد، السجن المؤقت، الغرامة، والإجراءات الاحترازية.
          <span class="faq-legal-ref">المواد 29-41 من قانون العقوبات العراقي رقم 111 لسنة 1969.</span>
        </div>
      </div>
    </div>

    <div class="accordion-item" data-search-terms="قبول دعوى مصلحة قانونية تقادم">
      <h2 class="accordion-header" id="faq21-header">
        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq21" aria-expanded="false" aria-controls="faq21">
          ما هي شروط قبول الدعوى المدنية؟
        </button>
      </h2>
      <div id="faq21" class="accordion-collapse collapse" aria-labelledby="faq21-header" data-bs-parent="#faqAccordion3">
        <div class="accordion-body">
          يجب أن يكون للمدعي مصلحة شخصية، وأن تكون الدعوى غير محرمة قانونيًا، وأن يقدمها خلال مدة التقادم.
          <span class="faq-legal-ref">المواد 11-13 من قانون أصول المحاكمات المدنية رقم 83 لسنة 1969.</span>
        </div>
      </div>
    </div>

    <div class="accordion-item" data-search-terms="تسجيل عقد بيع عقار رسمي">
      <h2 class="accordion-header" id="faq22-header">
        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq22" aria-expanded="false" aria-controls="faq22">
          كيف يتم تسجيل عقد بيع عقار رسميًا؟
        </button>
      </h2>
      <div id="faq22" class="accordion-collapse collapse" aria-labelledby="faq22-header" data-bs-parent="#faqAccordion3">
        <div class="accordion-body">
          يتم مراجعة دائرة التسجيل العقاري، التحقق من سلامة الملكية، وتقديم العقد مصدقًا عليه مع دفع الرسوم المطلوبة.
          <span class="faq-legal-ref">المادة (8) من قانون التسجيل العقاري رقم 30 لسنة 1975.</span>
        </div>
      </div>
    </div>

    <div class="accordion-item" data-search-terms="طعن قرار إداري جهة حكومية">
      <h2 class="accordion-header" id="faq23-header">
        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq23" aria-expanded="false" aria-controls="faq23">
          هل يمكن الطعن في قرار إداري صادر من جهة حكومية؟
        </button>
      </h2>
      <div id="faq23" class="accordion-collapse collapse" aria-labelledby="faq23-header" data-bs-parent="#faqAccordion3">
        <div class="accordion-body">
          نعم، يمكن الطعن أمام محكمة القضاء الإداري خلال 60 يومًا من تاريخ التبليغ.
          <span class="faq-legal-ref">المادة (34) من قانون محاكم القضاء الإداري رقم 71 لسنة 2000.</span>
        </div>
      </div>
    </div>

    <div class="accordion-item" data-search-terms="حقوق المرأة قانون العمل حماية">
      <h2 class="accordion-header" id="faq24-header">
        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq24" aria-expanded="false" aria-controls="faq24">
          ما هي حقوق المرأة في قانون العمل العراقي؟
        </button>
      </h2>
      <div id="faq24" class="accordion-collapse collapse" aria-labelledby="faq24-header" data-bs-parent="#faqAccordion3">
        <div class="accordion-body">
          تتمتع المرأة بنفس حقوق الرجل في العمل، وتشمل حماية من الفصل التعسفي، إجازات أمومة، وظروف عمل ملائمة.
          <span class="faq-legal-ref">المادة (10) من قانون العمل رقم 71 لسنة 1987.</span>
        </div>
      </div>
    </div>

    <div class="accordion-item" data-search-terms="حقوق الطفل قانون حماية اتفاقية">
      <h2 class="accordion-header" id="faq25-header">
        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq25" aria-expanded="false" aria-controls="faq25">
          ما هي حقوق الطفل في القانون العراقي؟
        </button>
      </h2>
      <div id="faq25" class="accordion-collapse collapse" aria-labelledby="faq25-header" data-bs-parent="#faqAccordion3">
        <div class="accordion-body">
          للطفل حقوق في الحماية، التعليم، الصحة، والعيش في بيئة آمنة حسب اتفاقية حقوق الطفل التي صدق عليها العراق.
          <span class="faq-legal-ref">اتفاقية حقوق الطفل لعام 1989، والقانون العراقي لحماية الطفل رقم 126 لسنة 1980.</span>
        </div>
      </div>
    </div>

  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// وظيفة البحث الحي داخل الأسئلة
const faqSearchInput = document.getElementById('faqSearch');
const accordionItems = document.querySelectorAll('.accordion-item');

faqSearchInput.addEventListener('input', () => {
  const query = faqSearchInput.value.trim().toLowerCase();

  accordionItems.forEach(item => {
    const text = item.getAttribute('data-search-terms') || item.textContent.toLowerCase();
    if (text.includes(query)) {
      item.style.display = '';
    } else {
      item.style.display = 'none';
    }
  });
});

// أزرار توسيع وطي الكل
const expandAllBtn = document.getElementById('expandAllBtn');
const collapseAllBtn = document.getElementById('collapseAllBtn');

expandAllBtn.addEventListener('click', () => {
  accordionItems.forEach(item => {
    const collapseEl = bootstrap.Collapse.getOrCreateInstance(item.querySelector('.accordion-collapse'));
    collapseEl.show();
  });
});

collapseAllBtn.addEventListener('click', () => {
  accordionItems.forEach(item => {
    const collapseEl = bootstrap.Collapse.getOrCreateInstance(item.querySelector('.accordion-collapse'));
    collapseEl.hide();
  });
});
</script>
</body>
</html>

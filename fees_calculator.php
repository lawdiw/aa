<?php
session_start();
require_once 'includes/db_connect.php';

// جلب أنواع الاستشارات
$stmt = $db->query("SELECT subtype, name, min_fee FROM fees WHERE type='consultation'");
$consultation_fees = $stmt->fetchAll(PDO::FETCH_ASSOC);

// جلب أنواع الضرائب
$stmt2 = $db->query("SELECT subtype, name, percent_fixed FROM fees WHERE type='tax'");
$tax_fees = $stmt2->fetchAll(PDO::FETCH_ASSOC);

// جلب أنواع رسوم الدعاوى
$stmt3 = $db->query("SELECT subtype, name, percent_fixed FROM fees WHERE type='case_fee'");
$case_fees = $stmt3->fetchAll(PDO::FETCH_ASSOC);

// جلب أنواع رسوم التسجيل العقاري
$stmt4 = $db->query("SELECT subtype, name, percent_fixed FROM fees WHERE type='property_fee'");
$property_fees = $stmt4->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>حاسبة الأتعاب والرسوم والضرائب - حقك تعرف</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        body {background: #11213a; color: #fff; font-family: 'Cairo', Arial, Tahoma, sans-serif;}
        .container-box {background-color: #192846; border-radius: 20px; padding: 2.5rem; max-width: 1100px; margin: 40px auto; box-shadow: 0 6px 30px #0006;}
        h1, h2 {color: #f7c873; font-weight: bold; text-align: center; margin-bottom: 30px; letter-spacing: 1px;}
        .tab-content {margin-top: 25px;}
        label {color: #f7c873; font-weight: 600;}
        .form-control, .form-select {background: #243455; color: #fff; border-radius: 8px; border: 1px solid #32416a;}
        .form-control:focus, .form-select:focus {background: #233054; border-color: #f7c873; color: #fff;}
        .result-box {background: #12213b; border-radius: 12px; padding: 1rem; margin-top: 1.2rem; font-size: 1.2rem; color: #fff; box-shadow: 0 3px 10px #0004; text-align: center;}
        .nav-tabs .nav-link.active {background: linear-gradient(90deg, #237cbf, #2ec5a3); color: #fff; border: none;}
        .nav-tabs .nav-link {border: none; color: #f7c873;}
        .law-ref {font-size: .98rem; color: #ffd076; margin-top: .6rem;}
        .btn-law {font-size: .95rem; color: #233054; background: #f7c873; border-radius: 8px;}
        .note {background: #243455; color: #f7c873; border-radius: 8px; padding: 8px 12px; margin: 10px 0; font-size: .95rem;}
        .alert-warning {background: #fff8e1; color: #946200; border: none;}
        @media (max-width: 767px) {.container-box {padding: 1rem;}}
    </style>
</head>
<body>
<?php include 'includes/navigation.php'; ?>
<div class="container-box">
    <h1><i class="bi bi-currency-exchange"></i> حاسبة الأتعاب والرسوم والضرائب</h1>
    <ul class="nav nav-tabs justify-content-center" id="feeTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="lawyer-fee-tab" data-bs-toggle="tab" data-bs-target="#lawyer-fee" type="button" role="tab" aria-controls="lawyer-fee" aria-selected="true">
                أتعاب المحامي
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="case-fee-tab" data-bs-toggle="tab" data-bs-target="#case-fee" type="button" role="tab" aria-controls="case-fee" aria-selected="false">
                رسوم الدعاوى
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="property-fee-tab" data-bs-toggle="tab" data-bs-target="#property-fee" type="button" role="tab" aria-controls="property-fee" aria-selected="false">
                رسوم التسجيل العقاري
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="tax-fee-tab" data-bs-toggle="tab" data-bs-target="#tax-fee" type="button" role="tab" aria-controls="tax-fee" aria-selected="false">
                الضرائب العراقية
            </button>
        </li>
    </ul>
    <div class="tab-content" id="feeTabContent">
        <!-- أتعاب المحامي -->
        <div class="tab-pane fade show active" id="lawyer-fee" role="tabpanel" aria-labelledby="lawyer-fee-tab">
            <div class="note">
                تعليمات نقابة المحامين العراقيين لسنة 2021: لا يجوز أن تقل الأتعاب عن <b>250,000</b> ولا تزيد عن <b>10%</b> من قيمة القضية (للقضايا التجارية والعقارية الكبيرة).
            </div>
            <form id="lawyerFeeForm" class="row g-3 mt-2">
                <div class="col-12 col-md-6">
                    <label>نوع الأتعاب</label>
                    <select class="form-select" name="fee_type" id="fee_type" required onchange="toggleLawyerFeeType()">
                        <option value="percent">نسبة مئوية من قيمة القضية (5% - 10%)</option>
                        <option value="fixed">مبلغ ثابت (ترافع/عقد – لا يقل عن 250,000)</option>
                        <option value="consultation">استشارة قانونية حول قضية/دعوى</option>
                    </select>
                </div>
                <div class="col-12 col-md-6" id="percentAmountDiv">
                    <label>قيمة القضية (دينار عراقي)</label>
                    <input type="number" min="0" step="1000" class="form-control" name="case_amount" id="case_amount" required>
                </div>
                <div class="col-12 col-md-6 d-none" id="fixedFeeDiv">
                    <label>مبلغ الأتعاب المتفق عليه (دينار عراقي)</label>
                    <input type="number" min="250000" step="1000" class="form-control" name="fixed_fee" id="fixed_fee">
                </div>
                <div class="col-12 col-md-6" id="percentInputDiv">
                    <label>نسبة الأتعاب (%)</label>
                    <input type="number" min="5" max="10" step="0.1" class="form-control" name="percent" id="percent" value="5" required>
                </div>
                <!-- استشارة القضايا (ديناميكي) -->
                <div class="col-12 col-md-6 d-none" id="consultationTypeDiv">
                    <label>نوع القضية/الدعوى للاستشارة</label>
                    <select class="form-select" name="consult_case_type" id="consult_case_type">
                        <?php foreach($consultation_fees as $row): ?>
                            <option value="<?= htmlspecialchars($row['subtype']) ?>">
                                <?= htmlspecialchars($row['name']) ?> (<?= number_format($row['min_fee']) ?> د.ع)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-12">
                    <button type="button" class="btn btn-success w-100" onclick="calcLawyerFee()">احتساب الأتعاب</button>
                </div>
            </form>
            <div class="law-ref">
                <b>أتعاب الاستشارة القانونية</b> تحدد بموجب <b>تعليمات نقابة المحامين العراقيين لسنة 2021، المادة (18)</b>.
                <a href="http://www.iraqalaws.gov.iq/LoadLawBook.aspx?SC=220220211134322" target="_blank" style="color:#46e6b3;">مصدر التعليمات الرسمية</a>
            </div>
            <div id="lawyerFeeResult" class="result-box" style="display:none;"></div>
        </div>
        <!-- رسوم الدعاوى (ديناميكي) -->
        <div class="tab-pane fade" id="case-fee" role="tabpanel" aria-labelledby="case-fee-tab">
            <form id="caseFeeForm" class="row g-3 mt-2">
                <div class="col-12 col-md-7">
                    <label>نوع الدعوى</label>
                    <select class="form-select" name="case_type" id="case_type" required>
                        <?php foreach($case_fees as $row): ?>
                            <option value="<?= htmlspecialchars($row['subtype']) ?>">
                                <?= htmlspecialchars($row['name']) ?> (<?= $row['percent_fixed'] ?>%)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-12 col-md-5">
                    <label>قيمة الدعوى (دينار عراقي)</label>
                    <input type="number" min="0" step="1000" class="form-control" name="case_value" id="case_value" required>
                </div>
                <div class="col-12">
                    <button type="button" class="btn btn-warning w-100" onclick="calcCaseFee()">احتساب الرسوم</button>
                </div>
            </form>
            <div id="caseFeeResult" class="result-box" style="display:none;"></div>
        </div>
        <!-- رسوم التسجيل العقاري (ديناميكي) -->
        <div class="tab-pane fade" id="property-fee" role="tabpanel" aria-labelledby="property-fee-tab">
            <form id="propertyFeeForm" class="row g-3 mt-2">
                <div class="col-12 col-md-7">
                    <label>قيمة العقار (دينار عراقي)</label>
                    <input type="number" min="0" step="1000" class="form-control" name="property_value" id="property_value" required>
                </div>
                <div class="col-12 col-md-5">
                    <label>نوع العملية</label>
                    <select class="form-select" name="property_type" id="property_type" required>
                        <?php foreach($property_fees as $row): ?>
                            <option value="<?= htmlspecialchars($row['subtype']) ?>">
                                <?= htmlspecialchars($row['name']) ?> (<?= $row['percent_fixed'] ?>%)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-12">
                    <label>رسوم كشف/شهادة عقارية إضافية (اختياري)</label>
                    <input type="number" min="0" step="1000" class="form-control" name="extra_fee" id="extra_fee" placeholder="أدخل قيمة الرسوم الإضافية (إن وجدت)">
                </div>
                <div class="col-12">
                    <button type="button" class="btn btn-info w-100" onclick="calcPropertyFee()">احتساب رسوم التسجيل</button>
                </div>
            </form>
            <div id="propertyFeeResult" class="result-box" style="display:none;"></div>
        </div>
        <!-- الضرائب العراقية (ديناميكي) -->
        <div class="tab-pane fade" id="tax-fee" role="tabpanel" aria-labelledby="tax-fee-tab">
            <form id="taxFeeForm" class="row g-3 mt-2">
                <div class="col-12 col-md-6">
                    <label>نوع الضريبة</label>
                    <select class="form-select" name="tax_type" id="tax_type" required onchange="toggleTaxType()">
                        <?php foreach($tax_fees as $row): ?>
                            <option value="<?= htmlspecialchars($row['subtype']) ?>">
                                <?= htmlspecialchars($row['name']) ?><?= $row['percent_fixed'] ? ' ('.$row['percent_fixed'].'%)' : '' ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-12 col-md-6" id="tax_amount_div">
                    <label id="tax_amount_label">القيمة الخاضعة للضريبة (دينار عراقي)</label>
                    <input type="number" min="0" step="1000" class="form-control" name="tax_amount" id="tax_amount" required>
                </div>
                <div class="col-12">
                    <button type="button" class="btn btn-danger w-100" onclick="calcTaxFee()">احتساب الضريبة</button>
                </div>
            </form>
            <div id="taxFeeResult" class="result-box" style="display:none;"></div>
        </div>
    </div>
    <div class="alert alert-warning text-center mt-4" style="font-size:1.1rem; border-radius:14px;">
        <i class="bi bi-exclamation-triangle-fill"></i>
        جميع الأتعاب والرسوم والضرائب المذكورة في هذه الصفحة تقريبية وقابلة للتغيير بحسب التشريعات والتعليمات، وحسب حالة كل قضية أو معاملة. يجب مراجعة المحامي المختص أو الدائرة الرسمية المختصة للحصول على القيمة الدقيقة لحالتك.
    </div>
</div>
<script>
    // حقن القيم من PHP لجافاسكريبت
    var consultationFees = <?php echo json_encode($consultation_fees); ?>;
    var taxFees = <?php echo json_encode($tax_fees); ?>;
    var caseFees = <?php echo json_encode($case_fees); ?>;
    var propertyFees = <?php echo json_encode($property_fees); ?>;

    function toggleLawyerFeeType() {
        var feeType = document.getElementById('fee_type').value;
        document.getElementById('percentAmountDiv').classList.add('d-none');
        document.getElementById('fixedFeeDiv').classList.add('d-none');
        document.getElementById('percentInputDiv').classList.add('d-none');
        document.getElementById('consultationTypeDiv').classList.add('d-none');
        if (feeType === 'percent') {
            document.getElementById('percentAmountDiv').classList.remove('d-none');
            document.getElementById('percentInputDiv').classList.remove('d-none');
        } else if (feeType === 'fixed') {
            document.getElementById('fixedFeeDiv').classList.remove('d-none');
        } else if (feeType === 'consultation') {
            document.getElementById('consultationTypeDiv').classList.remove('d-none');
        }
    }
    toggleLawyerFeeType();

    function calcLawyerFee() {
        let type = document.getElementById('fee_type').value;
        let resultBox = document.getElementById('lawyerFeeResult');
        let result = '';
        let minFee = 250000, percentMin = 5, percentMax = 10;
        if (type === 'percent') {
            let amount = parseFloat(document.getElementById('case_amount').value || 0);
            let percent = parseFloat(document.getElementById('percent').value || 0);
            if (amount > 0 && percent >= percentMin && percent <= percentMax) {
                let fee = amount * percent / 100;
                if (fee < minFee) fee = minFee;
                result = `أتعاب المحامي المتوقعة: <b>${fee.toLocaleString()} دينار عراقي</b> (${percent}% من قيمة القضية)<br><span style='color:#ffd076'>طبقاً للحد الأدنى المسموح به.</span>`;
            } else {
                result = "يرجى إدخال قيمة صحيحة ونسبة بين 5% و 10%.";
            }
        } else if (type === 'fixed') {
            let fixed = parseFloat(document.getElementById('fixed_fee').value || 0);
            if (fixed >= minFee) {
                result = `أتعاب المحامي المتفق عليها: <b>${fixed.toLocaleString()} دينار عراقي</b>`;
            } else {
                result = "لا يجوز أن تقل الأتعاب عن 250,000 دينار عراقي حسب تعليمات النقابة.";
            }
        } else if (type === 'consultation') {
            let consultType = document.getElementById('consult_case_type').value;
            let obj = consultationFees.find(f => f.subtype === consultType);
            let fee = obj ? obj.min_fee : 25000;
            let name = obj ? obj.name : "استشارة عامة";
            result = `أتعاب الاستشارة (${name}): <b>${fee.toLocaleString()} دينار عراقي</b>`;
        }
        resultBox.innerHTML = result;
        resultBox.style.display = 'block';
    }

    function calcCaseFee() {
        let type = document.getElementById('case_type').value;
        let value = parseFloat(document.getElementById('case_value').value || 0);
        let obj = caseFees.find(f => f.subtype === type);
        let percent = obj ? obj.percent_fixed : 0;
        let name = obj ? obj.name : "قضية";
        let resultBox = document.getElementById('caseFeeResult');
        if (value > 0 && percent > 0) {
            let fee = value * percent / 100;
            resultBox.innerHTML = `رسوم الدعوى (${name}): <b>${fee.toLocaleString()} دينار عراقي</b> (${percent}% من قيمة الدعوى)`;
            resultBox.style.display = 'block';
        } else {
            resultBox.innerHTML = "يرجى إدخال قيمة صحيحة.";
            resultBox.style.display = 'block';
        }
    }

    function calcPropertyFee() {
        let value = parseFloat(document.getElementById('property_value').value || 0);
        let type = document.getElementById('property_type').value;
        let obj = propertyFees.find(f => f.subtype === type);
        let percent = obj ? obj.percent_fixed : 0;
        let name = obj ? obj.name : "عملية";
        let extra = parseFloat(document.getElementById('extra_fee').value || 0);
        let resultBox = document.getElementById('propertyFeeResult');
        if (value > 0 && percent > 0) {
            let fee = value * percent / 100;
            let total = fee + extra;
            let details = (extra > 0) ? `<br>رسوم إضافية: <b>${extra.toLocaleString()} دينار</b>` : '';
            resultBox.innerHTML = `رسوم التسجيل العقاري (${name}): <b>${fee.toLocaleString()} دينار عراقي</b> (${percent}% من قيمة العقار)${details}<br><span style="color:#ffd076">المجموع الكلي: <b>${total.toLocaleString()} دينار عراقي</b></span>`;
            resultBox.style.display = 'block';
        } else {
            resultBox.innerHTML = "يرجى إدخال قيمة العقار.";
            resultBox.style.display = 'block';
        }
    }

    // الضرائب ديناميكية
    function toggleTaxType() {
        let type = document.getElementById('tax_type').value;
        let label = document.getElementById('tax_amount_label');
        let obj = taxFees.find(t => t.subtype === type);
        if(obj) label.innerText = obj.name + " (دينار عراقي)";
        else label.innerText = "القيمة الخاضعة للضريبة (دينار عراقي)";
    }
    toggleTaxType();

    function calcTaxFee() {
        let type = document.getElementById('tax_type').value;
        let amount = parseFloat(document.getElementById('tax_amount').value || 0);
        let obj = taxFees.find(t => t.subtype === type);
        let percent = obj ? obj.percent_fixed : 0;
        let name = obj ? obj.name : "ضريبة";
        let resultBox = document.getElementById('taxFeeResult');
        if (amount <= 0) {
            resultBox.innerHTML = "يرجى إدخال قيمة صحيحة.";
            resultBox.style.display = 'block';
            return;
        }
        if (type === "income_personal") {
            // الشرائح الضريبية للأفراد
            let tax = 0;
            if(amount <= 2500000) tax = 0;
            else if(amount <= 5000000) tax = (amount - 2500000) * 0.03;
            else if(amount <= 10000000) tax = (2500000 * 0) + (2500000 * 0.03) + (amount - 5000000) * 0.05;
            else if(amount <= 20000000) tax = (2500000 * 0) + (2500000 * 0.03) + (5000000 * 0.05) + (amount - 10000000) * 0.10;
            else tax = (2500000 * 0) + (2500000 * 0.03) + (5000000 * 0.05) + (10000000 * 0.10) + (amount - 20000000) * 0.15;
            resultBox.innerHTML = "ضريبة الدخل السنوية المستحقة (شخص): <b>" + tax.toLocaleString() + " دينار عراقي</b>.<br><span style='color:#ffd076'>الإعفاء السنوي: 2,500,000 د.ع، الشريحة 2: 3%، الشريحة 3: 5%، الشريحة 4: 10%، ما زاد عن 20 مليون: 15%</span>";
            resultBox.style.display = 'block';
            return;
        }
        if (percent > 0) {
            let fee = amount * percent / 100;
            resultBox.innerHTML = `${name}: <b>${fee.toLocaleString()} دينار عراقي</b> (${percent}% من القيمة)`;
            resultBox.style.display = 'block';
        } else {
            resultBox.innerHTML = "نوع الضريبة غير معرف أو يحتاج معالجة خاصة.";
            resultBox.style.display = 'block';
        }
    }
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

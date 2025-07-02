<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>عقد رقم: {{ $contract->contract_number }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', 'Arial Unicode MS', Arial, sans-serif;
            direction: rtl;
            text-align: right;
            margin: 0;
            padding: 20px;
            line-height: 1.8;
            color: #333;
            unicode-bidi: bidi-override;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #b47e13;
            padding-bottom: 20px;
        }
        
        .company-logo {
            text-align: center;
            margin-bottom: 20px;
        }
        
        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #b47e13;
            margin-bottom: 10px;
            font-family: 'DejaVu Sans', Arial, sans-serif;
        }
        
        .contract-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 20px;
        }
        
        .contract-info {
            background-color: #f8f9fa;
            padding: 15px;
            border: 1px solid #dee2e6;
            margin-bottom: 20px;
        }
        
        .party-section {
            margin-bottom: 25px;
            padding: 15px;
            border: 1px solid #dee2e6;
            background-color: #fff;
        }
        
        .party-title {
            font-size: 16px;
            font-weight: bold;
            color: #b47e13;
            margin-bottom: 10px;
            border-bottom: 1px solid #b47e13;
            padding-bottom: 5px;
            font-family: 'DejaVu Sans', Arial, sans-serif;
        }
        
        .content-section {
            margin-bottom: 20px;
        }
        
        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #be7b06;
            margin-bottom: 10px;
            font-family: 'DejaVu Sans', Arial, sans-serif;
        }
        
        .terms-list {
            padding-right: 20px;
        }
        
        .terms-list li {
            margin-bottom: 8px;
        }
        
        .signature-section {
            margin-top: 40px;
            display: table;
            width: 100%;
        }
        
        .signature-box {
            display: table-cell;
            width: 48%;
            border: 1px solid #333;
            padding: 20px;
            text-align: center;
            vertical-align: top;
        }
        
        .signature-box:first-child {
            margin-left: 4%;
        }
        
        .signature-title {
            font-weight: bold;
            margin-bottom: 15px;
            border-bottom: 1px solid #ccc;
            padding-bottom: 5px;
            font-family: 'DejaVu Sans', Arial, sans-serif;
        }
        
        .signature-content {
            min-height: 80px;
        }
        
        .signed {
            color: #28a745;
            font-weight: bold;
        }
        
        .unsigned {
            color: #6c757d;
        }
        
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 12px;
            color: #666;
            border-top: 1px solid #dee2e6;
            padding-top: 15px;
        }
        
        .contact-info {
            text-align: center;
            margin-top: 30px;
            font-size: 12px;
            color: #666;
        }
        
        .page-break {
            page-break-before: always;
        }
        
        .highlight {
            background-color: #fff3cd;
            padding: 2px 4px;
            border-radius: 3px;
        }
        
        .amount {
            font-weight: bold;
            color: #28a745;
        }
    </style>
</head>
<body>
    <!-- رأس العقد -->
    <div class="header">
        <div class="company-logo">
            <div class="company-name">{{ $contract->company_name }}</div>
        </div>
        <div class="contract-title">
            اتفاقية تقديم خدمات لفترة مؤقته (موسم الحج 1446هـ)
        </div>
        <div class="contract-info">
            <strong>رقم العقد:</strong> {{ $contract->contract_number }} |
            <strong>تاريخ الإنشاء:</strong> {{ $contract->contract_date->format('Y/m/d') }}
        </div>
    </div>

    <!-- مقدمة العقد -->
    <div class="content-section">
        <p style="text-align: justify;">
            تم بعون الله وتوفيقه في هذا اليوم <strong>{{ $contract->contract_date->format('l') }}</strong> 
            <strong>{{ $contract->contract_date->format('d/m/Y') }}</strong> 
            الموافق <strong>{{ $contract->hijri_date }}</strong> الاتفاق والتراضي بين كل من:
        </p>
    </div>

    <!-- الطرف الأول - الشركة -->
    <div class="party-section">
        <div class="party-title">1- الطرف الأول (الشركة):</div>
        <p>
            <strong>{{ $contract->company_name }}</strong>، ومقرها {{ $contract->company_address }} 
            سجل تجاري رقم: <span class="highlight">{{ $contract->company_commercial_register }}</span>
        </p>
        <p>
            البريد الإلكتروني: {{ $contract->company_email }}
        </p>
        <p>
            ويمثلها في هذا العقد الأستاذ: <strong>{{ $contract->company_representative_name }}</strong> 
            بصفته {{ $contract->company_representative_title }}
        </p>
        <p style="color: #666; font-style: italic;">
            ويشار إليه فيما بعد (بالطرف الأول)
        </p>
    </div>

    <!-- الطرف الثاني - الموظف -->
    <div class="party-section">
        <div class="party-title">2- الطرف الثاني (الموظف):</div>
        <p>
            الأستاذ: <strong>{{ $contract->employee_name }}</strong>
        </p>
        <p>
            الجنسية: {{ $contract->employee_nationality }} | 
            هوية وطنية رقم: <span class="highlight">{{ $contract->employee_national_id }}</span>
        </p>
        <p>
            جوال: {{ $contract->employee_phone }}
        </p>
        <p>
            رقم الحساب البنكي: {{ $contract->employee_bank_account }} | 
            اسم البنك: ({{ $contract->employee_bank_name }})
        </p>
        <p style="color: #666; font-style: italic;">
            ويشار إليه فيما بعد (بالطرف الثاني)
        </p>
    </div>

    <!-- التمهيد -->
    <div class="content-section">
        <div class="section-title">التمهيد</div>
        <p style="text-align: justify;">
            حيث أن الطرف الأول يعمل في تقديم خدمات الحج والعمرة في المملكة العربية السعودية (منطقة مكة المكرمة) 
            ويرغب في إسناد مهمة تقديم خدمات <strong>({{ $contract->job_description }})</strong> 
            وحيث أن الطرف الثاني رغب في إسناد المهمة له. وبعد أن عبر كل شخص عن أهليته المعتبرة شرعاً 
            وعن سلامة رضاه وتبادلا الإيجاب والقبول فقد اتفقا على ما يلي:
        </p>
    </div>

    <!-- بنود العقد -->
    <div class="content-section">
        <div class="section-title">أولاً: التمهيد</div>
        <p>يعتبر التمهيد المذكور أعلاه جزءاً لا يتجزأ من هذه الاتفاقية.</p>
    </div>

    <div class="content-section">
        <div class="section-title">ثانياً: موضوع الاتفاقية</div>
        <p>
            اتفق الطرفان على أن يقوم الطرف الثاني بتقديم الخدمات المذكورة في التمهيد أعلاه لفترة مؤقتة 
            وبمعدل <span class="highlight">{{ $contract->working_hours_per_day }} ساعات يومياً</span> بمدة الاتفاقية.
        </p>
    </div>

    <div class="content-section">
        <div class="section-title">ثالثاً: مدة الاتفاقية</div>
        <p>
            مدة هذه الاتفاقية تبدأ من <span class="highlight">{{ $contract->start_date->format('d/m/Y') }}</span> 
            وتنتهي في <span class="highlight">{{ $contract->end_date->format('d/m/Y') }}</span>
            <br>
            <small>(إجمالي المدة: {{ $contract->duration_in_days }} يوم)</small>
        </p>
    </div>

    <div class="content-section">
        <div class="section-title">رابعاً: الأتعاب</div>
        <p>
            اتفق الطرفان بأن الأتعاب للأعمال التي تشملها هذه الاتفاقية والمذكورة في الفقرة الثانية من هذه الاتفاقية 
            مبلغ مقطوع وقدره <span class="amount">{{ $contract->formatted_salary }}</span> 
            تدفع بنهاية الفترة الكلية.
        </p>
    </div>

    <!-- صفحة جديدة للشروط -->
    <div class="page-break"></div>

    <div class="content-section">
        <div class="section-title">خامساً: التزامات الطرف الثاني</div>
        <ul class="terms-list">
            <li>الالتزام ببذل العناية المطلوبة في إعداد وإنجاز العمل المطلوب والمكلف به.</li>
            <li>الالتزام بتنفيذ ما يطلب منه من مهام.</li>
            <li>الالتزام بالمحافظة على حقوق الطرف الأول وكتمان أسراره والمحافظة على سمعته وأمواله وممتلكاته ومستنداته ووثائقه.</li>
            <li>الالتزام بحضور الاجتماعات حسب ما تقتضيه طبيعة العمل بناءً على طلب الطرف الأول.</li>
            <li>الالتزام بأن يحتفظ بأسرار الطرف الأول لنفسه وبكل ما يخبره به الطرف الأول أو يوجه له من تعليمات خلال سير العمل أو بعد إتمامها وألا يطلع عليها أي طرف آخر.</li>
        </ul>
    </div>

    <div class="content-section">
        <div class="section-title">سادساً: التزامات الطرف الأول</div>
        <ul class="terms-list">
            <li>الالتزام بتقديم جميع الوسائل المساعدة لتنفيذ المهام المطلوب تحقيقها.</li>
            <li>الالتزام بأن يضع تحت تصرف وحيازة الطرف الثاني دوماً وعند الطلب جميع الأوراق والوثائق والمستندات الخاصة بأداء عمله وألا يخفى عنه شيئاً من المعلومات المتعلقة بها.</li>
        </ul>
    </div>

    <!-- الأحكام العامة -->
    <div class="content-section">
        <div class="section-title">سابعاً: أحكام عامة</div>
        <ul class="terms-list">
            <li>يعتبر هذا العقد وحدة متكاملة كل نص فيها يكمل الآخر ويفسر مضمونه وأحكامه.</li>
            <li>يلتزم الطرفان بالمحافظة على المعلومات السرية التي يتم الكشف عنها للأغراض المتعلقة بتنفيذ هذا العقد.</li>
            <li>تخضع جميع مواد وفقرات هذا العقد وتفسيراتها إلى القوانين المعمول بها في المملكة العربية السعودية.</li>
            <li>في حال حدوث خلاف أو نزاع بين طرفيه، فإن المحكمة العمالية هي الجهة المختصة بالنظر لهذا النزاع.</li>
        </ul>
    </div>

    <!-- الشروط الخاصة -->
    <div class="content-section">
        <div class="section-title">ثامناً: شروط خاصة</div>
        <ul class="terms-list">
            <li>يلتزم الموظف بالتواجد في جميع الأوقات التي يتطلب بها الحضور في الأماكن المحددة لعمله.</li>
            <li>في حال التخلف عن أحد المراحل أو الاعتذار لمرتين عن الحضور يتم استبعاده عن العمل ولا يحق له استلام المكافأة.</li>
            <li>في حالة الإخفاق أو التقاعس عن العمل الموكل إليه يحق للشركة إنهاء الاتفاقية في أي وقت دون تعويض.</li>
            <li>لا بد أن يكون الحساب البنكي بنفس اسم المستفيد وفي حالة اكتشاف غير ذلك لا يتم الصرف ويعتبر العقد لاغياً.</li>
            <li>يحق للشركة تسجيل المتعاقد في نظام أجير للفترة المذكورة.</li>
            <li>يتعهد الطرف الثاني بأنه ليس موظف حكومي وفي حال اكتشاف ذلك يعتبر العقد لاغياً.</li>
        </ul>
    </div>

    <!-- التوقيعات -->
    <div class="signature-section">
        <div class="signature-box">
            <div class="signature-title">الطرف الأول</div>
            <div><strong>{{ $contract->company_name }}</strong></div>
            <div class="signature-content">
                @if($contract->company_signed_at)
                    <div class="signed">
                        <div>التوقيع: {{ $contract->company_signature }}</div>
                        <div>التاريخ: {{ $contract->company_signed_at->format('Y/m/d H:i') }}</div>
                    </div>
                @else
                    <div class="unsigned">
                        <div>التوقيع: _______________</div>
                        <div>التاريخ: _______________</div>
                    </div>
                @endif
            </div>
        </div>
        
        <div class="signature-box">
            <div class="signature-title">الطرف الثاني</div>
            <div><strong>{{ $contract->employee_name }}</strong></div>
            <div class="signature-content">
                @if($contract->employee_signed_at)
                    <div class="signed">
                        <div>التوقيع: {{ $contract->employee_signature }}</div>
                        <div>التاريخ: {{ $contract->employee_signed_at->format('Y/m/d H:i') }}</div>
                    </div>
                @else
                    <div class="unsigned">
                        <div>التوقيع: _______________</div>
                        <div>التاريخ: _______________</div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- معلومات الاتصال -->
    <div class="contact-info">
        <div><strong>{{ $contract->company_name }}</strong></div>
        <div>{{ $contract->company_address }}</div>
        <div>هاتف: 800 245 0022 | بريد إلكتروني: {{ $contract->company_email }}</div>
    </div>

    <!-- تذييل -->
    <div class="footer">
        <p>هذا العقد تم إنشاؤه إلكترونياً بتاريخ {{ now()->format('Y/m/d H:i:s') }}</p>
        <p>رقم العقد: {{ $contract->contract_number }} | نظام إدارة العقود - شركة مناسك المشاعر</p>
    </div>
</body>
</html> 
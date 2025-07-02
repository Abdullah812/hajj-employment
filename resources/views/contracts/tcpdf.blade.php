<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>عقد رقم: {{ $contract->contract_number }}</title>
    <style>
        body {
            font-family: 'Arial Unicode MS', Arial, sans-serif;
            font-size: 14px;
            line-height: 1.6;
            color: #000;
            direction: rtl;
            text-align: right;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #000;
            padding-bottom: 15px;
        }
        
        .info-box {
            border: 1px solid #ccc;
            padding: 15px;
            margin: 15px 0;
            background-color: #f9f9f9;
        }
        
        .bold {
            font-weight: bold;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        
        table td, table th {
            border: 1px solid #000;
            padding: 10px;
            text-align: center;
        }
        
        table th {
            background-color: #f0f0f0;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>عقد عمل موسمي للحج 1446هـ</h1>
        <p><strong>رقم العقد:</strong> {{ $contract->contract_number ?? 'غير محدد' }}</p>
    </div>

    <div class="info-box">
        <h3>الطرف الأول (صاحب العمل)</h3>
        <p><strong>الاسم:</strong> {{ $contract->company_name ?? ($contract->company ? $contract->company->name : 'شركة مناسك المشاعر') }}</p>
        <p><strong>رقم السجل التجاري:</strong> {{ $contract->company_commercial_register ?? '1010000000' }}</p>
        <p><strong>العنوان:</strong> {{ $contract->company_address ?? 'الرياض - المملكة العربية السعودية' }}</p>
        <p><strong>الهاتف:</strong> {{ $contract->company_phone ?? '+966112345678' }}</p>
    </div>

    <div class="info-box">
        <h3>الطرف الثاني (العامل)</h3>
        <p><strong>الاسم:</strong> {{ $contract->employee_name ?? ($contract->employee ? $contract->employee->name : 'غير محدد') }}</p>
        <p><strong>رقم الهوية:</strong> {{ $contract->employee_national_id ?: 'غير محدد' }}</p>
        <p><strong>الجنسية:</strong> {{ $contract->employee_nationality ?? 'سعودي' }}</p>
        <p><strong>الهاتف:</strong> {{ $contract->employee_phone ?: 'غير محدد' }}</p>
    </div>

    <div class="info-box">
        <h3>تفاصيل العقد</h3>
        <p><strong>المسمى الوظيفي:</strong> {{ $contract->job_description ?? ($contract->job ? $contract->job->title : 'غير محدد') }}</p>
        <p><strong>الراتب الشهري:</strong> {{ number_format($contract->salary, 2) }} ريال سعودي</p>
        <p><strong>تاريخ بداية العقد:</strong> {{ $contract->start_date ? $contract->start_date->format('Y/m/d') : 'غير محدد' }}</p>
        <p><strong>تاريخ انتهاء العقد:</strong> {{ $contract->end_date ? $contract->end_date->format('Y/m/d') : 'غير محدد' }}</p>
        <p><strong>ساعات العمل اليومية:</strong> {{ $contract->working_hours_per_day ?? 8 }} ساعات</p>
    </div>

    <h3>الشروط والأحكام</h3>
    <ol>
        <li>يلتزم الطرف الثاني بأداء العمل المكلف به بكل أمانة وإخلاص وفقاً للوائح والتعليمات المعمول بها في الشركة.</li>
        <li>يحق للطرف الأول إنهاء هذا العقد في أي وقت دون إبداء الأسباب مع إشعار مسبق مدته أسبوع واحد.</li>
        <li>يلتزم الطرف الثاني بالمحافظة على أسرار العمل وعدم إفشائها لأي جهة كانت.</li>
        <li>يستحق الطرف الثاني راتباً شهرياً قدره {{ number_format($contract->salary, 2) }} ريال سعودي يُدفع في نهاية كل شهر.</li>
        <li>ساعات العمل الرسمية هي {{ $contract->working_hours_per_day ?? 8 }} ساعات يومياً، ويحق للطرف الأول تعديل ساعات العمل حسب متطلبات موسم الحج.</li>
        <li>في حالة وجود نزاع، يتم الرجوع إلى القوانين والأنظمة المعمول بها في المملكة العربية السعودية.</li>
    </ol>

    @if($contract->special_terms)
    <div class="info-box">
        <h4>شروط خاصة:</h4>
        <p>{{ $contract->special_terms }}</p>
    </div>
    @endif

    <table>
        <tr>
            <th>البيان</th>
            <th>الطرف الأول</th>
            <th>الطرف الثاني</th>
        </tr>
        <tr>
            <td class="bold">الاسم</td>
            <td>{{ $contract->company_name ?? ($contract->company ? $contract->company->name : 'شركة مناسك المشاعر') }}</td>
            <td>{{ $contract->employee_name ?? ($contract->employee ? $contract->employee->name : 'غير محدد') }}</td>
        </tr>
        <tr>
            <td class="bold">التوقيع</td>
            <td>
                @if($contract->company_signature)
                    تم التوقيع إلكترونياً
                @else
                    ___________________
                @endif
            </td>
            <td>
                @if($contract->employee_signature)
                    تم التوقيع إلكترونياً
                @else
                    ___________________
                @endif
            </td>
        </tr>
        <tr>
            <td class="bold">التاريخ</td>
            <td>{{ $contract->company_signed_at ? $contract->company_signed_at->format('Y/m/d') : '____/____/____' }}</td>
            <td>{{ $contract->employee_signed_at ? $contract->employee_signed_at->format('Y/m/d') : '____/____/____' }}</td>
        </tr>
    </table>

    @if($contract->notes)
    <div class="info-box">
        <h4>ملاحظات:</h4>
        <p>{{ $contract->notes }}</p>
    </div>
    @endif

    <div style="margin-top: 30px; text-align: center; font-size: 12px; color: #666;">
        <p>تم إنشاء هذا العقد إلكترونياً - شركة مناسك المشاعر</p>
        <p>حالة العقد: {{ $contract->status == 'cancelled' ? 'ملغي' : 'نشط' }}</p>
    </div>
</body>
</html> 
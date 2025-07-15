<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\HajjJob;
use App\Models\User;
use App\Models\Department;

class HajjJobsSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // الحصول على الأقسام
        $user = User::role('department')->first();
        
        if (!$user) {
            $this->command->info('لا توجد أقسام مسجلة. سيتم إنشاء الوظائف للمستخدم الأول.');
            $user = User::first();
        }

        // إنشاء قسم للمستخدم إذا لم يكن لديه قسم
        $department = Department::firstOrCreate(
            ['user_id' => $user->id],
            [
                'name' => 'القسم الرئيسي',
                'description' => 'القسم الرئيسي للنظام',
                'phone' => '0500000000',
                'address' => 'مكة المكرمة',
                'email' => $user->email,
                'website' => 'https://example.com',
                'status' => 'active',
                'type' => 'main',
                'registration_number' => 'DEP' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT),
                'created_at' => now(),
                'updated_at' => now()
            ]
        );
        
        $jobs = [
            [
                'title' => 'مشرف إعاشة - مكة المكرمة',
                'description' => 'مطلوب مشرف إعاشة للإشراف على خدمات الطعام للحجاج في مكة المكرمة. يجب أن يكون لديه خبرة في إدارة المطابخ والوجبات الجماعية.',
                'location' => 'مكة المكرمة',
                'employment_type' => 'seasonal',
                'salary_min' => 4000,
                'salary_max' => 6000,
                'requirements' => '- خبرة لا تقل عن سنتين في إدارة المطابخ
- شهادة في السلامة الغذائية
- القدرة على العمل تحت ضغط
- مهارات قيادية وتنظيمية
- إجادة اللغة العربية والإنجليزية',
                'benefits' => '- راتب تنافسي
- سكن مجاني
- وجبات مجانية
- تأمين طبي
- بونص نهاية الموسم',
                'application_deadline' => now()->addMonths(2),
                'max_applicants' => 50,
            ],
            [
                'title' => 'مرشد حج - متعدد الجنسيات',
                'description' => 'مطلوب مرشد حج للحجاج الإيرانيين والسنغاليين. يجب إجادة اللغات المطلوبة والمعرفة التامة بمناسك الحج.',
                'location' => 'مكة المكرمة - المدينة المنورة',
                'employment_type' => 'seasonal',
                'salary_min' => 5000,
                'salary_max' => 8000,
                'requirements' => '- إجادة اللغة الفارسية أو الفرنسية
- معرفة تامة بمناسك الحج والعمرة
- خبرة في التعامل مع المجموعات
- حسن الخلق والصبر
- شهادة مرشد سياحي معتمد',
                'benefits' => '- راتب مجزي
- سكن وإعاشة
- تأمين شامل
- حوافز أداء
- شهادة خبرة',
                'application_deadline' => now()->addMonths(3),
                'max_applicants' => 30,
            ],
            [
                'title' => 'سائق نقل جماعي',
                'description' => 'مطلوب سائق حافلة لنقل الحجاج بين المشاعر المقدسة. يجب أن يكون لديه رخصة قيادة درجة ثانية وخبرة في القيادة الآمنة.',
                'location' => 'مكة المكرمة - منى - عرفات',
                'employment_type' => 'seasonal',
                'salary_min' => 3500,
                'salary_max' => 5000,
                'requirements' => '- رخصة قيادة درجة ثانية سارية
- خبرة لا تقل عن 3 سنوات في قيادة الحافلات
- السجل الجنائي نظيف
- اللياقة البدنية والذهنية
- المعرفة بطرق مكة والمشاعر المقدسة',
                'benefits' => '- راتب أساسي + حوافز
- تأمين شامل
- سكن مجاني
- وجبات مدعومة
- تدريب على السلامة',
                'application_deadline' => now()->addMonths(2)->addWeeks(2),
                'max_applicants' => 100,
            ],
            [
                'title' => 'موظف استقبال وخدمة عملاء',
                'description' => 'مطلوب موظف استقبال للعمل في مكاتب الشركة لخدمة الحجاج وحل استفساراتهم وتنسيق خدماتهم.',
                'location' => 'مكة المكرمة - المدينة المنورة',
                'employment_type' => 'seasonal',
                'salary_min' => 3000,
                'salary_max' => 4500,
                'requirements' => '- مؤهل جامعي في أي تخصص
- إجادة اللغة الإنجليزية
- مهارات تواصل ممتازة
- خبرة في خدمة العملاء
- القدرة على العمل بنظام الورديات',
                'benefits' => '- راتب تنافسي
- مكافآت أداء
- تأمين طبي
- تدريب مهني
- بيئة عمل مريحة',
                'application_deadline' => now()->addMonths(2)->addDays(10),
                'max_applicants' => 75,
            ],
            [
                'title' => 'منسق سكن وإقامة',
                'description' => 'مطلوب منسق سكن لإدارة ومتابعة أماكن إقامة الحجاج والتأكد من جودة الخدمات المقدمة.',
                'location' => 'مكة المكرمة - منى',
                'employment_type' => 'seasonal',
                'salary_min' => 4500,
                'salary_max' => 6500,
                'requirements' => '- خبرة في إدارة الفنادق أو المساكن
- مهارات تنظيمية عالية
- القدرة على حل المشاكل بسرعة
- العمل بروح الفريق
- إجادة استخدام الحاسوب',
                'benefits' => '- راتب أساسي + عمولات
- سكن مجاني
- وجبات مجانية
- تأمين شامل
- حوافز نهاية الموسم',
                'application_deadline' => now()->addMonths(3)->addDays(5),
                'max_applicants' => 40,
            ],
            [
                'title' => 'مساعد إداري',
                'description' => 'مطلوب مساعد إداري للعمل في المكاتب الإدارية ومساعدة في الأعمال الإدارية والتنسيقية.',
                'location' => 'مكة المكرمة',
                'employment_type' => 'seasonal',
                'salary_min' => 2800,
                'salary_max' => 4000,
                'requirements' => '- مؤهل ثانوي كحد أدنى
- إجادة استخدام برامج المكتب
- مهارات تنظيمية جيدة
- السرعة والدقة في العمل
- حسن المظهر والسلوك',
                'benefits' => '- راتب شهري ثابت
- تأمين طبي أساسي
- وجبة غداء يومية
- مواصلات مجانية
- تدريب على رأس العمل',
                'application_deadline' => now()->addMonths(2)->addDays(20),
                'max_applicants' => 60,
            ],
        ];
        
        foreach ($jobs as $jobData) {
            HajjJob::create(array_merge($jobData, [
                'department_id' => $department->id,
                'status' => 'active'
            ]));
        }
        
        $this->command->info('تم إنشاء ' . count($jobs) . ' وظيفة تجريبية بنجاح!');
    }
}

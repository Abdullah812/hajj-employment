<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\HajjJob;
use App\Models\Department;

class MeccaJobsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // التأكد من وجود قسم افتراضي
        $department = Department::firstOrCreate([
            'name' => 'خدمات الحج',
            'description' => 'قسم خدمات الحج والعمرة'
        ]);

        // إضافة وظائف تجريبية في مكة المكرمة
        $meccaJobs = [
            [
                'title' => 'مرشد حج ومساعد حجاج',
                'description' => 'المساعدة في إرشاد الحجاج وتوجيههم في المشاعر المقدسة. العمل في فريق لتقديم أفضل الخدمات للحجاج وضمان راحتهم وسلامتهم أثناء تأدية المناسك.',
                'location' => 'مكة المكرمة - المشاعر المقدسة',
                'employment_type' => 'temporary',
                'salary_min' => 3000,
                'salary_max' => 5000,
                'requirements' => 'خبرة في التعامل مع الحجاج، إجادة اللغة العربية والإنجليزية، اللياقة البدنية',
                'benefits' => 'راتب شهري، سكن، وجبات، مواصلات، تأمين طبي',
                'application_deadline' => now()->addDays(30),
                'max_applicants' => 50,
                'status' => 'active',
                'region' => 'mecca',
                'application_type' => 'open',
                'requires_registration' => false,
            ],
            [
                'title' => 'منظم حشود في الحرم المكي',
                'description' => 'تنظيم حركة الحجاج والزوار في الحرم المكي الشريف، والمساعدة في تسهيل الطواف والسعي بأمان وانسيابية.',
                'location' => 'الحرم المكي الشريف',
                'employment_type' => 'temporary',
                'salary_min' => 3500,
                'salary_max' => 4500,
                'requirements' => 'القدرة على التعامل مع الحشود، الصبر، مهارات التواصل الممتازة',
                'benefits' => 'راتب تنافسي، وجبات، مواصلات، تأمين شامل',
                'application_deadline' => now()->addDays(25),
                'max_applicants' => 30,
                'status' => 'active',
                'region' => 'mecca',
                'application_type' => 'open',
                'requires_registration' => false,
            ],
            [
                'title' => 'مشرف نظافة وصحة عامة',
                'description' => 'الإشراف على أعمال النظافة والصحة العامة في مرافق الحج، وضمان المحافظة على البيئة الصحية للحجاج.',
                'location' => 'مكة المكرمة - مرافق الحج',
                'employment_type' => 'temporary',
                'salary_min' => 2800,
                'salary_max' => 4000,
                'requirements' => 'خبرة في أعمال الصحة العامة، الانضباط، العمل تحت ضغط',
                'benefits' => 'راتب شهري، سكن مؤقت، وجبات، مكافآت',
                'application_deadline' => now()->addDays(20),
                'max_applicants' => 40,
                'status' => 'active',
                'region' => 'mecca',
                'application_type' => 'open',
                'requires_registration' => false,
            ],
            [
                'title' => 'موظف استقبال وخدمة حجاج',
                'description' => 'استقبال الحجاج في المطار والفنادق، تقديم المساعدة اللازمة، والتأكد من حصولهم على جميع الخدمات المطلوبة.',
                'location' => 'مطار الملك عبدالعزيز - مكة المكرمة',
                'employment_type' => 'temporary',
                'salary_min' => 3200,
                'salary_max' => 4200,
                'requirements' => 'إجادة لغتين على الأقل، مهارات خدمة العملاء، مظهر مناسب',
                'benefits' => 'راتب جيد، مواصلات، وجبات، بدل لغات',
                'application_deadline' => now()->addDays(35),
                'max_applicants' => 25,
                'status' => 'active',
                'region' => 'mecca',
                'application_type' => 'open',
                'requires_registration' => false,
            ],
            [
                'title' => 'سائق حافلات حجاج',
                'description' => 'قيادة حافلات نقل الحجاج بين المشاعر المقدسة والفنادق، مع ضمان السلامة والراحة للركاب.',
                'location' => 'مكة المكرمة - المشاعر المقدسة',
                'employment_type' => 'temporary',
                'salary_min' => 4000,
                'salary_max' => 6000,
                'requirements' => 'رخصة قيادة عامة سارية، خبرة في قيادة الحافلات، معرفة طرق مكة',
                'benefits' => 'راتب عالي، مكافآت أداء، تأمين، وقود',
                'application_deadline' => now()->addDays(15),
                'max_applicants' => 20,
                'status' => 'active',
                'region' => 'mecca',
                'application_type' => 'open',
                'requires_registration' => false,
            ]
        ];

        foreach ($meccaJobs as $jobData) {
            $jobData['department_id'] = $department->id;
            
            HajjJob::create($jobData);
        }

        // إضافة وظيفة واحدة في المدينة للمقارنة (تتطلب تسجيل دخول)
        HajjJob::create([
            'department_id' => $department->id,
            'title' => 'مرشد سياحي في المدينة المنورة',
            'description' => 'إرشاد الزوار في المسجد النبوي الشريف والأماكن التاريخية في المدينة المنورة.',
            'location' => 'المسجد النبوي الشريف',
            'employment_type' => 'permanent',
            'salary_min' => 5000,
            'salary_max' => 7000,
            'requirements' => 'شهادة في السياحة أو التاريخ، إجادة لغات متعددة',
            'benefits' => 'راتب ثابت، تأمين طبي، إجازات سنوية',
            'application_deadline' => now()->addDays(45),
            'max_applicants' => 10,
            'status' => 'active',
            'region' => 'medina',
            'application_type' => 'registered',
            'requires_registration' => true,
        ]);

        echo "تم إضافة " . count($meccaJobs) . " وظائف في مكة المكرمة (بدون تسجيل) و 1 وظيفة في المدينة (بتسجيل)\n";
    }
}

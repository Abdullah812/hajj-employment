<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\HajjJob;
use App\Models\Department;

class SetupMeccaJobs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'setup:mecca-jobs {--force : Force creation even if jobs exist}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'إضافة وظائف تجريبية في مكة المكرمة بدون تسجيل دخول';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🕋 بدء إضافة وظائف مكة المكرمة...');

        // التحقق من وجود وظائف مكة مسبقاً
        $existingMeccaJobs = HajjJob::where('region', 'mecca')->count();
        
        if ($existingMeccaJobs > 0 && !$this->option('force')) {
            $this->warn("⚠️ يوجد {$existingMeccaJobs} وظيفة في مكة مسبقاً.");
            
            if (!$this->confirm('هل تريد إضافة المزيد؟')) {
                $this->info('تم الإلغاء.');
                return 0;
            }
        }

        // إنشاء أو العثور على قسم خدمات الحج
        $department = Department::firstOrCreate([
            'name' => 'خدمات الحج'
        ], [
            'description' => 'قسم خدمات الحج والعمرة - وظائف موسمية'
        ]);

        $this->info("📋 القسم: {$department->name}");

        // بيانات الوظائف
        $jobs = [
            [
                'title' => 'مرشد حج ومساعد حجاج',
                'description' => 'المساعدة في إرشاد الحجاج وتوجيههم في المشاعر المقدسة. العمل في فريق لتقديم أفضل الخدمات للحجاج وضمان راحتهم وسلامتهم أثناء تأدية المناسك.',
                'location' => 'مكة المكرمة - المشاعر المقدسة',
                'employment_type' => 'temporary',
                'salary_min' => 3000,
                'salary_max' => 5000,
                'requirements' => 'خبرة في التعامل مع الحجاج، إجادة اللغة العربية والإنجليزية، اللياقة البدنية',
                'benefits' => 'راتب شهري، سكن، وجبات، مواصلات، تأمين طبي',
                'max_applicants' => 50,
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
                'max_applicants' => 30,
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
                'max_applicants' => 40,
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
                'max_applicants' => 25,
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
                'max_applicants' => 20,
            ]
        ];

        $bar = $this->output->createProgressBar(count($jobs));
        $createdCount = 0;

        foreach ($jobs as $jobData) {
            // إضافة الحقول المطلوبة
            $jobData['department_id'] = $department->id;
            $jobData['application_deadline'] = now()->addDays(30);
            $jobData['status'] = 'active';
            $jobData['region'] = 'mecca';
            $jobData['application_type'] = 'open';
            $jobData['requires_registration'] = false;
            
            // التحقق من عدم وجود الوظيفة مسبقاً
            $exists = HajjJob::where('title', $jobData['title'])
                ->where('region', 'mecca')
                ->exists();
                
            if (!$exists) {
                HajjJob::create($jobData);
                $createdCount++;
            }
            
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();

        // إضافة وظيفة واحدة في المدينة للمقارنة
        $medinajobExists = HajjJob::where('title', 'مرشد سياحي في المدينة المنورة')
            ->where('region', 'medina')
            ->exists();
            
        if (!$medinajobExists) {
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
            
            $this->info('🕌 تم إضافة وظيفة واحدة في المدينة المنورة (تتطلب تسجيل دخول)');
        }

        $this->newLine();
        $this->info("✅ تم إنشاء {$createdCount} وظيفة جديدة في مكة المكرمة");
        $this->info('🔥 جميع الوظائف مُعدة للتقديم بدون تسجيل دخول!');
        
        $this->table(
            ['المنطقة', 'عدد الوظائف', 'نوع التقديم'],
            [
                ['مكة المكرمة', HajjJob::where('region', 'mecca')->count(), 'مفتوح بدون تسجيل'],
                ['المدينة المنورة', HajjJob::where('region', 'medina')->count(), 'يتطلب تسجيل دخول'],
                ['إجمالي', HajjJob::count(), 'متنوع']
            ]
        );

        return 0;
    }
}

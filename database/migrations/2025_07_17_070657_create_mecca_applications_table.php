<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('mecca_applications', function (Blueprint $table) {
            $table->id();
            $table->string('reference_number', 20)->unique()->comment('رقم مرجعي فريد');
            $table->unsignedBigInteger('job_id')->comment('معرف الوظيفة');
            
            // البيانات الشخصية
            $table->string('full_name')->comment('الاسم الكامل');
            $table->string('national_id', 10)->unique()->comment('رقم الهوية الوطنية');
            $table->date('birth_date')->comment('تاريخ الميلاد');
            $table->string('nationality', 50)->default('سعودي')->comment('الجنسية');
            $table->enum('marital_status', ['single', 'married', 'divorced', 'widowed'])->nullable()->comment('الحالة الاجتماعية');
            $table->enum('gender', ['male', 'female'])->comment('الجنس');
            
            // بيانات التواصل
            $table->string('phone', 15)->comment('رقم الجوال');
            $table->string('phone_alt', 15)->nullable()->comment('رقم جوال بديل');
            $table->string('email')->comment('البريد الإلكتروني');
            $table->text('address')->comment('العنوان الحالي');
            $table->string('city', 100)->nullable()->comment('المدينة');
            
            // المؤهلات العلمية
            $table->enum('qualification', ['ثانوي', 'دبلوم', 'بكالوريوس', 'ماجستير', 'دكتوراه'])->comment('المؤهل الأعلى');
            $table->string('specialization')->nullable()->comment('التخصص');
            $table->string('university')->nullable()->comment('الجامعة أو المعهد');
            $table->year('graduation_year')->nullable()->comment('سنة التخرج');
            $table->decimal('gpa', 3, 2)->nullable()->comment('المعدل');
            
            // الخبرات العملية
            $table->integer('experience_years')->default(0)->comment('سنوات الخبرة');
            $table->string('current_job')->nullable()->comment('الوظيفة الحالية');
            $table->string('current_employer')->nullable()->comment('جهة العمل الحالية');
            $table->decimal('current_salary', 10, 2)->nullable()->comment('الراتب الحالي');
            $table->text('experience_summary')->nullable()->comment('ملخص الخبرات');
            
            // البيانات البنكية
            $table->string('iban_number', 24)->comment('رقم الآيبان');
            $table->string('bank_name', 100)->comment('اسم البنك');
            
            // المرفقات
            $table->string('national_id_file')->nullable()->comment('صورة الهوية الوطنية');
            $table->string('address_file')->nullable()->comment('صورة العنوان الوطني');
            $table->string('certificate_file')->nullable()->comment('صورة الشهادة');
            $table->json('experience_files')->nullable()->comment('ملفات شهادات الخبرة');
            $table->string('iban_file')->nullable()->comment('صورة شهادة الآيبان');
            $table->string('cv_file')->comment('ملف السيرة الذاتية');
            $table->string('photo_file')->nullable()->comment('الصورة الشخصية');
            $table->json('other_files')->nullable()->comment('ملفات أخرى');
            
            // بيانات إضافية
            $table->text('cover_letter')->nullable()->comment('رسالة تعريفية');
            $table->text('skills')->nullable()->comment('المهارات');
            $table->text('languages')->nullable()->comment('اللغات');
            
            // حالة الطلب
            $table->enum('status', ['pending', 'approved', 'rejected', 'withdrawn'])
                  ->default('pending')
                  ->comment('حالة الطلب');
            
            // بيانات المراجعة
            $table->timestamp('applied_at')->useCurrent()->comment('تاريخ التقديم');
            $table->timestamp('reviewed_at')->nullable()->comment('تاريخ المراجعة');
            $table->unsignedBigInteger('reviewed_by')->nullable()->comment('من راجع الطلب');
            $table->text('review_notes')->nullable()->comment('ملاحظات المراجعة');
            $table->text('rejection_reason')->nullable()->comment('سبب الرفض');
            
            // تتبع التواصل
            $table->boolean('sms_sent')->default(false)->comment('تم إرسال SMS');
            $table->boolean('email_sent')->default(false)->comment('تم إرسال إيميل');
            $table->timestamp('last_contact_at')->nullable()->comment('آخر تواصل');
            
            $table->timestamps();
            
            // الفهارس
            $table->index(['job_id']);
            $table->index(['status']);
            $table->index(['national_id']);
            $table->index(['reference_number']);
            $table->index(['applied_at']);
            
            // المفاتيح الخارجية
            $table->foreign('job_id')->references('id')->on('hajj_jobs')->onDelete('cascade');
            $table->foreign('reviewed_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mecca_applications');
    }
};

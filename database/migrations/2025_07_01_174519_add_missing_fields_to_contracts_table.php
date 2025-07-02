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
        Schema::table('contracts', function (Blueprint $table) {
            // إضافة حقول التاريخ الهجري
            $table->string('hijri_contract_start_date')->nullable()->after('hijri_date');
            $table->string('hijri_contract_end_date')->nullable()->after('hijri_contract_start_date');
            
            // إضافة يوم الأسبوع للعقد
            $table->string('contract_day_of_week')->nullable()->after('contract_date');
            
            // إضافة الراتب بالكلمات
            $table->string('salary_in_words')->nullable()->after('salary');
            
            // إضافة معلومات إضافية للشركة
            $table->string('company_national_address')->nullable()->after('company_address');
            $table->string('company_phone')->nullable()->after('company_email');
            
            // إضافة معلومات إضافية للموظف
            $table->string('employee_gender')->nullable()->after('employee_nationality');
            $table->text('employee_address')->nullable()->after('employee_phone');
            
            // إضافة حقول للتوقيع الرقمي
            $table->text('digital_signature_data')->nullable()->after('company_signed_at');
            
            // إضافة حقل للملاحظات الخاصة
            $table->text('special_terms')->nullable()->after('contract_terms');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contracts', function (Blueprint $table) {
            $table->dropColumn([
                'hijri_contract_start_date',
                'hijri_contract_end_date', 
                'contract_day_of_week',
                'salary_in_words',
                'company_national_address',
                'company_phone',
                'employee_gender',
                'employee_address',
                'digital_signature_data',
                'special_terms'
            ]);
        });
    }
};

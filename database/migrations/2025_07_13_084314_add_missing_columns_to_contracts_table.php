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
            // إضافة الأعمدة المفقودة من موديل Contract (فقط التي غير موجودة)
            
            // التحقق من وجود الأعمدة قبل إضافتها
            if (!Schema::hasColumn('contracts', 'salary_in_words')) {
                $table->string('salary_in_words')->nullable()->after('salary');
            }
            
            if (!Schema::hasColumn('contracts', 'employee_gender')) {
                $table->string('employee_gender')->nullable()->after('employee_nationality');
            }
            
            if (!Schema::hasColumn('contracts', 'employee_address')) {
                $table->text('employee_address')->nullable()->after('employee_phone');
            }
            
            if (!Schema::hasColumn('contracts', 'department_national_address')) {
                $table->text('department_national_address')->nullable()->after('department_address');
            }
            
            if (!Schema::hasColumn('contracts', 'department_phone')) {
                $table->string('department_phone')->nullable()->after('department_email');
            }
            
            if (!Schema::hasColumn('contracts', 'special_terms')) {
                $table->text('special_terms')->nullable()->after('contract_terms');
            }
            
            if (!Schema::hasColumn('contracts', 'contract_day_of_week')) {
                $table->string('contract_day_of_week')->nullable()->after('contract_date');
            }
            
            if (!Schema::hasColumn('contracts', 'digital_signature_data')) {
                $table->text('digital_signature_data')->nullable()->after('department_signed_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contracts', function (Blueprint $table) {
            $table->dropColumn([
                'salary_in_words',
                'employee_gender',
                'employee_address',
                'department_national_address',
                'department_phone',
                'special_terms',
                'contract_day_of_week',
                'digital_signature_data'
            ]);
        });
    }
};

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
        Schema::table('hajj_jobs', function (Blueprint $table) {
            // إضافة حقل المنطقة
            $table->enum('region', ['mecca', 'medina', 'jeddah', 'taif', 'other'])
                  ->default('mecca')
                  ->after('department_id')
                  ->comment('منطقة الوظيفة');
            
            // إضافة حقل نوع التقديم
            $table->enum('application_type', ['registered', 'open', 'both'])
                  ->default('registered')
                  ->after('region')
                  ->comment('نوع التقديم: مسجل، مفتوح، أو كلاهما');
            
            // حقل يحدد ما إذا كانت الوظيفة تتطلب تسجيل دخول
            $table->boolean('requires_registration')
                  ->default(true)
                  ->after('application_type')
                  ->comment('هل تتطلب الوظيفة تسجيل دخول');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hajj_jobs', function (Blueprint $table) {
            $table->dropColumn(['region', 'application_type', 'requires_registration']);
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // تحديث الوظائف الموجودة بالقيم الافتراضية
        DB::table('hajj_jobs')->update([
            'region' => 'mecca',
            'application_type' => 'registered', 
            'requires_registration' => true
        ]);
        
        // يمكن تخصيص بعض الوظائف لتكون مفتوحة في مكة
        // هذا مثال - يمكن تعديله حسب الحاجة
        DB::table('hajj_jobs')
            ->where('title', 'like', '%مرشد%')
            ->orWhere('title', 'like', '%حج%')
            ->orWhere('title', 'like', '%عامل%')
            ->orWhere('title', 'like', '%منظف%')
            ->update([
                'region' => 'mecca',
                'application_type' => 'open',
                'requires_registration' => false
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // في حالة الرجوع، نعيد الحقول إلى null
        // لكن هذا قد يسبب مشاكل لذا نتركها كما هي
        // DB::table('hajj_jobs')->update([
        //     'region' => null,
        //     'application_type' => null,
        //     'requires_registration' => null
        // ]);
    }
}; 
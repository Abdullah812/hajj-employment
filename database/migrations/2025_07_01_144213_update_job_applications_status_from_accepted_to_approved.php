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
        // تحديث أي بيانات موجودة بحالة 'accepted' إلى 'approved'
        DB::table('job_applications')
            ->where('status', 'accepted')
            ->update(['status' => 'approved']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // إرجاع البيانات إلى حالتها السابقة
        DB::table('job_applications')
            ->where('status', 'approved')
            ->update(['status' => 'accepted']);
    }
};

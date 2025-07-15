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
        Schema::table('user_profiles', function (Blueprint $table) {
            // حذف الحقول المهنية غير المطلوبة
            $table->dropColumn([
                'education',
                'experience',
                'skills',
                'bio'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            // إعادة إضافة الحقول في حالة الرجوع
            $table->text('education')->nullable();
            $table->text('experience')->nullable();
            $table->text('skills')->nullable();
            $table->text('bio')->nullable();
        });
    }
};

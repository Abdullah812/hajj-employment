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
            // التحقق من وجود الأعمدة قبل إضافتها
            if (!Schema::hasColumn('user_profiles', 'qualification')) {
                $table->string('qualification')->nullable();
            }
            if (!Schema::hasColumn('user_profiles', 'iban_number')) {
                $table->string('iban_number')->nullable();
            }
            if (!Schema::hasColumn('user_profiles', 'iban_attachment')) {
                $table->string('iban_attachment')->nullable();
            }
            if (!Schema::hasColumn('user_profiles', 'national_address_attachment')) {
                $table->string('national_address_attachment')->nullable();
            }
            if (!Schema::hasColumn('user_profiles', 'national_id_attachment')) {
                $table->string('national_id_attachment')->nullable();
            }
            if (!Schema::hasColumn('user_profiles', 'experience_certificate')) {
                $table->string('experience_certificate')->nullable();
            }
            if (!Schema::hasColumn('user_profiles', 'academic_experience')) {
                $table->text('academic_experience')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'qualification',
                'iban_number',
                'iban_attachment',
                'national_address_attachment',
                'national_id_attachment',
                'experience_certificate',
                'academic_experience'
            ]);
        });
    }
}; 
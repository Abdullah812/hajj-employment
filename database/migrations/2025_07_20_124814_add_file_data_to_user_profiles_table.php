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
            // إضافة أعمدة لتخزين بيانات الملفات مباشرة في قاعدة البيانات
            $table->longText('cv_file_data')->nullable()->comment('بيانات ملف السيرة الذاتية مخزنة كـ base64');
            $table->string('cv_file_name')->nullable()->comment('اسم ملف السيرة الذاتية');
            $table->string('cv_file_type')->nullable()->comment('نوع ملف السيرة الذاتية');
            
            $table->longText('national_id_file_data')->nullable()->comment('بيانات ملف الهوية الوطنية مخزنة كـ base64');
            $table->string('national_id_file_name')->nullable()->comment('اسم ملف الهوية الوطنية');
            $table->string('national_id_file_type')->nullable()->comment('نوع ملف الهوية الوطنية');
            
            $table->longText('iban_file_data')->nullable()->comment('بيانات ملف الآيبان مخزنة كـ base64');
            $table->string('iban_file_name')->nullable()->comment('اسم ملف الآيبان');
            $table->string('iban_file_type')->nullable()->comment('نوع ملف الآيبان');
            
            $table->longText('national_address_file_data')->nullable()->comment('بيانات ملف العنوان الوطني مخزنة كـ base64');
            $table->string('national_address_file_name')->nullable()->comment('اسم ملف العنوان الوطني');
            $table->string('national_address_file_type')->nullable()->comment('نوع ملف العنوان الوطني');
            
            $table->longText('experience_file_data')->nullable()->comment('بيانات ملف شهادة الخبرة مخزنة كـ base64');
            $table->string('experience_file_name')->nullable()->comment('اسم ملف شهادة الخبرة');
            $table->string('experience_file_type')->nullable()->comment('نوع ملف شهادة الخبرة');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'cv_file_data', 'cv_file_name', 'cv_file_type',
                'national_id_file_data', 'national_id_file_name', 'national_id_file_type',
                'iban_file_data', 'iban_file_name', 'iban_file_type',
                'national_address_file_data', 'national_address_file_name', 'national_address_file_type',
                'experience_file_data', 'experience_file_name', 'experience_file_type'
            ]);
        });
    }
}; 
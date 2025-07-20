<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * إضافة حقول الملفات لجداول المحتوى
     */
    public function up(): void
    {
        // جدول الأخبار - إضافة حقول الصورة المميزة
        Schema::table('news', function (Blueprint $table) {
            $table->longText('image_file_data')->nullable()->after('image');
            $table->string('image_file_name')->nullable()->after('image_file_data');
            $table->string('image_file_type')->nullable()->after('image_file_name');
        });

        // جدول المعرض - إضافة حقول الصورة
        Schema::table('galleries', function (Blueprint $table) {
            $table->longText('image_file_data')->nullable()->after('image_path');
            $table->string('image_file_name')->nullable()->after('image_file_data');
            $table->string('image_file_type')->nullable()->after('image_file_name');
        });

        // جدول التوصيات - إضافة حقول صورة العميل
        Schema::table('testimonials', function (Blueprint $table) {
            $table->longText('client_image_file_data')->nullable()->after('client_image');
            $table->string('client_image_file_name')->nullable()->after('client_image_file_data');
            $table->string('client_image_file_type')->nullable()->after('client_image_file_name');
        });

        // جدول الفيديوهات - إضافة حقول الصورة المصغرة
        Schema::table('company_videos', function (Blueprint $table) {
            $table->longText('thumbnail_file_data')->nullable()->after('thumbnail_image');
            $table->string('thumbnail_file_name')->nullable()->after('thumbnail_file_data');
            $table->string('thumbnail_file_type')->nullable()->after('thumbnail_file_name');
        });

        // جدول طلبات مكة - إضافة حقول الملفات
        Schema::table('mecca_applications', function (Blueprint $table) {
            // السيرة الذاتية
            $table->longText('cv_file_data')->nullable()->after('cv_file');
            $table->string('cv_file_name')->nullable()->after('cv_file_data');
            $table->string('cv_file_type')->nullable()->after('cv_file_name');
            
            // الهوية الوطنية
            $table->longText('national_id_file_data')->nullable()->after('national_id_file');
            $table->string('national_id_file_name')->nullable()->after('national_id_file_data');
            $table->string('national_id_file_type')->nullable()->after('national_id_file_name');
            
            // الآيبان
            $table->longText('iban_file_data')->nullable()->after('iban_file');
            $table->string('iban_file_name')->nullable()->after('iban_file_data');
            $table->string('iban_file_type')->nullable()->after('iban_file_name');
            
            // ملفات الخبرة
            $table->longText('experience_files_data')->nullable()->after('experience_files');
            $table->text('experience_files_names')->nullable()->after('experience_files_data');
            $table->text('experience_files_types')->nullable()->after('experience_files_names');
            
            // ملفات أخرى
            $table->longText('other_files_data')->nullable()->after('other_files');
            $table->text('other_files_names')->nullable()->after('other_files_data');
            $table->text('other_files_types')->nullable()->after('other_files_names');
        });
    }

    /**
     * عكس العملية - حذف حقول الملفات
     */
    public function down(): void
    {
        Schema::table('news', function (Blueprint $table) {
            $table->dropColumn(['image_file_data', 'image_file_name', 'image_file_type']);
        });

        Schema::table('galleries', function (Blueprint $table) {
            $table->dropColumn(['image_file_data', 'image_file_name', 'image_file_type']);
        });

        Schema::table('testimonials', function (Blueprint $table) {
            $table->dropColumn(['client_image_file_data', 'client_image_file_name', 'client_image_file_type']);
        });

        Schema::table('company_videos', function (Blueprint $table) {
            $table->dropColumn(['thumbnail_file_data', 'thumbnail_file_name', 'thumbnail_file_type']);
        });

        Schema::table('mecca_applications', function (Blueprint $table) {
            $table->dropColumn([
                'cv_file_data', 'cv_file_name', 'cv_file_type',
                'national_id_file_data', 'national_id_file_name', 'national_id_file_type',
                'iban_file_data', 'iban_file_name', 'iban_file_type',
                'experience_files_data', 'experience_files_names', 'experience_files_types',
                'other_files_data', 'other_files_names', 'other_files_types'
            ]);
        });
    }
};

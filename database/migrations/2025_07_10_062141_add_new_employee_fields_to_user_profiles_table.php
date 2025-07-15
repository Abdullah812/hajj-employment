<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            // المعلومات الشخصية
            $table->string('national_id', 10)->nullable()->after('user_id');
            $table->string('phone', 10)->nullable()->after('national_id');
            $table->text('address')->nullable()->after('phone');
            $table->date('date_of_birth')->nullable()->after('address');
            
            // المؤهلات والخبرات
            $table->string('qualification')->nullable()->after('date_of_birth');
            $table->text('academic_experience')->nullable()->after('qualification');
            
            // المعلومات البنكية
            $table->string('iban_number', 24)->nullable()->after('academic_experience');
            
            // المرفقات
            $table->string('cv_path')->nullable()->after('iban_number');
            $table->string('iban_attachment')->nullable()->after('cv_path');
            $table->string('national_id_attachment')->nullable()->after('iban_attachment');
            $table->string('national_address_attachment')->nullable()->after('national_id_attachment');
            $table->string('experience_certificate')->nullable()->after('national_address_attachment');
        });
    }

    public function down()
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'national_id',
                'phone',
                'address',
                'date_of_birth',
                'qualification',
                'academic_experience',
                'iban_number',
                'cv_path',
                'iban_attachment',
                'national_id_attachment',
                'national_address_attachment',
                'experience_certificate'
            ]);
        });
    }
}; 
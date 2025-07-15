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
            if (!Schema::hasColumn('user_profiles', 'national_id')) {
                $table->string('national_id', 10)->nullable()->after('user_id');
            }
            if (!Schema::hasColumn('user_profiles', 'phone')) {
                $table->string('phone', 10)->nullable()->after('national_id');
            }
            if (!Schema::hasColumn('user_profiles', 'address')) {
                $table->text('address')->nullable()->after('phone');
            }
            if (!Schema::hasColumn('user_profiles', 'date_of_birth')) {
                $table->date('date_of_birth')->nullable()->after('address');
            }
            
            // المؤهلات والخبرات
            if (!Schema::hasColumn('user_profiles', 'qualification')) {
                $table->string('qualification')->nullable()->after('date_of_birth');
            }
            if (!Schema::hasColumn('user_profiles', 'academic_experience')) {
                $table->text('academic_experience')->nullable()->after('qualification');
            }
            
            // المعلومات البنكية
            if (!Schema::hasColumn('user_profiles', 'iban_number')) {
                $table->string('iban_number', 24)->nullable()->after('academic_experience');
            }
            
            // المرفقات
            if (!Schema::hasColumn('user_profiles', 'cv_path')) {
                $table->string('cv_path')->nullable()->after('iban_number');
            }
            if (!Schema::hasColumn('user_profiles', 'iban_attachment')) {
                $table->string('iban_attachment')->nullable()->after('cv_path');
            }
            if (!Schema::hasColumn('user_profiles', 'national_id_attachment')) {
                $table->string('national_id_attachment')->nullable()->after('iban_attachment');
            }
            if (!Schema::hasColumn('user_profiles', 'national_address_attachment')) {
                $table->string('national_address_attachment')->nullable()->after('national_id_attachment');
            }
            if (!Schema::hasColumn('user_profiles', 'experience_certificate')) {
                $table->string('experience_certificate')->nullable()->after('national_address_attachment');
            }
        });
    }

    public function down()
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            $columns = [
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
            ];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('user_profiles', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
}; 
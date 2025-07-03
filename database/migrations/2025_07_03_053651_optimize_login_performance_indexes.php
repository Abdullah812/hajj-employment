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
        // إضافة فهرسة محسنة لجدول users
        Schema::table('users', function (Blueprint $table) {
            // فهرسة على email و password معاً لتسريع تسجيل الدخول
            $table->index(['email', 'password'], 'users_email_password_index');
            // فهرسة على email_verified_at لتسريع التحقق من التوثيق
            $table->index('email_verified_at', 'users_email_verified_at_index');
            // فهرسة على created_at لتسريع الاستعلامات المؤرخة
            $table->index('created_at', 'users_created_at_index');
        });

        // إضافة فهرسة محسنة لجدول sessions
        Schema::table('sessions', function (Blueprint $table) {
            // فهرسة مركبة على user_id و last_activity لتسريع تنظيف الجلسات
            $table->index(['user_id', 'last_activity'], 'sessions_user_activity_index');
            // فهرسة على ip_address لتسريع استعلامات الأمان
            $table->index('ip_address', 'sessions_ip_address_index');
        });

        // إضافة فهرسة محسنة لجدول model_has_roles (Spatie Permission)
        Schema::table('model_has_roles', function (Blueprint $table) {
            // فهرسة على model_id و model_type لتسريع التحقق من الأدوار
            $table->index(['model_id', 'model_type'], 'model_has_roles_model_index');
            // فهرسة على role_id لتسريع استعلامات الأدوار
            $table->index('role_id', 'model_has_roles_role_index');
        });

        // إضافة فهرسة محسنة لجدول roles
        Schema::table('roles', function (Blueprint $table) {
            // فهرسة على name و guard_name لتسريع البحث عن الأدوار
            $table->index(['name', 'guard_name'], 'roles_name_guard_index');
            // فهرسة على created_at لتسريع الاستعلامات المؤرخة
            $table->index('created_at', 'roles_created_at_index');
        });

        // إضافة فهرسة محسنة لجدول permissions
        Schema::table('permissions', function (Blueprint $table) {
            // فهرسة على name و guard_name لتسريع البحث عن الصلاحيات
            $table->index(['name', 'guard_name'], 'permissions_name_guard_index');
            // فهرسة على created_at لتسريع الاستعلامات المؤرخة
            $table->index('created_at', 'permissions_created_at_index');
        });

        // تحسين جدول password_reset_tokens
        Schema::table('password_reset_tokens', function (Blueprint $table) {
            // فهرسة على created_at لتسريع تنظيف الرموز المنتهية الصلاحية
            $table->index('created_at', 'password_reset_tokens_created_at_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('users_email_password_index');
            $table->dropIndex('users_email_verified_at_index');
            $table->dropIndex('users_created_at_index');
        });

        Schema::table('sessions', function (Blueprint $table) {
            $table->dropIndex('sessions_user_activity_index');
            $table->dropIndex('sessions_ip_address_index');
        });

        Schema::table('model_has_roles', function (Blueprint $table) {
            $table->dropIndex('model_has_roles_model_index');
            $table->dropIndex('model_has_roles_role_index');
        });

        Schema::table('roles', function (Blueprint $table) {
            $table->dropIndex('roles_name_guard_index');
            $table->dropIndex('roles_created_at_index');
        });

        Schema::table('permissions', function (Blueprint $table) {
            $table->dropIndex('permissions_name_guard_index');
            $table->dropIndex('permissions_created_at_index');
        });

        Schema::table('password_reset_tokens', function (Blueprint $table) {
            $table->dropIndex('password_reset_tokens_created_at_index');
        });
    }
};

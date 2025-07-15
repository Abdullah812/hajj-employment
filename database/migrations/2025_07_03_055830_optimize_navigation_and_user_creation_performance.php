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
        // تحسين جدول users
        Schema::table('users', function (Blueprint $table) {
            // فهرسة مركبة على name و email لتسريع البحث
            $table->index(['name', 'email'], 'users_name_email_index');
        });

        // تحسين جدول user_profiles
        Schema::table('user_profiles', function (Blueprint $table) {
            // فهرسة على user_id لتسريع العلاقات
            $table->index('user_id', 'user_profiles_user_id_index');
        });

        // تحسين جدول notifications
        Schema::table('notifications', function (Blueprint $table) {
            // فهرسة مركبة على user_id و read_at لتسريع عرض الإشعارات
            $table->index(['user_id', 'read_at'], 'notifications_user_read_index');
            // فهرسة على created_at لتسريع الترتيب
            $table->index('created_at', 'notifications_created_at_index');
        });

        // تحسين جدول model_has_roles
        Schema::table('model_has_roles', function (Blueprint $table) {
            // فهرسة مركبة على model_id و role_id لتسريع التحقق من الأدوار
            $table->index(['model_id', 'role_id'], 'model_has_roles_model_role_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('users_name_email_index');
        });

        Schema::table('user_profiles', function (Blueprint $table) {
            $table->dropIndex('user_profiles_user_id_index');
        });

        Schema::table('notifications', function (Blueprint $table) {
            $table->dropIndex('notifications_user_read_index');
            $table->dropIndex('notifications_created_at_index');
        });

        Schema::table('model_has_roles', function (Blueprint $table) {
            $table->dropIndex('model_has_roles_model_role_index');
        });
    }
};

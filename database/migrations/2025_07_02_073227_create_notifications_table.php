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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('type'); // application_status, new_job, message, system, etc.
            $table->string('title');
            $table->text('message');
            $table->json('data')->nullable(); // بيانات إضافية مثل ID الطلب أو الوظيفة
            $table->string('icon')->default('bell'); // أيقونة الإشعار
            $table->string('color')->default('primary'); // لون الإشعار
            $table->string('action_url')->nullable(); // رابط للانتقال عند النقر
            $table->boolean('is_read')->default(false);
            $table->boolean('is_email_sent')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
            
            // فهارس لتحسين الأداء
            $table->index(['user_id', 'is_read']);
            $table->index(['user_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};

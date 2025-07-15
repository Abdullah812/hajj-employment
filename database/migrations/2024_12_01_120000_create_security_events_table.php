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
        Schema::create('security_events', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // نوع الحدث الأمني
            $table->text('message'); // رسالة الحدث
            $table->string('ip_address'); // عنوان IP
            $table->text('user_agent')->nullable(); // معلومات المتصفح
            $table->text('url')->nullable(); // الرابط المطلوب
            $table->string('method')->nullable(); // طريقة الطلب
            $table->unsignedBigInteger('user_id')->nullable(); // المستخدم (إذا كان مسجلاً)
            $table->string('session_id')->nullable(); // معرف الجلسة
            $table->text('referer')->nullable(); // المرجع
            $table->longText('headers')->nullable(); // رؤوس الطلب
            $table->longText('payload')->nullable(); // محتوى الطلب
            $table->enum('severity', ['low', 'medium', 'high', 'critical'])->default('medium'); // مستوى الخطورة
            $table->boolean('is_blocked')->default(false); // هل تم حظر الطلب
            $table->text('action_taken')->nullable(); // الإجراء المتخذ
            $table->timestamp('resolved_at')->nullable(); // وقت الحل
            $table->timestamps();
            
            // فهارس لتحسين الأداء
            $table->index('type');
            $table->index('ip_address');
            $table->index('user_id');
            $table->index('severity');
            $table->index('created_at');
            $table->index(['type', 'ip_address']);
            $table->index(['severity', 'created_at']);
            
            // مفتاح خارجي للمستخدم
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('security_events');
    }
}; 
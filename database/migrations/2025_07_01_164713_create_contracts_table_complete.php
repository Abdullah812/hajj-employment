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
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_application_id')->constrained()->onDelete('cascade');
            $table->foreignId('employee_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('company_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('job_id')->constrained('hajj_jobs')->onDelete('cascade');
            
            // معلومات العقد الأساسية
            $table->string('contract_number')->unique();
            $table->string('contract_type')->default('seasonal'); // موسمي
            $table->decimal('salary', 10, 2);
            $table->date('start_date');
            $table->date('end_date');
            $table->text('job_description');
            $table->integer('working_hours_per_day')->default(8);
            
            // معلومات الموظف في العقد
            $table->string('employee_name');
            $table->string('employee_nationality');
            $table->string('employee_national_id');
            $table->string('employee_phone');
            $table->string('employee_bank_account');
            $table->string('employee_bank_name');
            
            // معلومات الشركة في العقد
            $table->string('company_name');
            $table->string('company_address');
            $table->string('company_commercial_register');
            $table->string('company_email');
            $table->string('company_representative_name');
            $table->string('company_representative_title');
            
            // معلومات العقد
            $table->text('contract_terms')->nullable();
            $table->date('contract_date');
            $table->string('hijri_date')->nullable();
            
            // حالة العقد
            $table->enum('status', ['draft', 'sent', 'reviewed', 'signed', 'active', 'completed', 'cancelled'])->default('draft');
            
            // التوقيعات
            $table->string('employee_signature')->nullable();
            $table->timestamp('employee_signed_at')->nullable();
            $table->string('company_signature')->nullable();
            $table->timestamp('company_signed_at')->nullable();
            
            // معلومات إضافية
            $table->text('notes')->nullable();
            $table->string('contract_file_path')->nullable();
            $table->boolean('is_government_employee_confirmed')->default(false);
            $table->boolean('ajeer_system_registered')->default(false);
            
            $table->timestamps();
            
            // Indexes
            $table->index(['status', 'created_at']);
            $table->index(['employee_id', 'company_id']);
            $table->index(['start_date', 'end_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contracts');
    }
};

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class MeccaApplication extends Model
{
    protected $fillable = [
        'reference_number',
        'job_id',
        
        // البيانات الشخصية
        'full_name',
        'national_id',
        'birth_date',
        'nationality',
        'marital_status',
        'gender',
        
        // بيانات التواصل
        'phone',
        'phone_alt',
        'email',
        'address',
        'city',
        
        // المؤهلات العلمية
        'qualification',
        'specialization',
        'university',
        'graduation_year',
        'gpa',
        
        // الخبرات العملية
        'experience_years',
        'current_job',
        'current_employer',
        'current_salary',
        'experience_summary',
        
        // البيانات البنكية
        'iban_number',
        'bank_name',
        
        // المرفقات
        'national_id_file',
        'address_file',
        'certificate_file',
        'experience_files',
        'iban_file',
        'cv_file',
        'photo_file',
        'other_files',
        
        // بيانات إضافية
        'cover_letter',
        'skills',
        'languages',
        
        // حالة الطلب
        'status',
        'applied_at',
        'reviewed_at',
        'reviewed_by',
        'review_notes',
        'rejection_reason',
        
        // تتبع التواصل
        'sms_sent',
        'email_sent',
        'last_contact_at',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'graduation_year' => 'integer',
        'gpa' => 'decimal:2',
        'experience_years' => 'integer',
        'current_salary' => 'decimal:2',
        'experience_files' => 'array',
        'other_files' => 'array',
        'applied_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'last_contact_at' => 'datetime',
        'sms_sent' => 'boolean',
        'email_sent' => 'boolean',
    ];

    /**
     * العلاقة مع الوظيفة
     */
    public function job(): BelongsTo
    {
        return $this->belongsTo(HajjJob::class, 'job_id');
    }

    /**
     * العلاقة مع مراجع الطلب
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * توليد رقم مرجعي فريد
     */
    public static function generateReferenceNumber(): string
    {
        $year = date('Y');
        $month = date('m');
        $randomNumber = mt_rand(1000, 9999);
        
        $prefix = "MC{$year}{$month}";
        $reference = $prefix . $randomNumber;
        
        // التأكد من عدم تكرار الرقم
        while (self::where('reference_number', $reference)->exists()) {
            $randomNumber = mt_rand(1000, 9999);
            $reference = $prefix . $randomNumber;
        }
        
        return $reference;
    }

    /**
     * الحصول على نص الحالة بالعربية
     */
    public function getStatusTextAttribute(): string
    {
        $statuses = [
            'pending' => 'في انتظار المراجعة',
            'approved' => 'مقبول',
            'rejected' => 'مرفوض',
            'withdrawn' => 'منسحب',
        ];
        
        return $statuses[$this->status] ?? $this->status;
    }

    /**
     * الحصول على نص الحالة الاجتماعية
     */
    public function getMaritalStatusTextAttribute(): string
    {
        $statuses = [
            'single' => 'أعزب',
            'married' => 'متزوج',
            'divorced' => 'مطلق',
            'widowed' => 'أرمل',
        ];
        
        return $statuses[$this->marital_status] ?? $this->marital_status;
    }

    /**
     * الحصول على نص الجنس
     */
    public function getGenderTextAttribute(): string
    {
        return $this->gender === 'male' ? 'ذكر' : 'أنثى';
    }

    /**
     * الحصول على العمر
     */
    public function getAgeAttribute(): int
    {
        return $this->birth_date ? $this->birth_date->age : 0;
    }

    /**
     * التحقق من اكتمال البيانات الأساسية
     */
    public function isBasicDataComplete(): bool
    {
        return !empty($this->full_name) &&
               !empty($this->national_id) &&
               !empty($this->phone) &&
               !empty($this->email) &&
               !empty($this->birth_date);
    }

    /**
     * التحقق من اكتمال المرفقات الأساسية
     */
    public function isRequiredFilesComplete(): bool
    {
        return !empty($this->national_id_file) &&
               !empty($this->cv_file) &&
               !empty($this->iban_file);
    }

    /**
     * الحصول على نسبة اكتمال الطلب
     */
    public function getCompletionPercentageAttribute(): int
    {
        $fields = [
            'full_name', 'national_id', 'birth_date', 'phone', 'email',
            'address', 'qualification', 'iban_number', 'bank_name',
            'national_id_file', 'cv_file', 'iban_file'
        ];
        
        $completedFields = 0;
        foreach ($fields as $field) {
            if (!empty($this->$field)) {
                $completedFields++;
            }
        }
        
        return round(($completedFields / count($fields)) * 100);
    }

    /**
     * تحديث حالة إرسال الرسائل
     */
    public function markSMSSent(): void
    {
        $this->update([
            'sms_sent' => true,
            'last_contact_at' => now()
        ]);
    }

    public function markEmailSent(): void
    {
        $this->update([
            'email_sent' => true,
            'last_contact_at' => now()
        ]);
    }

    /**
     * الموافقة على الطلب
     */
    public function approve(User $reviewer, string $notes = null): void
    {
        $this->update([
            'status' => 'approved',
            'reviewed_at' => now(),
            'reviewed_by' => $reviewer->id,
            'review_notes' => $notes,
        ]);
    }

    /**
     * رفض الطلب
     */
    public function reject(User $reviewer, string $reason, string $notes = null): void
    {
        $this->update([
            'status' => 'rejected',
            'reviewed_at' => now(),
            'reviewed_by' => $reviewer->id,
            'rejection_reason' => $reason,
            'review_notes' => $notes,
        ]);
    }

    /**
     * Scopes للاستعلامات
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeForJob($query, $jobId)
    {
        return $query->where('job_id', $jobId);
    }

    public function scopeRecentFirst($query)
    {
        return $query->orderBy('applied_at', 'desc');
    }
}

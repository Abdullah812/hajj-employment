<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Contract extends Model
{
    protected $fillable = [
        'job_application_id',
        'employee_id',
        'department_id',
        'job_id',
        'contract_number',
        'contract_type',
        'salary',
        'salary_in_words',
        'start_date',
        'end_date',
        'job_description',
        'working_hours_per_day',
        'employee_name',
        'employee_nationality',
        'employee_gender',
        'employee_national_id',
        'employee_phone',
        'employee_address',
        'employee_bank_account',
        'employee_bank_name',
        'department_name',
        'department_address',
        'department_national_address',
        'department_commercial_register',
        'department_email',
        'department_phone',
        'department_representative_name',
        'department_representative_title',
        'contract_terms',
        'special_terms',
        'contract_date',
        'contract_day_of_week',
        'hijri_date',
        'hijri_contract_start_date',
        'hijri_contract_end_date',
        'status',
        'employee_signature',
        'employee_signed_at',
        'department_signature',
        'department_signed_at',
        'digital_signature_data',
        'notes',
        'contract_file_path',
        'is_government_employee_confirmed',
        'ajeer_system_registered',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'contract_date' => 'date',
        'employee_signed_at' => 'datetime',
        'department_signed_at' => 'datetime',
        'salary' => 'decimal:2',
        'is_government_employee_confirmed' => 'boolean',
        'ajeer_system_registered' => 'boolean',
    ];

    // العلاقات
    public function jobApplication(): BelongsTo
    {
        return $this->belongsTo(JobApplication::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(User::class, 'department_id');
    }

    public function job(): BelongsTo
    {
        return $this->belongsTo(HajjJob::class, 'job_id');
    }

    // Accessors
    public function getStatusTextAttribute(): string
    {
        return match($this->status) {
            'draft' => 'مسودة',
            'sent' => 'مُرسل للموظف',
            'reviewed' => 'تم الاطلاع عليه',
            'signed' => 'موقع',
            'active' => 'نشط',
            'completed' => 'مكتمل',
            'cancelled' => 'ملغي',
            default => 'غير محدد'
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'draft' => 'secondary',
            'sent' => 'info',
            'reviewed' => 'warning',
            'signed' => 'success',
            'active' => 'primary',
            'completed' => 'success',
            'cancelled' => 'danger',
            default => 'secondary'
        };
    }

    public function getFormattedSalaryAttribute(): string
    {
        return number_format($this->salary, 2) . ' ريال';
    }

    public function getSalaryInWordsAttribute(): string
    {
        if (!empty($this->attributes['salary_in_words'])) {
            return $this->attributes['salary_in_words'];
        }
        
        // تحويل تلقائي للراتب إلى كلمات (مبسط)
        $salary = (int) $this->salary;
        $thousands = intval($salary / 1000);
        $hundreds = intval(($salary % 1000) / 100);
        
        $words = '';
        if ($thousands > 0) {
            $words .= $this->numberToArabicWords($thousands) . ' آلاف ';
        }
        if ($hundreds > 0) {
            $words .= $this->numberToArabicWords($hundreds) . ' مائة ';
        }
        
        return trim($words) . ' ريال فقط';
    }

    private function numberToArabicWords(int $number): string
    {
        $ones = [
            '', 'واحد', 'اثنان', 'ثلاثة', 'أربعة', 'خمسة', 'ستة', 'سبعة', 'ثمانية', 'تسعة'
        ];
        
        $tens = [
            '', '', 'عشرون', 'ثلاثون', 'أربعون', 'خمسون', 'ستون', 'سبعون', 'ثمانون', 'تسعون'
        ];
        
        $hundreds = [
            '', 'مائة', 'مائتان', 'ثلاثمائة', 'أربعمائة', 'خمسمائة', 'ستمائة', 'سبعمائة', 'ثمانمائة', 'تسعمائة'
        ];
        
        if ($number == 0) return '';
        if ($number < 10) return $ones[$number];
        if ($number == 10) return 'عشرة';
        if ($number < 20) {
            $specialTens = [
                11 => 'أحد عشر', 12 => 'اثنا عشر', 13 => 'ثلاثة عشر', 14 => 'أربعة عشر',
                15 => 'خمسة عشر', 16 => 'ستة عشر', 17 => 'سبعة عشر', 18 => 'ثمانية عشر',
                19 => 'تسعة عشر'
            ];
            return $specialTens[$number];
        }
        if ($number < 100) {
            $ten = intval($number / 10);
            $one = $number % 10;
            return $tens[$ten] . ($one ? ' و' . $ones[$one] : '');
        }
        if ($number < 1000) {
            $hundred = intval($number / 100);
            $remainder = $number % 100;
            return $hundreds[$hundred] . ($remainder ? ' و' . $this->numberToArabicWords($remainder) : '');
        }
        
        return (string) $number;
    }

    public function getDurationInDaysAttribute(): int
    {
        return $this->start_date->diffInDays($this->end_date);
    }

    public function getIsSignedAttribute(): bool
    {
        return !is_null($this->employee_signed_at) && !is_null($this->department_signed_at);
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->end_date < now()->toDateString();
    }

    public function getCanBeSignedAttribute(): bool
    {
        return in_array($this->status, ['sent', 'reviewed']);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeSigned($query)
    {
        return $query->whereNotNull('employee_signed_at')
                    ->whereNotNull('department_signed_at');
    }

    public function scopePending($query)
    {
        return $query->whereIn('status', ['sent', 'reviewed']);
    }

    public function scopeExpired($query)
    {
        return $query->where('end_date', '<', now()->toDateString());
    }

    // طرق مساعدة
    public static function generateContractNumber(): string
    {
        $year = now()->format('Y');
        $lastContract = static::whereYear('created_at', $year)
                             ->orderBy('id', 'desc')
                             ->first();
        
        $number = $lastContract ? intval(substr($lastContract->contract_number, -4)) + 1 : 1;
        
        return sprintf('MMS-%s-%04d', $year, $number);
    }

    public function signByEmployee(string $signature): bool
    {
        return $this->update([
            'employee_signature' => $signature,
            'employee_signed_at' => now(),
            'status' => $this->department_signed_at ? 'signed' : 'reviewed'
        ]);
    }

    public function signByDepartment(string $signature): bool
    {
        return $this->update([
            'department_signature' => $signature,
            'department_signed_at' => now(),
            'status' => $this->employee_signed_at ? 'signed' : 'sent'
        ]);
    }

    public function activate(): bool
    {
        if ($this->is_signed) {
            return $this->update(['status' => 'active']);
        }
        return false;
    }

    public function complete(): bool
    {
        return $this->update(['status' => 'completed']);
    }

    public function cancel(string $reason = null): bool
    {
        return $this->update([
            'status' => 'cancelled',
            'notes' => $this->notes . "\n\nسبب الإلغاء: " . $reason
        ]);
    }

    protected static function booted(): void
    {
        static::creating(function (Contract $contract) {
            if (empty($contract->contract_number)) {
                $contract->contract_number = static::generateContractNumber();
            }
            
            if (empty($contract->contract_date)) {
                $contract->contract_date = now()->toDateString();
            }
            
            if (empty($contract->contract_day_of_week)) {
                $days = ['الأحد', 'الاثنين', 'الثلاثاء', 'الأربعاء', 'الخميس', 'الجمعة', 'السبت'];
                $contract->contract_day_of_week = $days[now()->dayOfWeek];
            }
            
            if (empty($contract->hijri_date)) {
                $contract->hijri_date = now()->format('d/m/Y') . 'هـ';
            }
            
            // إضافة التواريخ الهجرية للبداية والنهاية (مبسط)
            if (empty($contract->hijri_contract_start_date) && !empty($contract->start_date)) {
                $contract->hijri_contract_start_date = Carbon::parse($contract->start_date)->format('d/m/Y') . 'هـ';
            }
            
            if (empty($contract->hijri_contract_end_date) && !empty($contract->end_date)) {
                $contract->hijri_contract_end_date = Carbon::parse($contract->end_date)->format('d/m/Y') . 'هـ';
            }
        });
    }
}

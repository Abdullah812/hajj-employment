<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    protected $fillable = [
        'user_id',
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

    protected $casts = [
        'date_of_birth' => 'date'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

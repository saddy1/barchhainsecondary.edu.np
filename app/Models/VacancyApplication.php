<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VacancyApplication extends Model
{
    protected $fillable = [
        'vacancy_id', 'user_id', 'full_name', 'email', 'phone', 'address',
        'qualification', 'experience', 'motivation', 'cv_path',
        'profile_photo', 'date_of_birth', 'gender',
        'father_name', 'mother_name', 'permanent_address', 'temporary_address',
        'citizenship_no', 'citizen_front_path', 'citizen_back_path', 'signature_path',
        'status', 'admin_remarks',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
    ];

    public function vacancy()
    {
        return $this->belongsTo(Vacancy::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

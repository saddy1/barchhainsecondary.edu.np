<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Admission extends Model
{
    protected $fillable = [
        'student_name', 'dob', 'gender', 'guardian_name', 
        'phone', 'email', 'address', 'applied_grade', 
        'previous_school', 'status', 'admin_remarks'
    ];
}
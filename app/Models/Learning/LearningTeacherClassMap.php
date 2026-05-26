<?php

namespace App\Models\Learning;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LearningTeacherClassMap extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'learning_class_id',
        'assigned_by',
    ];

    public function teacher()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function learningClass()
    {
        return $this->belongsTo(LearningClass::class);
    }

    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }
}

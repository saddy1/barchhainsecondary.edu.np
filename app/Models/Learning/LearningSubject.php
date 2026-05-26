<?php

namespace App\Models\Learning;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LearningSubject extends Model
{
    use HasFactory;

    protected $fillable = ['learning_class_id', 'name', 'code', 'is_active'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function learningClass()
    {
        return $this->belongsTo(LearningClass::class);
    }

    public function courses()
    {
        return $this->hasMany(LearningCourse::class);
    }

    public function assignedTeachers()
    {
        return $this->belongsToMany(\App\Models\User::class, 'learning_teacher_subject_maps')
            ->withPivot('assigned_by')
            ->withTimestamps();
    }
}

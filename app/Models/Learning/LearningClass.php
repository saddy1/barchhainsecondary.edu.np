<?php

namespace App\Models\Learning;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LearningClass extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'sort_order', 'is_active'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function subjects()
    {
        return $this->hasMany(LearningSubject::class);
    }

    public function courses()
    {
        return $this->hasMany(LearningCourse::class);
    }

    public function teacherMaps()
    {
        return $this->hasMany(LearningTeacherClassMap::class);
    }

    public function teachers()
    {
        return $this->belongsToMany(\App\Models\User::class, 'learning_teacher_class_maps')
            ->withPivot('assigned_by')
            ->withTimestamps();
    }
}

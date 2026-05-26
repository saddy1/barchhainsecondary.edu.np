<?php

namespace App\Models\Learning;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class LearningQuiz extends Model
{
    protected $fillable = [
        'title', 'description', 'learning_course_id', 'learning_lesson_id',
        'time_limit_minutes', 'pass_percentage', 'max_attempts',
        'is_published', 'created_by', 'sort_order',
    ];

    protected $casts = ['is_published' => 'boolean'];

    public function course()
    {
        return $this->belongsTo(LearningCourse::class, 'learning_course_id');
    }

    public function lesson()
    {
        return $this->belongsTo(LearningLesson::class, 'learning_lesson_id');
    }

    public function questions()
    {
        return $this->hasMany(LearningQuizQuestion::class)->orderBy('sort_order');
    }

    public function attempts()
    {
        return $this->hasMany(LearningQuizAttempt::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function totalMarks(): int
    {
        return $this->questions->sum('marks');
    }
}

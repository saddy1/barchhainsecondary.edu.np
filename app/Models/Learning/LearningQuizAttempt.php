<?php

namespace App\Models\Learning;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class LearningQuizAttempt extends Model
{
    protected $fillable = [
        'user_id', 'learning_quiz_id', 'score', 'total_marks',
        'passed', 'started_at', 'completed_at',
    ];

    protected $casts = [
        'passed'       => 'boolean',
        'started_at'   => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function quiz()
    {
        return $this->belongsTo(LearningQuiz::class, 'learning_quiz_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function answers()
    {
        return $this->hasMany(LearningQuizAnswer::class);
    }

    public function percentage(): float
    {
        if (! $this->total_marks) return 0;
        return round(($this->score / $this->total_marks) * 100, 1);
    }
}

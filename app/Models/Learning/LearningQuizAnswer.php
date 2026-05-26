<?php

namespace App\Models\Learning;

use Illuminate\Database\Eloquent\Model;

class LearningQuizAnswer extends Model
{
    protected $fillable = [
        'learning_quiz_attempt_id', 'learning_quiz_question_id',
        'selected_option_id', 'text_answer', 'is_correct', 'marks_awarded',
    ];

    protected $casts = ['is_correct' => 'boolean'];

    public function attempt()
    {
        return $this->belongsTo(LearningQuizAttempt::class, 'learning_quiz_attempt_id');
    }

    public function question()
    {
        return $this->belongsTo(LearningQuizQuestion::class, 'learning_quiz_question_id');
    }

    public function selectedOption()
    {
        return $this->belongsTo(LearningQuizOption::class, 'selected_option_id');
    }
}

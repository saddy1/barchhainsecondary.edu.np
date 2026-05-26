<?php

namespace App\Models\Learning;

use Illuminate\Database\Eloquent\Model;

class LearningQuizOption extends Model
{
    protected $fillable = ['learning_quiz_question_id', 'option_text', 'is_correct', 'sort_order'];

    protected $casts = ['is_correct' => 'boolean'];

    public function question()
    {
        return $this->belongsTo(LearningQuizQuestion::class, 'learning_quiz_question_id');
    }
}

<?php

namespace App\Models\Learning;

use Illuminate\Database\Eloquent\Model;

class LearningQuizQuestion extends Model
{
    protected $fillable = ['learning_quiz_id', 'question_text', 'type', 'marks', 'sort_order', 'explanation'];

    public function quiz()
    {
        return $this->belongsTo(LearningQuiz::class, 'learning_quiz_id');
    }

    public function options()
    {
        return $this->hasMany(LearningQuizOption::class)->orderBy('sort_order');
    }

    public function answers()
    {
        return $this->hasMany(LearningQuizAnswer::class);
    }
}

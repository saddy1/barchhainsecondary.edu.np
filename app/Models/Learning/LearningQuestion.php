<?php

namespace App\Models\Learning;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LearningQuestion extends Model
{
    use HasFactory;

    protected $fillable = ['learning_quiz_id', 'question', 'marks', 'sort_order'];

    public function quiz()
    {
        return $this->belongsTo(LearningQuiz::class, 'learning_quiz_id');
    }

    public function options()
    {
        return $this->hasMany(LearningQuestionOption::class);
    }
}

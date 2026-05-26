<?php

namespace App\Models\Learning;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LearningQuestionOption extends Model
{
    use HasFactory;

    protected $fillable = ['learning_question_id', 'option_text', 'is_correct'];

    protected function casts(): array
    {
        return [
            'is_correct' => 'boolean',
        ];
    }

    public function question()
    {
        return $this->belongsTo(LearningQuestion::class, 'learning_question_id');
    }
}

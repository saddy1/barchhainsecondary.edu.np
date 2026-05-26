<?php

namespace App\Models\Learning;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LearningChapter extends Model
{
    use HasFactory;

    protected $fillable = [
        'learning_course_id',
        'title',
        'description',
        'sort_order',
    ];

    public function course()
    {
        return $this->belongsTo(LearningCourse::class, 'learning_course_id');
    }

    public function lessons()
    {
        return $this->hasMany(LearningLesson::class, 'learning_chapter_id')->orderBy('sort_order');
    }
}

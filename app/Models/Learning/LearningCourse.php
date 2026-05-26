<?php

namespace App\Models\Learning;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LearningCourse extends Model
{
    use HasFactory;

    protected $fillable = [
        'learning_class_id',
        'learning_subject_id',
        'created_by',
        'title',
        'slug',
        'description',
        'status',
        'sort_order',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function learningClass()
    {
        return $this->belongsTo(LearningClass::class);
    }

    public function subject()
    {
        return $this->belongsTo(LearningSubject::class, 'learning_subject_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function chapters()
    {
        return $this->hasMany(LearningChapter::class)->orderBy('sort_order');
    }

    public function lessons()
    {
        return $this->hasMany(LearningLesson::class)->orderBy('sort_order');
    }

    public function quizzes()
    {
        return $this->hasMany(LearningQuiz::class)->orderBy('sort_order');
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }
}

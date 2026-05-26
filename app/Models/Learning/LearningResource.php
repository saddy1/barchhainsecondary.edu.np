<?php

namespace App\Models\Learning;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LearningResource extends Model
{
    use HasFactory;

    protected $fillable = [
        'learning_class_id',
        'learning_subject_id',
        'created_by',
        'title',
        'type',
        'file_path',
        'description',
        'is_published',
    ];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
        ];
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
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    public function getFileUrlAttribute(): ?string
    {
        return $this->file_path ? asset($this->file_path) : null;
    }
}

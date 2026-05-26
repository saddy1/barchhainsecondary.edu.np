<?php

namespace App\Models\Learning;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LearningLesson extends Model
{
    use HasFactory;

    protected $fillable = [
        'learning_course_id',
        'learning_chapter_id',
        'title',
        'type',
        'description',
        'content_body',
        'video_url',
        'audio_url',
        'material_path',
        'duration_seconds',
        'sort_order',
        'is_published',
        'is_free',
    ];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'is_free'      => 'boolean',
        ];
    }

    public function getEmbedUrlAttribute(): ?string
    {
        if (! $this->youtube_video_id) {
            return null;
        }

        return 'https://www.youtube-nocookie.com/embed/' . $this->youtube_video_id
            . '?enablejsapi=1&rel=0&modestbranding=1&playsinline=1&controls=0&disablekb=1&fs=0&iv_load_policy=3';
    }

    public function getYoutubeVideoIdAttribute(): ?string
    {
        if (! $this->video_url) {
            return null;
        }

        if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/|youtube-nocookie\.com\/embed\/)([A-Za-z0-9_\-]{11})/', $this->video_url, $m)) {
            return $m[1];
        }

        return null;
    }

    public function getStreamUrlAttribute(): ?string
    {
        return $this->youtube_video_id ? null : $this->video_url;
    }

    public function course()
    {
        return $this->belongsTo(LearningCourse::class, 'learning_course_id');
    }

    public function chapter()
    {
        return $this->belongsTo(LearningChapter::class, 'learning_chapter_id');
    }

    public function quiz()
    {
        return $this->hasOne(LearningQuiz::class);
    }
}

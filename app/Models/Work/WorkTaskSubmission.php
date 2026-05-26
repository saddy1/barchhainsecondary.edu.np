<?php

namespace App\Models\Work;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkTaskSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'work_task_id',
        'user_id',
        'work_group_id',
        'submitted_by',
        'comment',
        'evidence_link',
        'evidence_file',
        'submitted_at',
        'status',
        'score',
        'review_note',
        'payout_amount',
        'reviewed_by',
        'reviewed_at',
    ];

    protected function casts(): array
    {
        return [
            'submitted_at' => 'datetime',
            'reviewed_at' => 'datetime',
            'payout_amount' => 'decimal:2',
        ];
    }

    public function task()
    {
        return $this->belongsTo(WorkTask::class, 'work_task_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function group()
    {
        return $this->belongsTo(WorkGroup::class, 'work_group_id');
    }

    public function submittedBy()
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function getEvidenceUrlAttribute(): ?string
    {
        return $this->evidence_file ? asset($this->evidence_file) : null;
    }
}

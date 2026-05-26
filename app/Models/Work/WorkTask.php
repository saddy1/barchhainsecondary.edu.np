<?php

namespace App\Models\Work;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkTask extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'category',
        'due_date',
        'max_score',
        'incentive_amount',
        'assignment_type',
        'assigned_user_id',
        'work_group_id',
        'group_submission_mode',
        'group_payment_mode',
        'late_penalty_percent',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'due_date' => 'date',
            'incentive_amount' => 'decimal:2',
            'late_penalty_percent' => 'decimal:2',
        ];
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }

    public function group()
    {
        return $this->belongsTo(WorkGroup::class, 'work_group_id');
    }

    public function submissions()
    {
        return $this->hasMany(WorkTaskSubmission::class);
    }

    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        if ($user->canAccess('work-tasks.review') || $user->canAccess('work-tasks.create')) {
            return $query;
        }

        return $query->where(function (Builder $visible) use ($user) {
            $visible->where('assigned_user_id', $user->id)
                ->orWhereHas('group.members', fn (Builder $members) => $members->where('users.id', $user->id));
        });
    }

    public function isAssignedTo(User $user): bool
    {
        if ((int) $this->assigned_user_id === (int) $user->id) {
            return true;
        }

        if ($this->relationLoaded('group') && $this->group) {
            return $this->group->members->contains('id', $user->id);
        }

        return $this->group?->members()->where('users.id', $user->id)->exists() ?? false;
    }

    public function isSharedGroupTask(): bool
    {
        return $this->assignment_type === 'group' && $this->group_submission_mode === 'shared';
    }
}

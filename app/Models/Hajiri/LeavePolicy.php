<?php

namespace App\Models\Hajiri;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeavePolicy extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'short_code', 'days_allowed',
        'period_type', 'applicable_to', 'is_active',
    ];

    protected $casts = ['is_active' => 'boolean'];

    public function requests()
    {
        return $this->hasMany(LeaveRequest::class);
    }

    public function getPeriodLabelAttribute(): string
    {
        return $this->period_type === 'annual' ? 'Annual' : 'Tenure';
    }

    public function getApplicableLabelAttribute(): string
    {
        return match ($this->applicable_to) {
            'teaching'     => 'Teaching Staff',
            'non_teaching' => 'Non-Teaching Staff',
            default        => 'All Staff',
        };
    }
}

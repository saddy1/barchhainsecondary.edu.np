<?php

namespace App\Models\Hajiri;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class NepaliCalendarYear extends Model
{
    protected $fillable = [
        'bs_year',
        'months',
        'notes',
        'updated_by',
    ];

    protected $casts = [
        'bs_year' => 'integer',
        'months' => 'array',
    ];

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function monthDays(): array
    {
        return array_values(array_map('intval', $this->months ?: []));
    }
}

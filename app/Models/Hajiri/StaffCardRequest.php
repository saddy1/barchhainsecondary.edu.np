<?php

namespace App\Models\Hajiri;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class StaffCardRequest extends Model
{
    protected $fillable = ['user_id', 'reason', 'status', 'admin_note'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'approved'  => 'green',
            'printed'   => 'blue',
            'collected' => 'purple',
            'rejected'  => 'red',
            default     => 'amber',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'approved'  => 'Approved',
            'printed'   => 'Printed',
            'collected' => 'Collected',
            'rejected'  => 'Rejected',
            default     => 'Pending',
        };
    }
}

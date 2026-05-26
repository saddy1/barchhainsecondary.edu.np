<?php

namespace App\Models\Card;

use Illuminate\Database\Eloquent\Model;

class CardRequest extends Model
{
    protected $fillable = ['student_id', 'status', 'admin_note'];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending'   => 'Pending Payment Verification',
            'approved'  => 'Approved – Ready to Collect',
            'collected' => 'Collected',
            'rejected'  => 'Rejected',
            default     => ucfirst($this->status),
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'pending'   => 'yellow',
            'approved'  => 'green',
            'collected' => 'blue',
            'rejected'  => 'red',
            default     => 'gray',
        };
    }
}

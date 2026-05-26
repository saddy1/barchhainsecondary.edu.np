<?php

namespace App\Models\Card;

use Illuminate\Database\Eloquent\Model;

class UpdateRequest extends Model
{
    protected $fillable = ['student_id', 'requested_changes', 'status', 'admin_note'];

    protected $casts = [
        'requested_changes' => 'array',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}

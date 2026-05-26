<?php

namespace App\Models\Card;

use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    protected $fillable = ['department_id', 'name', 'is_active'];
    protected $casts = ['is_active' => 'boolean'];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}

<?php

namespace App\Models\Card;

use Illuminate\Database\Eloquent\Model;

class MemberType extends Model
{
    protected $fillable = ['organization_id', 'name', 'is_active'];
    protected $casts = ['is_active' => 'boolean'];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }
}

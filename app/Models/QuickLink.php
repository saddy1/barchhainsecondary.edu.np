<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuickLink extends Model
{
    protected $fillable = [
        'title',
        'url',
        'open_in_new_tab',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'open_in_new_tab' => 'boolean',
        'is_active'       => 'boolean',
        'sort_order'      => 'integer',
    ];
}

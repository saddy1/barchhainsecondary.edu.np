<?php

namespace App\Models\Hajiri;

use Illuminate\Database\Eloquent\Model;

class Holiday extends Model 
{

    protected $table = 'holiday';
    public $timestamps = true;
    protected $fillable = array('label', 'alias', 'date', 'color', 'status', 'dsa');

    protected $casts = [
        'date' => 'date',
        'status' => 'boolean',
        'dsa' => 'boolean',
    ];

}

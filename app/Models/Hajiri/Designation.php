<?php

namespace App\Models\Hajiri;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Designation extends Model 
{

    protected $table = 'designation';
    public $timestamps = true;

    use SoftDeletes;

    protected $dates = ['deleted_at'];
    protected $fillable = array('label', 'alias', 'status');
    protected $visible = array('label','id','alias','status');

}
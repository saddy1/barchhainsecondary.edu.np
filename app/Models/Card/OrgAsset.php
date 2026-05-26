<?php

namespace App\Models\Card;

use Illuminate\Database\Eloquent\Model;

class OrgAsset extends Model
{
    protected $fillable = ['name', 'type', 'path'];
}

<?php

namespace App\Models\Hajiri;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Http\Controllers\Hajiri\NepaliCalendarController;
use App\Models\User;

class Leave extends Model
{
    use HasFactory;

    public function user(){
        return $this->hasOne(User::class,'id','user_id');
    }
    public function type(){
        return $this->hasOne(TypeOfLeave::class,'id','leave_id');
    }
    
    public function getDateAttribute($value)
    {
        // return ucfirst($value);
        $dateArr = explode("-",$value);
        // return $dateArr[0];
        $np = new NepaliCalendarController();
        $converted = $np->ad_2_bs($dateArr[0],$dateArr[1],$dateArr[2]);
        return "{$converted['year']}-{$converted['month']}-{$converted['date']}";
    }
}

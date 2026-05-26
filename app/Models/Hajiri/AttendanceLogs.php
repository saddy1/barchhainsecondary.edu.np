<?php

namespace App\Models\Hajiri;
use App\Http\Controllers\Hajiri\NepaliCalendarController;
use Carbon\Carbon;

use Illuminate\Database\Eloquent\Model;

class AttendanceLogs extends Model 
{

    protected $table = 'attendacelogs';
    public $timestamps = true;

    public function getAtAttribute($value)
    {
        $cObj = new NepaliCalendarController();

        $carbonDate = Carbon::parse($value);

        $valueString = $carbonDate->toDateTimeString();
        $valueArray  = explode(' ',$valueString);
        $valueArrayDate  = explode('-',$valueArray[0]);
        $valueTime  = $valueArray[1];
        
        $aaja_bs_intermediate = $cObj->ad_2_bs($valueArrayDate[0],$valueArrayDate[1],$valueArrayDate[2]);
        $valueBS = "{$aaja_bs_intermediate['year']}-{$aaja_bs_intermediate['month']}-{$aaja_bs_intermediate['date']} {$valueTime}";    
        
        return $valueBS;
    }
}
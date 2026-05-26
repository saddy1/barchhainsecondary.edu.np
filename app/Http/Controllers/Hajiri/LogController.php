<?php

namespace App\Http\Controllers\Hajiri;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Hajiri\AttendanceLogs;
use App\Models\Hajiri\Holiday;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Auth;

class LogController extends Controller
{
    private $attnLogs;
    private $npCal;
    private $holiday;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(AttendanceLogs $attnLogs,Holiday $holiday)
    {
        $this->attnLogs = $attnLogs;
        $this->npCal = new NepaliCalendarController();
        $this->holiday = $holiday;
    }

    public function index()
    {
        return $this->showlogs();
    }

    public function showlogs($type = 'brief', $year = null,$month = null, $user = null)
    {
        if($year == null || $month == null){
            $y_ = Date('Y');
            $m_ = Date('m');
            $getToday =  $this->getToday($y_,$m_,1);
        }
        else
        {
            $tempDate =  $this->npCal->bs_2_ad($year,$month,"27");
            $getToday =  $this->getToday($tempDate['year'],$tempDate['month'],1);
        }      

        if($user == null){
            $user = Auth::user()->device_id;
        }

        $startA_ = "{$getToday['yearAD']}-{$getToday['monthAD_A']}-{$getToday['firstAD']}";
        $endA_ = "{$getToday['yearAD']}-{$getToday['monthAD_B']}-{$getToday['lastAD']}";

        $startA = Carbon::parse("{$startA_}");
        $endA = Carbon::parse("{$endA_}");

        $start = $startA->format('Y-m-d').' 00:00:00';
        $end = $endA->format('Y-m-d').' 23:59:59';
        
        // return '1';

        $checkIN =  $checkOUT = 'N/A';
        $attendanceLogs = $this->attnLogs->where('user_id','LIKE',$user)->where('at','>=',$start)->where('at','<=',$end)->orderBy('at')->get();
        // return $attendanceLogs;
        
        $holidays_ = $this->holiday->where('date','>=',$start)->orderBy('date','DESC')->get();
        $holidays = array();
        foreach($holidays_ as $holiday)
        {
            $holidays[$holiday->date->format('Y-m-d')] = $holiday;
        }
        if(count($attendanceLogs) >=2){
            $checkIN = $attendanceLogs[0];
            $checkOUT = $attendanceLogs[count($attendanceLogs)-1];
        }
        elseif(count($attendanceLogs) == 1)
        {
            $checkIN = $attendanceLogs[0];
            $checkOUT = 'N/A';
        }
        $attendanceLogs = array('in'=>$checkIN,'out'=>$checkOUT);

        $nowData = $getToday;
        $attendance = array();
        // DB::enableQueryLog(); // Enable query log
        foreach($nowData['periodAD'] as $periodData){
            $attendanceData = $this->attnLogs->where('user_id','LIKE',$user)->whereDate('at','=',"{$periodData->format('Y-m-d')}")->get();
            if(count($attendanceData) == 0){
                $attendance[$periodData->format('Y-m-d')] = array('in'=>'-','out'=>'-');
            }
            else
            {
                $count = count($attendanceData);
                if($count >= 2)
                {
                    $checkIN = Carbon::parse($attendanceData[0]['at'])->toTimeString();
                    $checkOUT = Carbon::parse($attendanceData[count($attendanceData) - 1]['at'])->toTimeString();;
                }
                else
                {
                    $checkIN = Carbon::parse($attendanceData[0]['at'])->toTimeString();
                    $checkOUT = 'N/A';
                }
                $attendance[$periodData->format('Y-m-d')] = array('in'=>$checkIN,'out'=>$checkOUT);
            }
        }
        $npCal = $this->npCal;

    return view('hajiri.logs.view',compact('type','attendance','nowData','npCal','holidays'));
    }


    private function getToday($y = null,$m = null,$d = null)
    {
        if($y == null || $m == null || $d == null){
            $dateRef = Carbon::now();
            $dateToday = $dateRef->toDateString();
            list($y,$m,$d) = explode('-',$dateToday);
        }

        $firstDayofMonth = $this->npCal->ad_2_bs($y,$m,$d);
        $forCheckFirstDay = $this->npCal->bs_2_ad($firstDayofMonth['year'],$firstDayofMonth['month'],"01");
        $lastDayofMonth = $this->npCal->bs[intval(substr($firstDayofMonth['year'],2,4))][intval($firstDayofMonth['month'])];
        $forCheckLastDay = $this->npCal->bs_2_ad($firstDayofMonth['year'],$firstDayofMonth['month'],$lastDayofMonth);
        
        return array(
            'yearBS'=>intval($firstDayofMonth['year']),
            'monthBS'=>intval($firstDayofMonth['month']),
            'nmonthBS'=>$firstDayofMonth['nmonth'],            
            'firstBS'=>1,
            'lastBS'=>intval($lastDayofMonth),
            'yearAD'=>intval($forCheckFirstDay['year']),
            'monthAD'=>intval($forCheckFirstDay['month']),
            'monthAD_A'=>intval($forCheckFirstDay['month']),
            'monthAD_B'=>intval($forCheckLastDay['month']),
            'nmonthAD_A'=>$forCheckFirstDay['nmonth'],
            'firstAD'=>intval($forCheckFirstDay['date']),
            'lastAD'=>intval($forCheckLastDay['date']),
            'firstDay'=>$forCheckFirstDay['num_day'],
            'lastDay'=>$forCheckLastDay['num_day'],
            'nmonthAD_B'=>$forCheckLastDay['nmonth'],
            'periodAD'=> CarbonPeriod::create("{$forCheckFirstDay['year']}-{$forCheckFirstDay['month']}-{$forCheckFirstDay['date']}", "{$forCheckLastDay['year']}-{$forCheckLastDay['month']}-{$forCheckLastDay['date']}")->toArray()
        );
    }
}

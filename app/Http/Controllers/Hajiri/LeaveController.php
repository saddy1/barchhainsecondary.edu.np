<?php

namespace App\Http\Controllers\Hajiri;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use DB;
use Carbon\CarbonPeriod;
use Carbon\Carbon;
use App\Models\Hajiri\AttendanceLogs;
use App\Models\User;
use App\Http\Controllers\Hajiri\NepaliCalendarController;
use App\Models\Hajiri\Leave;
use App\Models\Hajiri\TypeOfLeave;

class LeaveController extends Controller
{
    private $leave;
    private $npCal;
    private $users;
    private $leave_type;

    public function __construct(Leave $leave, User $users, TypeOfLeave $leave_type){
        $this->leave = $leave;
        $this->npCal = new NepaliCalendarController();
        $this->users = $users;
        $this->leave_type = $leave_type;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($year = '', $month = '')
    {
        // return array($year,$month);
        if($year == '' || $month == ''){
            $y_ = Date('Y');
            $m_ = Date('m');
            $d_ = Date('d');
            $getToday =  $this->getToday($y_,$m_,$d_);
            // return $getToday;
            $bothD = [];
            $bothD['start'] = (reset($getToday['periodAD']))->format('Y-m-d');
            $bothD['end'] = (end($getToday['periodAD']))->format('Y-m-d');

        }
        else
        {
            $tempDate =  $this->npCal->bs_2_ad($year,$month,"20");
            // return $tempDate;
            $getToday =  $this->getToday($tempDate['year'],$tempDate['month'],$tempDate['date']);
            
            $bothD = [];
            $bothD['start'] = (reset($getToday['periodAD']))->format('Y-m-d');
            $bothD['end'] = (end($getToday['periodAD']))->format('Y-m-d');
            
            // return $bothD;
        }

        // return $getToday;
        // $startA_ = "{$getToday['yearAD']}-{$getToday['monthAD_A']}-{$getToday['firstAD']}";
        // $endA_ = "{$getToday['yearAD']}-{$getToday['monthAD_B']}-{$getToday['lastAD']}";
        
        $startA = $bothD['start'];
        $endA = $bothD['end'];
        
        $start = $startA.' 00:00:00';
        $end = $endA.' 23:59:59';  
        
        // return array($startA_,$endA_);
        
        $npCal = $this->npCal;

        $leaves_ = $this->leave->with('user','type')->whereBetween('date',[$start,$end])->orderBy('user_id','DESC')->get();
        $leaves = array();
        foreach($leaves_ as $leave)
        {
            $leaves[] = $leave;
        }
        // return $leaves;
        $nowData = $getToday;
        $users = $this->users->with('designation','working_at')->where('status',1)->where('work_assigned_id',1)->orderBy('name')->get();
        $leave_type = $this->leave_type->get();
        return view('hajiri.leaves.index',compact('leaves','nowData','npCal','users','leave_type'));
    }
   /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    public function addHoliday(Request $request)
    {
        // return 'OK';
        $date = $request->date;
        $dateNP = $request->npDate;
        $holidayD = $this->leave->where('date','=',$date)->first();
        $leave_type = $this->leave_type->get();
        $users = $this->users->orderBy('name')->get();
        if($holidayD != null){
            return view('hajiri.leaves.edit',compact('date','holidayD','leave_type','users'));
        }
        else{
            return view('hajiri.leaves.create',compact('date','dateNP','leave_type','users'));
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // return $request->all();
        $validated = $request->validate([
            'from' => 'required|max:150',
            'to' => 'required|max:150',
            'user_id'=>'required',
            'leave_id'=>'required',            
        ]);
        $fromArr = explode("-",$request->from);
        $toArr = explode("-",$request->to);
        $fromAD =  $this->npCal->bs_2_ad($fromArr[0],$fromArr[1],$fromArr[2]);
        $toAD =  $this->npCal->bs_2_ad($toArr[0],$toArr[1],$toArr[2]);
        // return $fromAD;

        $date_range = $this->date_range("{$fromAD['year']}-{$fromAD['month']}-{$fromAD['date']}", "{$toAD['year']}-{$toAD['month']}-{$toAD['date']}");
        $flag = true;
        // return $date_range;
        foreach($date_range as $dr)
        {
            $holidayObj = new Leave();
            $holidayObj->date = $dr;
            $holidayObj->name = "Leave";
            $holidayObj->user_id = $request->user_id;   
            $holidayObj->leave_id = $request->leave_id;   
            if($holidayObj->save())
            {
               // $flag = true;
            }
            else
            {
                $flag = false;
            }
        }
        if($flag == true){
            return redirect()->back()->with('message', "Adding Holiday was successful!!");
        }
    }

    private function date_range($first, $last, $step = '+1 day', $output_format = 'Y-m-d' ) {

        $dates = array();
        $current = strtotime($first);
        $last = strtotime($last);
    
        while( $current <= $last ) {
    
            $dates[] = date($output_format, $current);
            $current = strtotime($step, $current);
        }
    
        return $dates;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|max:150',
            'alias' => 'required|max:150',
            'dsa'=>'required|max:10',
            'status'=>'required|max:10'
        ]);

        $holidayObj = $this->holiday->find($id);

        // return $request;

        $holidayArr = array();
        $holidayArr['label'] = $request->name;
        $holidayArr['alias'] = $request->alias;
        $holidayArr['dsa'] = (intval($request->dsa) == 1)?true:false;
        $holidayArr['status'] = (intval($request->status) == 1)?true:false;

        // return $holidayArr;

        if($holidayObj->where('id',$id)->update($holidayArr)){
            return redirect()->back()->with('message', "Update to Holiday was successful!!");
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
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

<?php

namespace App\Http\Controllers\Hajiri;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Hajiri\Holiday;
use App\Models\Hajiri\HajiriSetting;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Auth;

class HolidayController extends Controller
{
    private $holiday;
    private $npCal;

    public function __construct(Holiday $holiday){
        $this->holiday = $holiday;
        $this->npCal = new NepaliCalendarController();
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
        }
        else
        {
            $tempDate =  $this->npCal->bs_2_ad($year,$month,"20");
            // return $tempDate;
            $getToday =  $this->getToday($tempDate['year'],$tempDate['month'],$tempDate['date']);
            // return $getToday;
        }

        // return $getToday;
        $startA_ = "{$getToday['yearAD']}-{$getToday['monthAD_A']}-{$getToday['firstAD']}";
        $endA_ = "{$getToday['yearAD']}-{$getToday['monthAD_B']}-{$getToday['lastAD']}";

        
        $startA = Carbon::parse("{$startA_}");
        $endA = Carbon::parse("{$endA_}");

        $start = $startA->format('Y-m-d').' 00:00:00';
        $end = $endA->format('Y-m-d').' 23:59:59';  
        
        // return array($startA_,$endA_);
        
        $npCal = $this->npCal;

        $holidays_ = $this->holiday
            ->whereDate('date', '>=', $startA->toDateString())
            ->whereDate('date', '<=', $endA->toDateString())
            ->orderBy('date', 'DESC')
            ->get();
        $holidays = array();
        foreach($holidays_ as $holiday)
        {
            $holidays[$holiday->date->format('Y-m-d')] = $holiday;
        }

        $nowData = $getToday;
        $setting = HajiriSetting::current();
        return view('hajiri.holidays.index',compact('holidays','nowData','npCal','setting'));
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
        $date = $request->date;
        $dateNP = $request->npDate;
        $holidayD = $this->holiday->where('date','=',$date)->first();
        if($holidayD != null){
            return view('hajiri.holidays.edit',compact('date','holidayD'));
        }
        else{
            return view('hajiri.holidays.create',compact('date','dateNP'));
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
        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'name' => 'required|max:150',
            'alias' => 'nullable|max:150',
            'status' => 'nullable|boolean',
            'skip_weekends' => 'nullable|boolean',
        ]);

        $startDate = Carbon::parse($request->start_date)->startOfDay();
        $endDate = Carbon::parse($request->end_date ?: $request->start_date)->startOfDay();
        $setting = HajiriSetting::current();
        $created = 0;

        foreach (CarbonPeriod::create($startDate, $endDate) as $date) {
            if ($request->boolean('skip_weekends') && $setting->isWeekend($date->dayOfWeek)) {
                continue;
            }

            $this->holiday->updateOrCreate(
                ['date' => $date->toDateString()],
                [
                    'label' => $request->name,
                    'alias' => $request->alias,
                    'status' => $request->boolean('status', true),
                    'dsa' => false,
                    'color' => '#e11d48',
                ]
            );
            $created++;
        }

        return redirect()->back()->with('message', "{$created} holiday date(s) saved successfully.");
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
            'alias' => 'nullable|max:150',
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

    public function settings(Request $request)
    {
        $validated = $request->validate([
            'office_start_time' => 'required|date_format:H:i',
            'office_end_time' => 'required|date_format:H:i',
            'late_grace_minutes' => 'required|integer|min:0|max:240',
            'early_grace_minutes' => 'required|integer|min:0|max:240',
            'weekend_days' => 'nullable|array',
            'weekend_days.*' => 'integer|between:0,6',
        ]);

        $weekendDays = array_map('intval', $validated['weekend_days'] ?? []);
        $weekendDays = array_values(array_unique(array_filter($weekendDays, fn ($day) => $day >= 0 && $day <= 6)));
        sort($weekendDays);

        $setting = HajiriSetting::current();
        $setting->update([
            'office_start_time' => $validated['office_start_time'] . ':00',
            'office_end_time' => $validated['office_end_time'] . ':00',
            'late_grace_minutes' => $validated['late_grace_minutes'],
            'early_grace_minutes' => $validated['early_grace_minutes'],
            'weekend_days' => $weekendDays,
        ]);

        return redirect()->back()->with('message', 'Attendance rules updated successfully.');
    }

    public function destroy($id)
    {
        $this->holiday->findOrFail($id)->delete();
        return redirect()->back()->with('message', 'Holiday deleted successfully.');
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

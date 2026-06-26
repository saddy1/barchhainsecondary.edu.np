<?php

namespace App\Http\Controllers\Hajiri;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use File;
use App\Models\Hajiri\AttendanceLogs;
use Carbon\Carbon;

class DeviceController extends Controller
{
    private $attnLogs;
    
    public function __construct(AttendanceLogs $attnLogs)
    {
        $this->attnLogs = $attnLogs;
    }
    
    public function index(){
        return view('hajiri.devices.index');
    }
    
    public function sync_online(){
        $publicDir = public_path()."/upload_json/";
        if (!File::isDirectory($publicDir)) {
            return response()->json(['status' => 0]);
        }

        $files = File::files($publicDir);
        $hajiriDB = array();
        foreach ($files as $file)
        {
            $fileName = $file->getFileName();
            $hajiriLogs = json_decode(file_get_contents($publicDir.$fileName), true);
            if (! is_array($hajiriLogs) || ! isset($hajiriLogs['machineInfo']) || ! is_array($hajiriLogs['machineInfo'])) {
                unlink($publicDir.$fileName);
                continue;
            }

            foreach ($hajiriLogs['machineInfo'] as $hajiriLog)
            {
                if (empty($hajiriLog['indRegID']) || empty($hajiriLog['dateTimeRecord'])) {
                    continue;
                }

                $deviceID = $hajiriLog['indRegID'];
                $dateTimeRecord = $hajiriLog['dateTimeRecord'];
                $dateHajiri = (Carbon::parse($dateTimeRecord))->format('Y-m-d H:i:s');
                // if($this->attnLogs->where('user_id','LIKE',$deviceID)->where('at',$dateHajiri)->count() >= 1){
                //     continue;
                // }
                // else{
                    $hajiriDB[] = array('user_id'=>$deviceID,'at'=>$dateHajiri);
               // }
               // if($this->attnLogs->where(''))
            }
            if ($hajiriDB) {
                $this->attnLogs->insertOrIgnore($hajiriDB);
            }
            unlink($publicDir.$fileName);   
            return response()->json(['status'=>count($hajiriDB)]);
        }

        return response()->json(['status' => 0]);

    }
    
    public function upload_json(Request $request)
    {
        $request->validate([
            'file' => 'required',
        ]);
        if (!File::isDirectory(public_path('upload_json'))) {
            File::makeDirectory(public_path('upload_json'), 0755, true);
        }

        $fileNameOrg = $request->file->getClientOriginalName();
        $fileName = $fileNameOrg.'-'.time().'.'.$request->file->extension();  
        $request->file->move(public_path('upload_json'), $fileName);
        return array('status'=>1,'msg'=>'Data Uploaded to Server');
    }
}

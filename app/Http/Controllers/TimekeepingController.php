<?php

namespace App\Http\Controllers;

use App\Models\Timekeeping;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class TimekeepingController extends Controller
{
    public function renderQrCode(){
        Cache::add('code', Str::random(10), 30);
        $code = Cache::get('code');
        return view('welcome',[
            'value'=>$code
        ]);
    }

    public function handle($request_code,$id){
        $time = Carbon::now('Asia/Ho_Chi_Minh');
        $code = Cache::get('code');
        if ($request_code == $code){
            $user = User::find($id);
            if ($user){
                $timekeeping = Timekeeping::query()->where([['start_time','like', '%'.date_format($time,'y-m-d').'%'],'user_id'=>$id])->get();
                if (!$timekeeping){
                    $newtTimekeeping = new Timekeeping();
                    $newtTimekeeping->user_id = $id;
                    $newtTimekeeping->start_time = $time;
                    $newtTimekeeping->end_time = null;
                    $newtTimekeeping->user_id = $id;
                    if (date_format($time,'H') > 9){
                        $newtTimekeeping->late_start = 1;
                    }
                    else{
                        $newtTimekeeping->late_start = 0;
                    }
                    $newtTimekeeping->total_time = null;
                    $newtTimekeeping->late_attendance = null;
                }
                else{

                }

            }

            return "người dùng ko tồn tại";
        }

        return "đen lắm em";
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Timekeeping;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class TimekeepingController extends Controller
{
    public function renderQrCode()
    {
        Cache::add('code', Str::random(10), 60 * 5);
        $code = Cache::get('code');
        return view('welcome', [
            'value' => $code
        ]);
    }


    public function handle($request_code, $id)
    {
        $time = Carbon::now('Asia/Ho_Chi_Minh');
        $code = Cache::get('code');
        if ($request_code == $code) {
            $user = User::find($id);
            if ($user) {
                $timekeeping = Timekeeping::query()->where([['created_at', 'like', '%' . date_format($time, 'y-m-d') . '%'],
                    'user_id' => $id])->first();
                if (!$timekeeping) {
                    $newtTimekeeping = new Timekeeping();
                    $newtTimekeeping->user_id = $id;
                    $newtTimekeeping->start_time = $time;
                    $newtTimekeeping->user_id = $id;
                    $newtTimekeeping->late_attendance = false;
                    // check thời gian chấm công có bị muộn ko
                    if ($time->hour > 9 && $time->hour < 12 || $time->hour > 13) {
                        $newtTimekeeping->late_start = true;
                        $newtTimekeeping->save();

                        return response()->json([
                            'code' => 'A02',
                        ]);
                    } else {
                        $newtTimekeeping->late_start = false;
                        $newtTimekeeping->save();

                        return response()->json([
                            'code' => 'A01'
                        ]);
                    }

                } else {
                    if (floatval(date('H.i', strtotime($timekeeping->start_time))) >= 12) {
                        $timekeeping->update([
                            'end_time' => $time,
                            'total_time' => floatval(date('H.i', strtotime($time))) - floatval(date('H.i',
                                    strtotime($timekeeping->start_time)))
                        ]);
                        $timekeeping->save();
                    } else {
                        $timekeeping->update([
                            'end_time' => $time,
                            'total_time' => floatval(date('H.i', strtotime($time))) - floatval(date('H.i',
                                    strtotime($timekeeping->start_time))) - 1
                        ]);
                        $timekeeping->save();
                    }
                    if (floatval(date('H.i', strtotime($timekeeping->start_time))) < 12 && $timekeeping->total_time < 8) {
                        return response()->json([
                            'code' => 'A03',
                            'missing' => 8 - $timekeeping->total_time,
                            'total_time' => $timekeeping->total_time
                        ]);
                    } else {
                        return response()->json([
                            'code' => 'A04',
                            'total_time' => $timekeeping->total_time
                        ]);
                    }
                }
            }

            return response()->json([
                'code' => 'A05',
            ]);
        }

        return response()->json([
            'code' => 'A06',
        ]);
    }
}

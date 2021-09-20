<?php

namespace App\Http\Controllers;

use App\Jobs\SendBackEmail;
use App\Models\Approve;
use App\Models\Request;
use App\Models\User;
use Carbon\Carbon;


class ApproveController extends Controller
{
    public function accept($request_id,$hash,$choice){
        $approve = Approve::query()->where(['request_id'=>$request_id,'hash'=>$hash])->first();
        $receiver = User::find(Request::query()->where('hash',$request_id)->first()->sender_id);
        $time = Carbon::now('Asia/Ho_Chi_Minh');
        if ($approve->status == 1 ){
            if ($choice == 2 || $choice == 3){
                $approve->update(['status'=>$choice]);
                $approve->save();
                $userSendBack = User::find($approve->approve_id);
                $this->dispatch(new SendBackEmail(collect($userSendBack)->toArray() , collect($receiver)->toArray() , $choice));
                return view('mails.response',[
                    'choice'=>$choice,
                    'request_id'=> $request_id,
                    'receiver' => $receiver,
                    'time'=>$time
                ]);
            }
        }
        else{
            return view('mails.response',[
                'choice'=>null,
                'request_id'=> $request_id,
                'receiver' => $receiver,
                'time'=>$time
            ]);
        }
    }
}

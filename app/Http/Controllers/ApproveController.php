<?php

namespace App\Http\Controllers;

use App\Jobs\SendBackEmail;
use App\Models\Approve;
use App\Models\Request;
use App\Models\User;


class ApproveController extends Controller
{
    public function accept($request_id,$hash,$choice){

        $approve = Approve::query()->where(['request_id'=>$request_id,'hash'=>$hash])->first();
        if ($approve->status == 1 && $choice == 0 || $choice == 2){
            $approve->update(['status'=>$choice]);
            $approve->save();
            $userSendBack = User::find($approve->approve_id);
            $receiver = User::find(Request::query()->where('hash',$request_id)->first()->sender_id);
            $this->dispatch(new SendBackEmail(collect($userSendBack)->toArray() , collect($receiver)->toArray() , $choice));
        }
        else{
            return "ban da cap quen rồi";
        }
        return User::find(Request::query()->where('hash',$request_id)->first()->sender_id);
    }
}

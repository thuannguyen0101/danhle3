<?php

namespace App\Http\Controllers;

use App\Models\Approve;


class ApproveController extends Controller
{
    public function accept($request_id,$hash,$choice){

        $approve = Approve::query()->where(['request_id'=>$request_id,'hash'=>$hash])->first();
        if ($approve->status == 1 && $choice == 0 || $choice == 2){
            $approve->update(['status'=>$choice]);
            $approve->save();
            return $approve;
        }
        else{
            return "ban da cap quen rồi";
        }


    }
}

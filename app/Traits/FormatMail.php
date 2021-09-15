<?php

namespace App\Traits;

use App\Models\SendMail;
use App\Models\TeamDetail;
use App\Models\User;

class FormatMail
{
    public function invoke($requestData){
        $arrayMail = [];
        foreach ($requestData as $id) {
            $mail = SendMail::find($id);
            if ($mail->teamId == null) {
                if (!in_array($mail->mailName, $arrayMail)) {
                    array_push($arrayMail, $mail->mailName);
                }
            } else {
                $teamDetail = TeamDetail::query()->where('team_id', $mail->teamId)->get();
                foreach ($teamDetail as $item) {
                    $user = User::query()->where('id', $item->employee_id)->first();
                    if (!in_array($user->email, $arrayMail)) {
                        array_push($arrayMail, $user->email);
                    }
                }
            }
        }
        return $arrayMail;
    }
}

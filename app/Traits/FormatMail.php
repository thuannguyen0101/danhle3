<?php

namespace App\Traits;


use App\Models\SendMail;
use App\Models\Team;
use App\Models\TeamDetail;
use App\Models\User;
use Illuminate\Support\Facades\Log;


class FormatMail
{
    public function invoke($requestData, $hash)
    {
        $arrayMail = [];
        $arrayLeader = [];
        foreach ($requestData as $id) {
            $mail = SendMail::find($id);
            if ($mail->team_id == null) {
                if (!in_array($mail->mail_name, $arrayMail)) {
                    array_push($arrayMail, $mail->mail_name);
                }
            } else {
                $teamDetail = TeamDetail::query()->where('team_id', $mail->team_id)->get();
                $team = Team::find($mail->team_id);
                if ($team) {
                    array_push($arrayLeader,$team->leader_id);
                }
                foreach ($teamDetail as $item) {
                    $user = User::query()->where('id', $item->employee_id)->first();
                    if (!in_array($user->email, $arrayMail)) {
                        array_push($arrayMail, $user->email);
                    }
                }
            }
        }
        $data['mail'] = $arrayMail;
        $data['leader'] = $arrayLeader;

        return $data;
    }
}

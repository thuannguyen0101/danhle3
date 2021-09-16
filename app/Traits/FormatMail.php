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
            // mang mail co teamid
            if ($mail->teamId == null) {
                if (!in_array($mail->mailName, $arrayMail)) {
                    array_push($arrayMail, $mail->mailName);
                }
            } else {
                //team_id
                $teamDetail = TeamDetail::query()->where('team_id', $mail->teamId)->get();
                $team = Team::find($mail->teamId);
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
        Log::info('A',$arrayLeader);
        $data['mail'] = $arrayMail;
        $data['leader'] = $arrayLeader;
        return $data;
    }
}

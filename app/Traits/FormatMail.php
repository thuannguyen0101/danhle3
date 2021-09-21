<?php

namespace App\Traits;

use App\Models\SendMail;
use App\Models\Team;
use App\Models\TeamDetail;
use Illuminate\Support\Facades\Log;

class FormatMail
{
    /**  Processing sent data management returns a list of emails **/
    public function invoke($requestData, $hash)
    {
        $arrayMail = [];
        $temp = null;
        $arrayLeader = [];
        try {
            foreach ($requestData as $id) {
                $mail = SendMail::find($id);
                if ($mail->team_id == null && !in_array($mail->mail_name, $arrayMail)) {
                    array_push($arrayMail, $mail->mail_name);
                } else {
                    $users = TeamDetail::query()->where('team_id', $mail->team_id)->with('user')->get()->pluck('user')->pluck('email');
                    $team = Team::find($mail->team_id);
                    $temp ? $temp = [array_merge(collect($users)->toArray(), $arrayMail[0])] : $temp = [array_merge(collect($users)->toArray(), $arrayMail)];
                    $arrayMail = $temp;
                    !$team ?: array_push($arrayLeader, $team->leader_id);
                }
            }
        } catch (\Exception $e) {
            Log::error($e);
        }
        $data['mail'] = array_unique($arrayMail[0]);
        $data['leader'] = $arrayLeader;

        return $data;
    }
}

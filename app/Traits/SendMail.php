<?php

namespace App\Traits;

use App\Models\Approve;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class SendMail
{
    public function invoke($data, $backpackUser, $content, $hash)
    {
        foreach ($data['mail'] as $mail) {
            $user = User::query()->where('email', $mail)->first();
            $to_name = $user->name;
            $user_email = $user->email;
            Log::info($user->id);
            if (in_array($user->id,$data['leader'])) {
                $approve = new Approve();
                $approve->approve_id = $user->id;
                $approve->request_id = $hash;
                $approve->hash = Str::random(10);
                $approve->save();
                $url = 'http://localhost:8000/approve/'. $hash .'/' . $approve->hash;
                Mail::send('mails.demo_mail', ['user' => $user, 'content' => $content, 'backpackUser' => $backpackUser,'url'=>$url], function ($message) use ($to_name, $user_email, $backpackUser) {
                    $message->to($user_email, $to_name)
                        ->subject('ĐƠN XIN NGHỈ PHÉP CỦA :' . $backpackUser['name']);
                    $message->from(env('MAIL_USERNAME'), 'HRMS');
                });
            }

            else{
                Mail::send('mails.demo_mail', ['user' => $user, 'content' => $content, 'backpackUser' => $backpackUser ,'url'=>null], function ($message) use ($to_name, $user_email, $backpackUser) {
                    $message->to($user_email, $to_name)
                        ->subject('ĐƠN XIN NGHỈ PHÉP CỦA :' . $backpackUser['name']);
                    $message->from(env('MAIL_USERNAME'), 'HRMS');
                });
            }

        }

    }
}

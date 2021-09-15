<?php

namespace App\Traits;

use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendMail
{
    public function invoke($arrayMail,$backpackUser,$content){
        foreach ($arrayMail as $mail){
            $user = User::query()->where('email',$mail)->first();
            $to_name = $user->name;
            $user_email = $user->email;
            Mail::send('mails.demo_mail', ['user' => $user, 'content' => $content,'backpackUser'=>$backpackUser], function ($message) use ($to_name, $user_email,$backpackUser) {
                $message->to($user_email, $to_name)
                    ->subject('ĐƠN XIN NGHỈ PHÉP CỦA :' . $backpackUser['name']);
                $message->from(env('MAIL_USERNAME'), 'HRMS');
            });
        }
    }
}

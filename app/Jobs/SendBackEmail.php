<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendBackEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    private $userSendBack;
    private $receiver;
    private $choice;

    public function __construct(array $userSendBack, array $receiver, $choice)
    {
        $this->userSendBack = $userSendBack;
        $this->receiver = $receiver;
        $this->choice = $choice;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $content = null;
        if ($this->choice == 2) {
            $content = 'Yêu cầu xin nghỉ của bạn đã được phê duyệt';
        } else {
            $content = 'Yêu cầu xin nghỉ của bạn đã bị từ chối';
        }
        $toName = $this->receiver['name'];
        $userEmail = $this->receiver['email'];
        $sendBackUser = $this->userSendBack;
        Mail::send('mails.sendBackMail', ['user' => $this->userSendBack, 'content' => $content ,'url' => null], function ($message) use ($toName, $userEmail, $sendBackUser) {
            $message->to($userEmail, $toName)
                ->subject('PHẢN HỒI ĐƠN XIN NGHỈ TỪ :' . $sendBackUser['name']);
            $message->from(env('MAIL_USERNAME'), 'HRMS');
        });
    }
}

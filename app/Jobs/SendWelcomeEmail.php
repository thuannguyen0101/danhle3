<?php

namespace App\Jobs;

use App\Traits\SendMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Traits\FormatMail;

class SendWelcomeEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels ;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    private $content;
    public $userInfo;

//    public function __construct()
    public function __construct(array $content, array $userInfo)
    {
        $this->content = $content;
        $this->userInfo = $userInfo;
    }

    /**
     * Execute the job.
     *
     * @return string
     */
    public function handle()
    {
        $arrayMail = (new FormatMail())->invoke($this->content['sendMail']);
        (new SendMail())->invoke($arrayMail,$this->userInfo,$this->content);

        return 'send mail success';
    }

}




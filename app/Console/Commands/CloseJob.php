<?php

namespace App\Console\Commands;

use App\Models\Timekeeping;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CloseJob extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'closeJob:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $user = new User();
        $user->name = 'hjnhhjh'.random_int(12345,98765);
        $user->email = 'hjnhhjh'.random_int(12345,98765).'@gmail.com';
        $user->microsoft_id = random_int(1000000000,9000000000);
        $user->save();
        Log::info($user);
//        $all_user_late_attendance = Timekeeping::query()->where(['end_time'=>null,'late_attendance'=>0])->get();
//        $all_user_late_attendance->late_attendance = 1;
//        foreach ($all_user_late_attendance as $item){
//            $item->late_attendance = 1;
//            $item->save();
//        }
    }
}

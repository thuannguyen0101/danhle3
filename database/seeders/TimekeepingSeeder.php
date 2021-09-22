<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TimekeepingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('timekeeping')->insert([
            [
                'user_id' => 5,
                'start_time' => Carbon::now()->addDay(-3),
                'end_time' => Carbon::now()->addDay(-3)->addHour(8),
                'late_start' => 0,
                'total_time' => 7,
                'late_attendance' => 0,
            ],
            [
                'user_id' => 4,
                'start_time' => Carbon::now()->addDay(-3),
                'end_time' => Carbon::now()->addDay(-3)->addHour(8),
                'late_start' => 1,
                'total_time' => 7,
                'late_attendance' => 2,
            ],
            [
                'user_id' => 4,
                'start_time' => Carbon::now()->addDay(-2),
                'end_time' => Carbon::now()->addDay(-2)->addHour(8),
                'late_start' => 0,
                'total_time' => 7,
                'late_attendance' => 1,
            ],
            [
                'user_id' => 5,
                'start_time' => Carbon::now()->addDay(-2),
                'end_time' => Carbon::now()->addDay(-2)->addHour(8),
                'late_start' => 0,
                'total_time' => 8,
                'late_attendance' => 1,
            ],
            [
                'user_id' => 6,
                'start_time' => Carbon::now()->addDay(-2),
                'end_time' => Carbon::now()->addDay(-2)->addHour(8),
                'late_start' => 0,
                'total_time' => 7,
                'late_attendance' => 1,
            ],
            [
                'user_id' => 5,
                'start_time' => Carbon::now()->addDay(-1),
                'end_time' => Carbon::now()->addDay(-1)->addHour(8),
                'late_start' => 0,
                'total_time' => 8,
                'late_attendance' => 1,
            ],
            [
                'user_id' => 4,
                'start_time' => Carbon::now()->addDay(-1),
                'end_time' => Carbon::now()->addDay(-1)->addHour(8),
                'late_start' => 0,
                'total_time' => 7,
                'late_attendance' => 1,
            ],
            [
                'user_id' => 5,
                'start_time' => Carbon::now()->addDay(-1),
                'end_time' => Carbon::now()->addDay(-1)->addHour(8),
                'late_start' => 0,
                'total_time' => 8,
                'late_attendance' => 1,
            ],
            [
                'user_id' => 1,
                'start_time' => Carbon::now()->addHour(-8),
                'end_time' => Carbon::now(),
                'late_start' => 1,
                'total_time' => 8,
                'late_attendance' => 1,
            ],
            [
                'user_id' => 3,
                'start_time' => Carbon::now()->addHour(-7),
                'end_time' => Carbon::now(),
                'late_start' => 0,
                'total_time' => 8,
                'late_attendance' => 0,
            ],
            [
                'user_id' => 4,
                'start_time' => Carbon::now()->addHour(-6),
                'end_time' => Carbon::now()->addHour(+2),
                'late_start' => 0,
                'total_time' => 7,
                'late_attendance' => 1,
            ]
        ]);
    }
}

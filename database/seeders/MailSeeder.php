<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('mails')->insert([
            [
                'mail_name'=>'teammobile@newit.co.jp',
                'team_id'=>1,
            ],
            [
                'mail_name'=>'teamweb@newit.co.jp',
                'team_id'=>2,
            ],
            [
                'mail_name'=>'hjnhh18@gmail.com',
                'team_id'=>null,
            ],
            [
                'mail_name'=>'nguyenhjnh2002@gmail.com',
                'team_id'=>null,
            ],
            [
                'mail_name'=>'thuandz01012002gmail.com',
                'team_id'=>null,
            ],
            [
                'mail_name'=>'thuannnth2009019@fpt.edu.vn',
                'team_id'=>null,
            ],
            [
                'mail_name'=>'hjnhh19@gmail.com',
                'team_id'=>null,
            ],
            [
                'mail_name'=>'hjnhhjnh2345@gmail.com',
                'team_id'=>null,
            ],

        ]);
    }
}

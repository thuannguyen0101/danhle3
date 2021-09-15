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
                'mailName'=>'teammobile@newit.co.jp',
                'teamId'=>1,
            ],
            [
                'mailName'=>'teamweb@newit.co.jp',
                'teamId'=>2,
            ],
            [
                'mailName'=>'hjnhh18@gmail.com',
                'teamId'=>null,
            ],
            [
                'mailName'=>'nguyenhjnh2002@gmail.com',
                'teamId'=>null,
            ],
            [
                'mailName'=>'thuandz01012002gmail.com',
                'teamId'=>null,
            ],
            [
                'mailName'=>'thuannnth2009019@fpt.edu.vn',
                'teamId'=>null,
            ],
            [
                'mailName'=>'hjnhh19@gmail.com',
                'teamId'=>null,
            ],
            [
                'mailName'=>'hjnhhjnh2345@gmail.com',
                'teamId'=>null,
            ],

        ]);
    }
}

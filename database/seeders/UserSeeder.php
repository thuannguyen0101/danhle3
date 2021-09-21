<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            [
                'name'=>'hinh18',
                'email'=>'hjnhh18@gmail.com',
                'microsoft_id'=>'09876543234567890',
            ],
            [
                'name'=>'Nguyen Van Toan',
                'email'=>'Toannv@newit.co.jp',
                'microsoft_id'=>'09876543234567891',
            ],
            [
                'name'=>'Nguyen Ngoc Thuan DZ',
                'email'=>'thuandz01012002gmail.com',
                'microsoft_id'=>'09876543234567893',
            ],
            [
                'name'=>'Nguyen Ngoc Thuan 19',
                'email'=>'thuannnth2009019@fpt.edu.vn',
                'microsoft_id'=>'09876543234567894',
            ],
            [
                'name'=>'nguyen xuan hjnh 19',
                'email'=>'hjnhh19@gmail.com',
                'microsoft_id'=>'09876543234567895',
            ],
            [
                'name'=>'nguyen xuan hjnh 2345',
                'email'=>'hjnhhjnh2345@gmail.com',
                'microsoft_id'=>'09876543234567896',
            ]
        ]);
    }
}

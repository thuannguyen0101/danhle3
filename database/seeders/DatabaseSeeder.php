<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            UserSeeder::class,
            MailSeeder::class
        ]);
        DB::table('departments')->insert([
            [
                'manager_id'=>'1',
                'name'=>'Product',
                'description'=>'Product Development',
            ],
            [
                'manager_id'=>'4',
                'name'=>'Business',
                'description'=>'Commercial business',
            ],
        ]);

        DB::table('teams')->insert([
            [
                'department_id'=>'1',
                'leader_id'=>'2',
                'name'=>'Mobile',
                'description'=>'Mobile application',
                'status'=>1,
            ],
            [
                'department_id'=>'2',
                'leader_id'=>'4',
                'name'=>'Web',
                'description'=>'Web development',
                'status'=>1,
            ],
        ]);

        DB::table('profile_works')->insert([
            [
                'employee_id'=>1,
                'department_id'=>1,
                'phone'=>'0987987789',
                'address'=>'Ha Noi Viet Nam',
                'work_location'=>'21.031, 105.783',
                'position'=>'personnel',
            ],
            [
                'employee_id'=>2,
                'department_id'=>1,
                'phone'=>'0987987789',
                'address'=>'Ha Noi Viet Nam',
                'work_location'=>'21.031, 105.783',
                'position'=>'personnel',
            ],
            [
                'employee_id'=>3,
                'department_id'=>1,
                'phone'=>'0987987789',
                'address'=>'Ha Noi Viet Nam',
                'work_location'=>'21.031, 105.783',
                'position'=>'personnel',
            ],
            [
                'employee_id'=>4,
                'department_id'=>2,
                'phone'=>'0987987789',
                'address'=>'Ha Noi Viet Nam',
                'work_location'=>'21.031, 105.783',
                'position'=>'personnel',
            ],
            [
                'employee_id'=>5,
                'department_id'=>2,
                'phone'=>'0987987789',
                'address'=>'Ha Noi Viet Nam',
                'work_location'=>'21.031, 105.783',
                'position'=>'personnel',
            ],
            [
                'employee_id'=>6,
                'department_id'=>2,
                'phone'=>'0987987789',
                'address'=>'Ha Noi Viet Nam',
                'work_location'=>'21.031, 105.783',
                'position'=>'personnel',
            ],
        ]);
        DB::table('team_details')->insert([
            [
                'team_id'=>1,
                'employee_id'=>1,
            ],
            [
                'team_id'=>1,
                'employee_id'=>2,
            ],
            [
                'team_id'=>1,
                'employee_id'=>3,
            ],
            [
                'team_id'=>2,
                'employee_id'=>4,
            ],
            [
                'team_id'=>2,
                'employee_id'=>5,
            ],
            [
                'team_id'=>2,
                'employee_id'=>6,
            ],
        ]);
    }
}

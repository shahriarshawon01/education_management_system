<?php

namespace Database\Seeders;

use App\Models\School;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class SchoolSeeder extends Seeder
{
    public function run()
    {
        School::truncate();

        $schools = [
            [
                'title' => 'TMSS Public School And College',
                'email' => 'tmssict@gmail.com',
                'phone' => '01500000000',
                'steb_number' => '2023',
                'status' => 1,
                'reg_number' => '1001',
                'institute_code' => '1921',
                'address' => 'Joypur Para, Bogura',
                'emis_code' => '2002',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];

        School::insert($schools);
    }
}

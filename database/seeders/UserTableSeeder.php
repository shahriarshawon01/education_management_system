<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserTableSeeder extends Seeder
{
    public function run()
    {
        User::truncate();

        $users = [
            [
                'username' => 'tmssict',
                'email' => 'superadmin@gmail.com',
                'password' => Hash::make('123456'),
                'role_id' => 1,
                'type' => 1,
                'school_id' => 1,
                'created_by' => 1,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
        ];

        User::insert($users);
    }
}

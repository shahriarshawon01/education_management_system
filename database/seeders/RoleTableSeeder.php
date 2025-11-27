<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleTableSeeder extends Seeder
{
    public function run()
    {
        Role::truncate();

        $roles = [
            [
                'display_name' => 'SuperUser',
                'name' => 'superAdmin',
            ],
            [
                'display_name' => 'TeacherUser',
                'name' => 'teacher',
            ],
            [
                'display_name' => 'StaffUser',
                'name' => 'staff',
            ],
            [
                'display_name' => 'ParentUser',
                'name' => 'parent',
            ],
            [
                'display_name' => 'StudentUser',
                'name' => 'student',
            ],
            [
                'display_name' => 'WebsiteUser',
                'name' => 'website',
            ],
            [
                'display_name' => 'ChiefAccount',
                'name' => 'Chief Account Officer',
            ],

            [
                'display_name' => 'AccountPayment',
                'name' => 'Accounts (Payment)',
            ],
            [
                'display_name' => 'AccountInvoice',
                'name' => 'Accounts (Invoice)',
            ],
            [
                'display_name' => 'ItHead',
                'name' => 'IT-Head',
            ],
            [
                'display_name' => 'ItUser',
                'name' => 'IT-User',
            ],
            [
                'display_name' => 'IdPrint',
                'name' => 'ID-Print',
            ],
        ];

        foreach ($roles as $roleData) {

            $role = new Role();
            $role->display_name = $roleData['display_name'];
            $role->name = $roleData['name'];
            $role->save();
        }
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // 1️⃣ Move Teachers → Employees
        if (Schema::hasTable('teachers')) {
            $teachers = DB::table('teachers')->get();

            foreach ($teachers as $teacher) {
                $employeeId = DB::table('employees')->insertGetId([
                    'name' => $teacher->name,
                    'user_id' => $teacher->user_id,
                    'father_name' => $teacher->father_name,
                    'mother_name' => $teacher->mother_name,
                    'joining_date' => $teacher->joining_date,
                    'resign_date' => $teacher->resign_date,
                    'terminate_date' => $teacher->terminate_date,
                    'employee_id' => $teacher->employee_id,
                    'gender' => $teacher->gender,
                    'marital_status' => $teacher->marital_status,
                    'religion' => $teacher->religion,
                    'designation_id' => $teacher->designation_id,
                    'department_id' => $teacher->department_id,
                    'date_of_birth' => $teacher->date_of_birth,
                    'phone' => $teacher->phone,
                    'nid' => $teacher->nid,
                    'employee_type' => 1, // ✅ Teacher
                    'school_id' => $teacher->school_id,
                    'status' => $teacher->status,
                    'photo' => $teacher->photo,
                    'job_comments' => $teacher->job_comments,
                    'created_at' => $teacher->created_at,
                    'updated_at' => $teacher->updated_at,
                ]);

                // Move Teacher Qualifications → Employee Qualifications
                if (Schema::hasTable('qualifications')) {
                    $qualifications = DB::table('qualifications')
                        ->where('teacher_id', $teacher->id)
                        ->get();

                    foreach ($qualifications as $q) {
                        DB::table('employee_qualifications')->insert([
                            'school_id' => $q->school_id,
                            'employee_id' => $employeeId,
                            'degree_name' => $q->degree_name,
                            'board_name' => $q->board_name,
                            'passing_year' => $q->passing_year,
                            'dept_name' => $q->dept_name,
                            'status' => $q->status,
                            'created_at' => $q->created_at,
                            'updated_at' => $q->updated_at,
                        ]);
                    }
                }
            }
        }

        // 2️⃣ Move Staff → Employees (Support Staff included)
        if (Schema::hasTable('staff')) {
            $staffs = DB::table('staff')->get();

            foreach ($staffs as $staff) {
                // Skip duplicates if same staff already inserted
                $exists = DB::table('employees')
                    ->where('employee_id', $staff->employee_id)
                    ->where('school_id', $staff->school_id)
                    ->exists();

                if ($exists) {
                    continue; // already inserted
                }

                if($staff->staff_type == 2) {
                    $employeeType = 3; // Support Staff
                } else {
                    $employeeType = 2; // Staff
                }

                $employeeId = DB::table('employees')->insertGetId([
                    'name' => $staff->name,
                    'user_id' => $staff->user_id,
                    'father_name' => $staff->father_name,
                    'mother_name' => $staff->mother_name,
                    'joining_date' => $staff->joining_date,
                    'resign_date' => $staff->resign_date,
                    'terminate_date' => $staff->terminate_date,
                    'employee_id' => $staff->employee_id,
                    'gender' => $staff->gender,
                    'marital_status' => $staff->marital_status,
                    'religion' => $staff->religion,
                    'designation_id' => $staff->designation_id,
                    'department_id' => $staff->department_id,
                    'date_of_birth' => $staff->date_of_birth,
                    'phone' => $staff->phone,
                    'nid' => $staff->nid,
                    'employee_type' => $employeeType,  // ✅ 1=Staff, 2=Support Staff
                    'school_id' => $staff->school_id,
                    'status' => $staff->status,
                    'photo' => $staff->photo,
                    'job_comments' => $staff->job_comments,
                    'created_at' => $staff->created_at,
                    'updated_at' => $staff->updated_at,
                ]);

                // Copy qualifications
                $qualifications = DB::table('qualifications')
                    ->where('staff_id', $staff->id)
                    ->get();

                foreach ($qualifications as $q) {
                    DB::table('employee_qualifications')->insert([
                        'school_id' => $q->school_id,
                        'employee_id' => $employeeId,
                        'degree_name' => $q->degree_name,
                        'board_name' => $q->board_name,
                        'passing_year' => $q->passing_year,
                        'dept_name' => $q->dept_name,
                        'status' => $q->status,
                        'created_at' => $q->created_at,
                        'updated_at' => $q->updated_at,
                    ]);
                }
            }
        }

    }

    public function down(): void
    {
        DB::table('employee_qualifications')->truncate();
        DB::table('employees')->truncate();
    }
};

// php artisan make:migration migrate_teachers_and_staff_to_employees
// php artisan migrate


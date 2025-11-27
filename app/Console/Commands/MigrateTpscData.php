<?php

namespace App\Console\Commands;

use DateTime;
use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\MigrationData\MigrationModel;

class MigrateTpscData extends Command
{
    protected $signature = 'migrate:tpsc-data {table_name}';
    protected $description = 'Command description';

    public function handle()
    {
        $tblName = $this->argument('table_name');
        $dynamicModel = new MigrationModel($tblName);
        $progressbar = $this->output->createProgressBar($dynamicModel->count());
        $progressbar->start();

        // cmd - php artisan migrate:tpsc-data users
        // $this->userMigrate($dynamicModel, $tblName);

        // cmd - php artisan migrate:tpsc-data teacher
        // $this->teacherMigrate($dynamicModel);

        // cmd - php artisan migrate:tpsc-data teacher
        // $this->staffMigrate($dynamicModel);

        // session migrate
        // cmd - php artisan migrate:tpsc-data ov_setup
        // $this->studentSessionMigrate($dynamicModel);

        // student class migrate
        // cmd - php artisan migrate:tpsc-data manage_class
        // $this->studentClassMigrate($dynamicModel);

        // student Departments migrate
        // cmd - php artisan migrate:tpsc-data manage_department
        // $this->studentDepartmentMigrate($dynamicModel);

        // student subjects migrate
        // cmd - php artisan migrate:tpsc-data manage_subject
        // $this->studentSubjectsMigrate($dynamicModel);

        // student sections migrate
        // cmd - php artisan migrate:tpsc-data manage_section
        // $this->studentSectionsMigrate($dynamicModel);

        // student migrate
        // cmd - php artisan migrate:tpsc-data students
        // $this->userAndStudentMigrate($dynamicModel, $tblName);

        // / Attendance migration

        // cmd - php artisan migrate:tpsc-data attendance_history
        // $this->attendanceHistoryMigrate($dynamicModel, $tblName);

        // cmd - php artisan migrate:tpsc-data teacher_attendance
        // $this->teacherAttendanceMigrate($dynamicModel, $tblName);

        // cmd - php artisan migrate:tpsc-data staff_attendance
        // $this->staffAttendanceMigrate($dynamicModel, $tblName);

        // cmd - php artisan migrate:tpsc-data student_attendance
        // $this->studentAttendanceMigrate($dynamicModel, $tblName);

        // ./ Attendance migration

        // / sms migration

        // cmd - php artisan migrate:tpsc-data sms_history
        // $this->smsHistoryMigrate($dynamicModel, $tblName);

        // cmd - php artisan migrate:tpsc-data sms_settings_gp
        // $this->smsSettingGpMigrate($dynamicModel, $tblName);

        // ./ sms migration

        // invoice migrate | no Need
        // cmd - php artisan migrate:tpsc-data invoice
        // $this->invoiceMigrate($dynamicModel, $tblName);

        // components migrate
        // cmd - php artisan migrate:tpsc-data invoice_component
        // $this->invoiceComponentMigrate($dynamicModel, $tblName);

        // invoice_details migrate  | no Need
        // cmd - php artisan migrate:tpsc-data invoice_child
        // $this->invoiceDetailsMigrate($dynamicModel, $tblName);

        // academic_syllabus migrate
        // cmd - php artisan migrate:tpsc-data academic_syllebus
        // $this->academicSyllabusMigrate($dynamicModel, $tblName);

        // canteen_components migrate
        // cmd - php artisan migrate:tpsc-data canteen_component
        // $this->MealTimeMigrate($dynamicModel, $tblName);

        // book_authors migrate
        // cmd - php artisan migrate:tpsc-data templet_article
        // $this->bookAuthorMigrate($dynamicModel, $tblName);

        // book publisher migrate
        // cmd - php artisan migrate:tpsc-data templet_article
        // $this->bookPublisherMigrate($dynamicModel, $tblName);

        // book_editions migrate
        // cmd - php artisan migrate:tpsc-data templet_article
        // $this->bookEditionsMigrate($dynamicModel, $tblName);

        // book_languages migrate
        // cmd - php artisan migrate:tpsc-data templet_article
        // $this->bookLanguageMigrate($dynamicModel, $tblName);

        // book_accessions migrate
        // cmd - php artisan migrate:tpsc-data templet_article
        // $this->bookAccessionsMigrate($dynamicModel, $tblName);

        // stock_books migrate
        // cmd - php artisan migrate:tpsc-data stock_article
        // $this->bookStockMigrate($dynamicModel, $tblName);

        // store_categories migrate
        // cmd - php artisan migrate:tpsc-data manage_category
        // $this->storeCategoryMigrate($dynamicModel, $tblName);

        // store_products migrate
        // cmd - php artisan migrate:tpsc-data manage_products
        // $this->storeProductMigrate($dynamicModel, $tblName);

        // stock_ins migrate
        // cmd - php artisan migrate:tpsc-data manage_products_stock
        // $this->storeStockInMigrate($dynamicModel, $tblName);

        // stock_outs migrate
        // cmd - php artisan migrate:tpsc-data sell_products
        // $this->storeStockOutMigrate($dynamicModel, $tblName);

        // cmd - php artisan migrate:tpsc-data manage_dormitory
        // $this->manageDormitoryMigrate($dynamicModel, $tblName);

        // cmd - php artisan migrate:tpsc-data assign_dormitory
        // $this->assignDormitoryMigrate($dynamicModel, $tblName);

        // cmd - php artisan migrate:tpsc-data notice_board
        $this->noticeBoardMigrate($dynamicModel, $tblName);

        // live_class migrate
        // cmd - php artisan migrate:tpsc-data live_class
        // $this->liveClassMigrate($dynamicModel, $tblName);

        // website data migration
        // website_slider migrate
        // php artisan migrate:tpsc-data website_slider
        // $this->websiteSliderMigrate($dynamicModel, $tblName);

        // applicant_student migrate
        // php artisan migrate:tpsc-data applicant_student
        // $this->applicantStudentMigrate($dynamicModel, $tblName);

        // website_contact migrate
        // php artisan migrate:tpsc-data website_contact
        //  $this->websiteContactMigrate($dynamicModel, $tblName);

        // website_course_category migrate
        // php artisan migrate:tpsc-data website_course_category
        // $this->courseCategoriesMigrate($dynamicModel, $tblName);

        // website_event migrate
        // php artisan migrate:tpsc-data website_event
        // $this->websiteEventMigrate($dynamicModel, $tblName);

        // website_setup migrate
        // php artisan migrate:tpsc-data website_setup
        // $this->websiteSetupsMigrate($dynamicModel, $tblName);

        $progressbar->advance();
        $progressbar->finish();
        dd('data migration done');
    }

    public function userMigrate($dynamicModel, $tblName)
    {
        DB::connection('new_edu')->table('users')->truncate();

        $oldUsers = $dynamicModel->get();
        $newUsers = DB::connection('new_edu')->table($tblName)->get();

        foreach ($oldUsers as $oldUser) {
            $newUser = $newUsers->where('email', $oldUser->email)->first();
            $userType = DB::connection('old_edu')->table('teacher')->where('email', $oldUser->email)->first();

            $adminType = 1;
            $userRole = 1;

            if ($userType) {
                $adminType = $userType->status === 'Teacher' ? 2 : 3;
                $userRole = $userType->status === 'Teacher' ? 2 : 3;
            }

            $mappedData = [
                "username" => strtolower($oldUser->name),
                "email" => $oldUser->email,
                "password" => Hash::make('123456'),
                "type" => $adminType,
                "role_id" => $userRole,
                "school_id" => 1,
                "created_by" => 1,
                "status" => $oldUser->status === "Active" ? 1 : 0,
                "updated_at" => now(),
                "created_at" => now(),
            ];

            if ($newUser) {
                DB::connection('new_edu')->table($tblName)->where('id', $newUser->id)->update($mappedData);
                $userId = $newUser->id;
            } else {
                $userId = DB::connection('new_edu')->table($tblName)->insertGetId($mappedData);
            }
        }
    }

    // migrate to this function => teacher,department,designation,address,qualification
    public function teacherMigrate($dynamicModel)
    {
        DB::connection('new_edu')->table('teachers')->truncate();
        DB::connection('new_edu')->table('designations')->truncate();
        DB::connection('new_edu')->table('work_departments')->truncate();
        DB::connection('new_edu')->table('addresses')->truncate();
        DB::connection('new_edu')->table('qualifications')->truncate();

        $oldTeachers = DB::connection('old_edu')->table('teacher')
            ->join('teacher_address_child', 'teacher.teacher_id', '=', 'teacher_address_child.parent')
            ->join('teacher_qualification_child', 'teacher.teacher_id', '=', 'teacher_qualification_child.parent')
            ->where('status', 'Teacher')
            ->get();

        $designations = $oldTeachers->pluck('designation')->unique();
        $workDepartments = $oldTeachers->pluck('work_department')->unique();

        $designationIds = [];
        foreach ($designations as $designation) {
            DB::connection('new_edu')->table('designations')->updateOrInsert(
                ['name' => $designation],
                [
                    'school_id' => 1,
                    'status' => 1,
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );
            $designationIds[$designation] = DB::connection('new_edu')->table('designations')
                ->where('name', $designation)
                ->value('id');
        }
        $departmentIds = [];
        foreach ($workDepartments as $department) {
            DB::connection('new_edu')->table('work_departments')->updateOrInsert(
                ['name' => $department],
                [
                    'school_id' => 1,
                    'status' => 1,
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );

            $departmentIds[$department] = DB::connection('new_edu')->table('work_departments')
                ->where('name', $department)
                ->value('id');
        }
        foreach ($oldTeachers as $oldTeacher) {
            $newUser = DB::connection('new_edu')->table('users')->where('email', $oldTeacher->email)->first();

            // Prepare to handle the profile image
            $imagePath = null;

            if ($oldTeacher && !empty($oldTeacher->teacher_id)) {
                $oldImageFileName = $oldTeacher->teacher_id . '.jpg';

                if ($oldImageFileName) {
                    $imagePath = 'uploads/' . $oldImageFileName;
                } else {
                    $imagePath = 'default-employee.jpg';
                }
            }

            $teacherId = DB::connection('new_edu')->table('teachers')->insertGetId([
                "name" => $oldTeacher->teacher_name,
                "user_id" => $newUser->id ?? null,
                "teacher_bio_id" => $oldTeacher->teacher_id,
                "father_name" => $oldTeacher->fathers_name,
                "mother_name" => $oldTeacher->mothers_name,
                "joining_date" => $oldTeacher->created_at ?? null,
                "employee_id" => $oldTeacher->employment_id ?? null,
                "gender" => $oldTeacher->gender == 'Male' ? 1 : 2,
                "marital_status" => $oldTeacher->marital_status == 'Married' ? 1 : 2,
                "religion" => match ($oldTeacher->religion) {
                    'Islam' => 1,
                    'Hindu' => 2,
                    'Buddhist' => 3,
                    'Khristan', 'Christian' => 4,
                    default => null,
                },
                "designation_id" => $designationIds[$oldTeacher->designation],
                "department_id" => $departmentIds[$oldTeacher->work_department],
                "job_category" => $oldTeacher->type == 'Tech' ? 1 : 2,
                "school_id" => 1,
                "status" => $oldTeacher->activity_status === "Active" ? 1 : 2,
                'nid' => $oldTeacher->nid ?? null,
                'phone' => $oldTeacher->mobile_no ?? null,
                'date_of_birth' => $oldTeacher->birth_date ?? null,
                "photo" => $imagePath,
                "job_comments" => null,
                "created_at" => now(),
                "updated_at" => now()
            ]);

            // Migrate teacher address
            $divisionId = $this->getOrInsertId('divisions', $oldTeacher->division);
            $districtId = $this->getOrInsertId('districts', $oldTeacher->home_district, 'division_id', $divisionId);
            // $upazilaId = $this->getOrInsertId('upazilas', $oldTeacher->upazila_name, 'district_id', $districtId);
            // $unionId = $this->getOrInsertId('unions', $oldTeacher->union_name, 'upazila_id', $upazilaId);

            DB::connection('new_edu')->table('addresses')->insert([
                "school_id" => 1,
                "student_id" => NULL,
                "teacher_id" => $teacherId,
                "staff_id" => NULL,
                "parent_id" => NULL,
                "type" => 1,
                "village" => $oldTeacher->village_name,
                "post_office" => $oldTeacher->post_office,
                "district" => $districtId,
                "division" => $divisionId,
                "upazila" => NULL,
                "union" => NULL,
                "created_at" => now(),
                "updated_at" => now()
            ]);
            // Migrate teacher qualification
            DB::connection('new_edu')->table('qualifications')->insert([
                "school_id" => 1,
                "teacher_id" => $teacherId,
                "staff_id" => NULL,
                "degree_name" => $oldTeacher->degree_name ?? NULL,
                "board_name" => $oldTeacher->board_name ?? NULL,
                "passing_year" => $oldTeacher->passing_year ?? NULL,
                "dept_name" => $oldTeacher->department_name ?? NULL,
                "status" => 1,
                "created_at" => now(),
                "updated_at" => now()
            ]);
        }
    }

    // staff,department,designation
    public function staffMigrate($dynamicModel)
    {
        DB::connection('new_edu')->table('staff')->truncate();

        $oldStaffs = DB::connection('old_edu')->table('teacher')
            ->join('teacher_address_child', 'teacher.teacher_id', '=', 'teacher_address_child.parent')
            ->join('teacher_qualification_child', 'teacher.teacher_id', '=', 'teacher_qualification_child.parent')
            ->where('status', 'Staff')
            ->get();

        $designations = $oldStaffs->pluck('designation')->unique();
        $workDepartments = $oldStaffs->pluck('work_department')->unique();

        $designationIds = [];
        foreach ($designations as $designation) {
            if (!empty($designation)) {
                $designationId = DB::connection('new_edu')->table('designations')->updateOrInsert(
                    ['name' => $designation],
                    [
                        'school_id' => 1,
                        'status' => 1,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]
                );
                $designationIds[$designation] = DB::connection('new_edu')->table('designations')
                    ->where('name', $designation)
                    ->value('id');
            }
        }
        $departmentIds = [];
        foreach ($workDepartments as $department) {
            if (!empty($department)) {
                $departmentId = DB::connection('new_edu')->table('work_departments')->updateOrInsert(
                    ['name' => $department],
                    [
                        'school_id' => 1,
                        'status' => 1,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]
                );
                $departmentIds[$department] = DB::connection('new_edu')->table('work_departments')
                    ->where('name', $department)
                    ->value('id');
            }
        }
        foreach ($oldStaffs as $oldStaff) {
            $newUser = DB::connection('new_edu')->table('users')->where('email', $oldStaff->email)->first();

            // Prepare to handle the profile image
            $imagePath = null;

            if ($oldStaff && !empty($oldStaff->teacher_id)) {
                $oldImageFileName = $oldStaff->teacher_id . '.jpg';

                if ($oldImageFileName) {
                    $imagePath = 'uploads/' . $oldImageFileName;
                } else {
                    $imagePath = 'default-employee.jpg';
                }
            }

            $staffId = DB::connection('new_edu')->table('staff')->insertGetId([
                "name" => $oldStaff->teacher_name,
                "user_id" => $newUser->id ?? null,
                "staff_bio_id" => $oldStaff->teacher_id,
                "father_name" => $oldStaff->fathers_name,
                "mother_name" => $oldStaff->mothers_name,
                'joining_date' => $oldStaff->created_at ?? null,
                "employee_id" => $oldStaff->employment_id ?? null,
                "gender" => $oldStaff->gender == 'Male' ? 1 : 2,
                "marital_status" => $oldStaff->marital_status == 'Married' ? 1 : 2,
                "religion" => match ($oldStaff->religion) {
                    'Islam' => 1,
                    'Hindu' => 2,
                    'Buddhist' => 3,
                    'Khristan', 'Christian' => 4,
                    default => null,
                },
                "designation_id" => $designationIds[$oldStaff->designation] ?? null,
                "department_id" => $departmentIds[$oldStaff->work_department] ?? null,
                "school_id" => 1,
                "status" => $oldStaff->activity_status === "Active" ? 1 : 2,
                'nid' => $oldStaff->nid ?? null,
                'phone' => $oldStaff->mobile_no ?? null,
                'date_of_birth' => $oldStaff->birth_date ?? null,
                "photo" => $imagePath,
                "job_comments" => null,
                "updated_at" => now(),
                "created_at" => now()
            ]);

            $divisionId = $this->getOrInsertId('divisions', $oldStaff->division);
            $districtId = $this->getOrInsertId('districts', $oldStaff->home_district, 'division_id', $divisionId);
            // $upazilaId = $this->getOrInsertId('upazilas', $oldTeacher->upazila_name, 'district_id', $districtId);
            // $unionId = $this->getOrInsertId('unions', $oldTeacher->union_name, 'upazila_id', $upazilaId);

            DB::connection('new_edu')->table('addresses')->insert([
                "school_id" => 1,
                "student_id" => NULL,
                "teacher_id" => NULL,
                "staff_id" => $staffId,
                "parent_id" => NULL,
                "type" => 1,
                "village" => $oldStaff->village_name,
                "post_office" => $oldStaff->post_office,
                "district" => $districtId ?? NULL,
                "division" => $divisionId ?? NULL,
                "upazila" => NULL,
                "union" => NULL,
                "created_at" => now(),
                "updated_at" => now()
            ]);
            DB::connection('new_edu')->table('qualifications')->insert([
                "school_id" => 1,
                "teacher_id" => NULL,
                "staff_id" => $staffId,
                "degree_name" => $oldStaff->degree_name ?? NULL,
                "board_name" => $oldStaff->board_name ?? NULL,
                "passing_year" => $oldStaff->passing_year ?? NULL,
                "dept_name" => $oldStaff->department_name ?? NULL,
                "status" => 1,
                "created_at" => now(),
                "updated_at" => now()
            ]);
        }
    }

    // cmd - php artisan migrate:tpsc-data ov_setup
    public function studentSessionMigrate($dynamicModel)
    {
        DB::connection('new_edu')->table('session_years')->truncate();

        $oldStudentSessions = DB::connection('old_edu')->table('ov_setup')
            ->where('type', 'Session')
            ->get();

        foreach ($oldStudentSessions as $oldStudentSession) {

            $mappedData = [
                'id' => $oldStudentSession->id,
                "title" => $oldStudentSession->type_name,
                "school_id" => 1,
                "status" => 1,
                "updated_at" => now(),
                "created_at" => now()
            ];
            DB::connection('new_edu')->table('session_years')->insert($mappedData);
        }
    }

    // cmd - php artisan migrate:tpsc-data manage_class
    public function studentClassMigrate($dynamicModel)
    {
        DB::connection('new_edu')->table('student_classes')->truncate();

        // Get old student class
        $oldStudentClasses = DB::connection('old_edu')->table('manage_class')
            ->get();

        // Migrate old student class to the new student class table
        foreach ($oldStudentClasses as $oldStudentClass) {

            $mappedData = [
                'id' => $oldStudentClass->id,
                "name" => $oldStudentClass->class_name,
                "numeric_name" => $oldStudentClass->numeric_name ?? null,
                "teacher_id" => $oldStudentClass->class_teacher ? 1 : NULL,
                "school_id" => 1,
                "status" => 1,
                "updated_at" => now(),
                "created_at" => now()
            ];
            DB::connection('new_edu')->table('student_classes')->insert($mappedData);
        }
    }

    // cmd - php artisan migrate:tpsc-data manage_department
    public function studentDepartmentMigrate($dynamicModel)
    {
        DB::connection('new_edu')->table('departments')->truncate();

        // Get old student manage_department
        $oldStudentDepartments = DB::connection('old_edu')->table('manage_department')
            ->get();

        // Migrate old student manage_department to the new student departments table
        foreach ($oldStudentDepartments as $oldStudentDepartment) {
            // ddA($oldStudentDepartment);
            $classId = DB::connection('new_edu')->table('student_classes')
                ->where('id', $oldStudentDepartment->class_id)
                ->value('id');

            // dd($classId);
            $mappedData = [
                "id" => $oldStudentDepartment->id,
                "school_id" => 1,
                "department_name" => $oldStudentDepartment->department_name,
                "department_code" => $oldStudentDepartment->department_code ?? null,
                "class_id" => $classId,
                "status" => 1,
                "created_by" => 1,
                "updated_at" => now(),
                "created_at" => now()
            ];
            DB::connection('new_edu')->table('departments')->insert($mappedData);
        }
    }

    // cmd - php artisan migrate:tpsc-data manage_subject
    public function studentSubjectsMigrate($dynamicModel)
    {
        DB::connection('new_edu')->table('subjects')->truncate();

        // Get old student manage_subject
        $oldStudentSubjects = DB::connection('old_edu')->table('manage_subject')
            ->get();


        // Migrate old student manage_subject to the new student subjects table
        foreach ($oldStudentSubjects as $oldStudentSubject) {

            $classId = DB::connection('new_edu')->table('student_classes')
                ->where('id', $oldStudentSubject->class_id)
                ->value('id');

            $sessionYearId = DB::connection('new_edu')->table('session_years')
                ->where('title', $oldStudentSubject->session)
                ->value('id');

            $departmentId = DB::connection('new_edu')->table('departments')
                ->where('department_name', $oldStudentSubject->department)
                ->where('class_id', $oldStudentSubject->class_id)
                ->value('id');

            $teacherId = DB::connection('new_edu')->table('teachers')
                ->where('name', $oldStudentSubject->teacher)
                ->value('id');

            $mappedData = [
                "name" => $oldStudentSubject->subject_name,
                "school_id" => 1,
                "class_id" => $classId,
                "subject_code" => $oldStudentSubject->subject_code ?? null,
                "subject_type" => $oldStudentSubject->type === 'Tech' ? 1 : 2,
                // "subject_mark" => $oldStudentSubject->subject_mark,
                // "subject_credit" => $oldStudentSubject->subject_mark ?? null,
                "session_year_id" => $sessionYearId,
                "department_id" => $departmentId,
                "teacher_id" => $teacherId,
                "status" => 1,
                "updated_at" => now(),
                "created_at" => now()
            ];
            DB::connection('new_edu')->table('subjects')->insert($mappedData);
        }
    }

    // cmd - php artisan migrate:tpsc-data manage_section
    public function studentSectionsMigrate($dynamicModel)
    {
        DB::connection('new_edu')->table('sections')->truncate();

        // Get old student manage_section
        $oldStudentSections = DB::connection('old_edu')->table('manage_section')
            ->get();

        // Migrate old student manage_section to the new student sections table
        foreach ($oldStudentSections as $oldStudentSection) {

            $classId = DB::connection('new_edu')->table('student_classes')
                ->where('id', $oldStudentSection->class_id)
                ->value('id');

            $teacherId = DB::connection('new_edu')->table('teachers')
                ->where('name', $oldStudentSection->teacher)
                ->value('id');

            $mappedData = [
                "name" => $oldStudentSection->section_name,
                // "nick_name" => $oldStudentSection->nick_name ?? null,
                "school_id" => 1,
                "class_id" => $classId,
                "teacher_id" => $teacherId,
                "status" => 1,
                "updated_at" => now(),
                "created_at" => now()
            ];
            DB::connection('new_edu')->table('sections')->insert($mappedData);
        }
    }

    // cmd -> php artisan migrate:tpsc-data students
    public function userAndStudentMigrate($dynamicModel, $tblName)
    {
        DB::connection('new_edu')->table('students')->truncate();
        DB::connection('new_edu')->table('student_qualifications')->truncate();

        $oldUsers = DB::connection('old_edu')->table('students')
            ->leftJoin('students_child', 'students.roll', '=', 'students_child.roll')
            ->leftJoin('student_educational_qualification', 'students.roll', '=', 'student_educational_qualification.student_roll')
            ->distinct()
            ->select(
                'students.*',
                'students_child.roll as child_roll',
                'students_child.post_office',
                'students_child.home_district',
                'students_child.division',
                'students_child.village_name',
                'student_educational_qualification.exam_name',
                'student_educational_qualification.borad',
                'student_educational_qualification.group',
                'student_educational_qualification.passing_year',
                'student_educational_qualification.gpa'
            )
            ->get();


        $newUsers = DB::connection('new_edu')->table($tblName)->get();

        foreach ($oldUsers as $oldUser) {
            $newUser = $newUsers->where('email', $oldUser->email)->first();

            $imagePath = null;

            if ($oldUser && !empty($oldUser->roll)) {
                $oldImageFileName = $oldUser->roll . '.jpg';

                if ($oldImageFileName) {
                    $imagePath = 'uploads/' . $oldImageFileName;
                } else {
                    $imagePath = 'default-employee.jpg';
                }
            }

            // Map user data
            $mappedData = [
                "username" => $oldUser->student_name,
                "email" => $oldUser->email,
                "password" => Hash::make('123456'),
                "type" => 5,
                "role_id" => 5,
                "school_id" => 1,
                "created_by" => 1,
                "status" => $oldUser->status === "Active" ? 1 : 0,
                "created_at" => now(),
                "updated_at" => now()
            ];

            // Update or insert the user
            if ($newUser) {
                DB::connection('new_edu')->table('users')
                    ->where('id', $newUser->id)
                    ->update($mappedData);
                $userId = $newUser->id;
            } else {
                $userId = DB::connection('new_edu')
                    ->table('users')
                    ->insertGetId($mappedData);
            }

            $sectionId = DB::connection('new_edu')->table('sections')
                ->where('name', $oldUser->section)
                ->value('id');

            $departmentId = DB::connection('new_edu')->table('departments')
                ->where('department_name', $oldUser->department)->where('class_id', $oldUser->class_id)
                ->value('id');

            $sessionId = DB::connection('new_edu')->table('session_years')
                ->where('title', $oldUser->session)
                ->value('id');

            $teacherId = DB::connection('new_edu')->table('teachers')
                ->where('name', $oldUser->reponsible_teacher)
                ->value('id');


            // Additional logic for student data
            $studentId = DB::connection('new_edu')->table('students')->insertGetId([
                'student_name_bn' => NULL,
                'student_name_en' => $oldUser->student_name,
                'merit_roll' => $oldUser->merit_roll,
                'student_roll' => $oldUser->roll,
                'reg_number' => is_numeric($oldUser->reg_number) ? $oldUser->reg_number : NULL,

                'father_name_bn' => NULL,
                'father_name_en' => $oldUser->parent_name ?? NULL,
                'father_phone' => $oldUser->mobile ?? NULL,
                'father_profession' => NULL,

                'mother_name_bn' => NULL,
                'mother_name_en' => $oldUser->mothers_name,
                'mother_phone' => $oldUser->phone ?? NULL,
                'mother_profession' => NULL,

                'school_id' => 1,
                'user_id' => $userId,
                'class_id' => $oldUser->class_id,
                'responsible_teacher_id' => $teacherId,
                'section_id' => $sectionId,
                'department_id' => $departmentId,
                'session_year_id' => $sessionId,
                'parent_id' => NULL,
                'nationality' => NULL,
                "religion" => match ($oldUser->religion) {
                    'Islam' => 1,
                    'Hindu' => 2,
                    'Buddhist' => 3,
                    'Khristan', 'Christian' => 4,
                    default => null,
                },
                'admission_date' => $oldUser->created_at,
                'blood_group' => $oldUser->blood_group ?? NULL,
                'gender' => $oldUser->gender == 'Male' ? 1 : 2,
                'height' => NULL,
                'weight' => NULL,
                'relation' => strtolower($oldUser->relation),
                'yearly_income' => NULL,
                'previous_institute' => NULL,
                'status' => $oldUser->status === 'Active' ? 1 : 0,
                'date_of_birth' => isset($oldUser->birth_date) ?
                    (DateTime::createFromFormat('Y-m-d', $oldUser->birth_date) ? $oldUser->birth_date : null) : null,
                'photo' => $imagePath,

                'created_at' => now(),
                'updated_at' => now()
            ]);

            $divisionId = $this->getOrInsertId('divisions', $oldUser->division);
            $districtId = $this->getOrInsertId('districts', $oldUser->home_district, 'division_id', $divisionId);
            // $upazilaId = $this->getOrInsertId('upazilas', $oldTeacher->upazila_name, 'district_id', $districtId);
            // $unionId = $this->getOrInsertId('unions', $oldTeacher->union_name, 'upazila_id', $upazilaId);

            DB::connection('new_edu')->table('addresses')->insert([
                "school_id" => 1,
                "student_id" => $studentId,
                "teacher_id" => NULL,
                "staff_id" => NULL,
                "parent_id" => NULL,
                "type" => 1,
                "village" => $oldUser->village_name,
                "post_office" => $oldUser->post_office,
                "district" => $districtId ?? NULL,
                "division" => $divisionId ?? NULL,
                "upazila" => NULL,
                "union" => NULL,
                "created_at" => now(),
                "updated_at" => now()
            ]);

            DB::connection('new_edu')->table('student_qualifications')->insert([
                "school_id" => 1,
                "student_id" => $studentId,
                "roll_number" => NULL,
                "reg_number" => NULL,
                "exam_name" => !empty($oldUser->exam_name) ? $oldUser->exam_name : NULL,
                "board_name" => !empty($oldUser->borad) ? $oldUser->borad : NULL,
                "group" => !empty($oldUser->group) ? $oldUser->group : NULL,
                "passing_year" => !empty($oldUser->passing_year) ? $oldUser->passing_year : NULL,
                "gpa" => !empty($oldUser->gpa) ? $oldUser->gpa : NULL,

                "status" => 1,
                "created_at" => now(),
                "updated_at" => now()
            ]);
        }
    }

    // In this function just return the division, district
    private function getOrInsertId($tableName, $name, $parentColumn = null, $parentId = null, $bnName = null)
    {
        $query = DB::connection('new_edu')->table($tableName)->where('name', $name);

        if ($parentColumn && $parentId) {
            $query->where($parentColumn, $parentId);
        }
        $id = $query->value('id');

        if (!$id) {
            $data = [
                'name' => $name ?? NULL,
                'created_at' => now(),
                'updated_at' => now()
            ];
            // Conditionally add the parent column if it exists
            if ($parentColumn && $parentId) {
                $data[$parentColumn] = $parentId;
            }
            // Add bn_name if applicable and provided
            if ($tableName === 'districts' && $bnName) {
                $data['bn_name'] = $bnName;
            }
            $id = DB::connection('new_edu')->table($tableName)->insertGetId($data);
        }
        return $id;
    }

    // / Attendance Migrate Function
    // cmd -> php artisan migrate:tpsc-data attendance_history
    public function attendanceHistoryMigrate($dynamicModel, $tblName)
    {
        DB::connection('new_edu')->table('attendance_history')->truncate();
        $oldAttendanceHistories = DB::connection('old_edu')->table('attendance_history')->get();

        foreach ($oldAttendanceHistories as $oldAttendanceHistory) {
            $existingRecord = DB::connection('new_edu')->table('attendance_history')->where('id', $oldAttendanceHistory->id)->first();

            if ($existingRecord) {
                continue;
            }

            $userType = 0;
            switch ($oldAttendanceHistory->user_type) {
                case 'student':
                    $userType = 3;
                    break;
                case 'Teacher':
                    $userType = 1;
                    break;
                case 'Staff':
                    $userType = 2;
                    break;
            }

            $mappedData = [
                "user_id" => $oldAttendanceHistory->user_id,
                "time_history" => $oldAttendanceHistory->time_history,
                "user_type" => $userType,
                "tpsc_user_id" => $oldAttendanceHistory->tpsc_user_id,
                "updated_at" => now(),
                "created_at" => now(),
                "deleted_at" => null,
            ];
            DB::connection('new_edu')->table('attendance_history')->insert($mappedData);
        }
    }

    // cmd - php artisan migrate:tpsc-data teacher_attendance
    public function teacherAttendanceMigrate($dynamicModel, $tblName)
    {
        DB::connection('new_edu')->table('teacher_attendance')->truncate();
        $oldTeacherAttendances = DB::connection('old_edu')->table('teacher_attendance')->get();
        foreach ($oldTeacherAttendances as $oldTeacherAttendance) {
            $existingRecord = DB::connection('new_edu')->table('teacher_attendance')->where('id', $oldTeacherAttendance->id)->first();

            if ($existingRecord) {
                continue;
            }

            $mappedData = [
                "teacher_id" => $oldTeacherAttendance->teacher_id,
                "check_in" => $oldTeacherAttendance->check_in,
                "checkout" => $oldTeacherAttendance->checkout,
                "tpsc_generate_user_id" => $oldTeacherAttendance->tpsc_generate_user_id,
                "comment" => $oldTeacherAttendance->tpsc_generate_user_id,
                "updated_at" => now(),
                "created_at" => now(),
                "deleted_at" => null,
            ];
            DB::connection('new_edu')->table('teacher_attendance')->insert($mappedData);
        }
    }

    // cmd - php artisan migrate:tpsc-data staff_attendance
    public function staffAttendanceMigrate($dynamicModel, $tblName)
    {
        DB::connection('new_edu')->table('staff_attendance')->truncate();
        $oldStaffAttendances = DB::connection('old_edu')->table('staff_attendance')->get();
        foreach ($oldStaffAttendances as $oldStaffAttendance) {
            $existingRecord = DB::connection('new_edu')->table('staff_attendance')->where('id', $oldStaffAttendance->id)->first();

            if ($existingRecord) {
                continue;
            }

            $mappedData = [
                "staff_id" => $oldStaffAttendance->staff_id,
                "check_in" => $oldStaffAttendance->check_in,
                "checkout" => $oldStaffAttendance->checkout,
                "tpsc_generate_user_id" => $oldStaffAttendance->tpsc_generate_user_id,
                "comment" => $oldStaffAttendance->tpsc_generate_user_id,
                "updated_at" => now(),
                "created_at" => now(),
                "deleted_at" => null,
            ];
            DB::connection('new_edu')->table('staff_attendance')->insert($mappedData);
        }
    }

    // cmd - php artisan migrate:tpsc-data student_attendance
    public function studentAttendanceMigrate($dynamicModel, $tblName)
    {
        DB::connection('new_edu')->table('student_attendance')->truncate();
        $oldStudentAttendances = DB::connection('old_edu')->table('student_attendance')->get();

        foreach ($oldStudentAttendances as $oldStudentAttendance) {
            $existingRecord = DB::connection('new_edu')->table('student_attendance')->where('id', $oldStudentAttendance->id)->first();

            if ($existingRecord) {
                continue;
            }

            $departmentId = DB::connection('new_edu')->table('departments')
                ->where('department_name', $oldStudentAttendance->depertment_name)->where('class_id', $oldStudentAttendance->class_id)
                ->value('id');

            $sectionId = DB::connection('new_edu')->table('sections')
                ->where('name', $oldStudentAttendance->section_name)
                ->value('id');

            $mappedData = [
                "student_id" => $oldStudentAttendance->student_id,
                "year" => $oldStudentAttendance->year,
                "check_in" => $oldStudentAttendance->check_in,
                "checkout" => $oldStudentAttendance->checkout,
                "tpsc_gen_user_id" => $oldStudentAttendance->tpsc_gen_user_id,
                "class_id" => $oldStudentAttendance->class_id,
                "department_id" => $departmentId ?? NULL,
                "section_id" => $sectionId,
                "comment" => $oldStudentAttendance->comment,
                "updated_at" => now(),
                "created_at" => now(),
                "deleted_at" => null,
            ];
            DB::connection('new_edu')->table('student_attendance')->insert($mappedData);
        }
    }

    // ./ Attendance Migrate Function

    // ./ sms Migrate function sms_settings_gp
    // cmd - php artisan migrate:tpsc-data sms_settings_gp
    public function smsSettingGpMigrate($dynamicModel, $tblName)
    {
        DB::connection('new_edu')->table('sms_setting_gp')->truncate();
        $oldGpSMSAttendances = DB::connection('old_edu')->table('sms_settings_gp')->get();

        foreach ($oldGpSMSAttendances as $oldGpSMSAttendance) {

            $mappedData = [
                "username" => $oldGpSMSAttendance->username,
                "password" => $oldGpSMSAttendance->password,
                "apicode" => $oldGpSMSAttendance->apicode,
                "countrycode" => $oldGpSMSAttendance->countrycode,
                "cli" => $oldGpSMSAttendance->cli,
                "messagetype" => $oldGpSMSAttendance->messagetype,
                "messageid" => $oldGpSMSAttendance->messageid,
                "api_status" => 1,
                "test_number" => $oldGpSMSAttendance->test_number,
                "updated_at" => now(),
                "created_at" => now(),
            ];
            DB::connection('new_edu')->table('sms_setting_gp')->insert($mappedData);
        }
    }

    // cmd - php artisan migrate:tpsc-data sms_history
    public function smsHistoryMigrate($dynamicModel, $tblName)
    {
    }
    // / sms Migrate function

    // cmd - php artisan migrate:tpsc-data invoice
    // no need
    // public function invoiceMigrate($dynamicModel, $tblName)
    // {
    //     // Truncate the new table before inserting data
    //     DB::connection('new_edu')->table('invoices')->truncate();
    //     $oldInvoices = DB::connection('old_edu')->table('invoice')->get();

    //     $failedInserts = [];

    //     foreach ($oldInvoices as $oldInvoice) {
    //         try {
    //             $studentId = DB::connection('new_edu')->table('students')
    //                 ->where('student_roll', $oldInvoice->student_roll)
    //                 ->value('id');

    //             if (!$studentId) {
    //                 // Log or keep track of skipped records
    //                 $failedInserts[] = $oldInvoice->invoice_id;
    //                 continue;
    //             }

    //             $totalAmount = is_numeric($oldInvoice->on_net_total) ? (int) $oldInvoice->on_net_total : 0;
    //             $classId = is_numeric($oldInvoice->class_id) ? (int) $oldInvoice->class_id : NULL;

    //             // Validate dates
    //             $date = isset($oldInvoice->from_date) ? DateTime::createFromFormat('Y-m-d', $oldInvoice->from_date) : null;
    //             $toDate = isset($oldInvoice->to_date) ? DateTime::createFromFormat('Y-m-d', $oldInvoice->to_date) : null;

    //             $invoiceData = [
    //                 'id' => $oldInvoice->invoice_id,
    //                 'school_id' => 1,
    //                 'invoice_type' => 'student',
    //                 'invoice_type_id' => $studentId,
    //                 'invoice_code' => $oldInvoice->title,
    //                 'total_amount' => $totalAmount,
    //                 'total_waiver' => 0,
    //                 'total_payable' => $totalAmount,
    //                 'date' => $date ? $date->format('Y-m-d') : null,
    //                 'to_date' => $toDate ? $toDate->format('Y-m-d') : null,
    //                 'created_at' => $oldInvoice->created_at,
    //                 'updated_at' => $oldInvoice->updated_at,
    //             ];

    //             DB::connection('new_edu')->table('invoices')->insert($invoiceData);
    //         } catch (\Exception $e) {
    //             // Log error details
    //             $this->error('Error inserting invoice ID ' . $oldInvoice->invoice_id . ': ' . $e->getMessage());
    //         }
    //     }

    //     // Log or handle failed inserts
    //     if (!empty($failedInserts)) {
    //         $this->info('Failed to insert the following invoice IDs: ' . implode(', ', $failedInserts));
    //     }

    //     $this->info('Invoice migration completed.');
    // }

    // cmd - php artisan migrate:tpsc-data invoice_component
    public function invoiceComponentMigrate($dynamicModel, $tblName)
    {
        DB::connection('new_edu')->table('components')->truncate();

        $oldComponents = DB::connection('old_edu')->table('invoice_component')->get();

        foreach ($oldComponents as $oldComponent) {
            $newComponentData = [
                // id not like this 
                // 'id' => $oldComponent->invoice_component_id, 
                'school_id' => 1,
                'group_id' => NULL,
                'name' => $oldComponent->component_name,
                'value' => 0,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ];
            DB::connection('new_edu')->table('components')->insert($newComponentData);
        }
    }

    // cmd - php artisan migrate:tpsc-data invoice_child
    // // no need
    // public function invoiceDetailsMigrate($dynamicModel, $tblName)
    // {
    //     // DB::connection('new_edu')->transaction(function () use ($progressBar) {
    //     DB::connection('new_edu')->table('invoice_details')->truncate();

    //     // Fetch data from the old tables with a join to get the amount
    //     $oldData = DB::connection('old_edu')
    //         ->table('invoice_child')
    //         ->select(
    //             'invoice_child.invoice_child_id',
    //             'invoice_child.invoice_id',
    //             'invoice_child.component_id',
    //             'invoice_child.amount',
    //             'invoice_child.created_at',
    //             'invoice_child.updated_at'
    //         )
    //         ->get();

    //     // Insert data into the new invoice_details table
    //     foreach ($oldData as $data) {

    //         $invoiceData = [
    //             'id' => $data->invoice_child_id,
    //             'invoice_id' => $data->invoice_id,
    //             'components_id' => $data->component_id,
    //             'invoice_amount' => $data->amount,
    //             'waiver_amount' => 0,
    //             'payable_amount' => $data->amount,

    //             'created_at' => $data->created_at ?? now(),
    //             'updated_at' => $data->updated_at ?? now(),
    //         ];

    //         // Insert into the new table
    //         DB::connection('new_edu')->table('invoice_details')->insert($invoiceData);

    //         // Optionally: Handle progress bar advancement
    //         // $progressBar->advance();
    //     }
    //     // });
    // }

    // cmd - php artisan migrate:tpsc-data academic_syllebus
    public function academicSyllabusMigrate($dynamicModel, $tblName)
    {
        DB::connection('new_edu')->table('academic_syllabus')->truncate();
        $oldAcademicSyllabuses = DB::connection('old_edu')->table('academic_syllebus')->get();

        foreach ($oldAcademicSyllabuses as $oldAcademicSyllabus) {

            $classId = DB::connection('new_edu')->table('student_classes')
                ->where('id', $oldAcademicSyllabus->class_id)
                ->value('id');

            $departmentId = DB::connection('new_edu')->table('departments')
                ->where('department_name', $oldAcademicSyllabus->department)
                ->value('id');

            $subjectId = DB::connection('new_edu')->table('subjects')
                ->where('name', $oldAcademicSyllabus->subject)
                ->value('id');

            // dd($oldAcademicSyllabus);
            $syllabusData = [
                'school_id' => 1,
                'class_id' => $classId,
                'department_id' => $departmentId,
                'subject_id' => $subjectId,
                'title' => $oldAcademicSyllabus->title,
                'description' => $oldAcademicSyllabus->description,
                'file' => NULL,
                'status' => 1,
                'created_at' => $oldAcademicSyllabus->created_at,
                'updated_at' => $oldAcademicSyllabus->updated_at,
            ];

            DB::connection('new_edu')->table('academic_syllabus')->insert($syllabusData);
        }
    }

    // cmd - php artisan migrate:tpsc-data canteen_component
    public function MealTimeMigrate($dynamicModel, $tblName)
    {
        DB::connection('new_edu')->table('canteen_components')->truncate();
        $oldMealTimes = DB::connection('old_edu')->table('canteen_component')->get();

        foreach ($oldMealTimes as $oldMealTime) {

            // dd($oldMealTime);
            $MealTimeData = [
                'school_id' => 1,
                'name' => $oldMealTime->component_title,
                'status' => 1,
                'created_at' => $oldMealTime->created_at,
                'updated_at' => $oldMealTime->updated_at,
            ];

            DB::connection('new_edu')->table('canteen_components')->insert($MealTimeData);
        }
    }

    // cmd - php artisan migrate:tpsc-data templet_article
    public function bookAuthorMigrate($dynamicModel, $tblName)
    {
        DB::connection('new_edu')->table('book_authors')->truncate();

        $oldBookAuthors = DB::connection('old_edu')
            ->table('templet_article')
            ->select('author', 'created_at', 'updated_at')
            ->distinct()
            ->whereNotIn('author', ['0', '00', '000', ''])
            ->whereRaw('TRIM(author) != ""')
            ->get();

        foreach ($oldBookAuthors as $oldBookAuthor) {
            $authorName = trim($oldBookAuthor->author);

            if (!empty($authorName) && $authorName !== '00' && strlen($authorName) > 1) {
                $existingAuthor = DB::connection('new_edu')->table('book_authors')->where('name', $authorName)->first();

                if (!$existingAuthor) {
                    $bookAuthorData = [
                        'school_id' => 1,
                        'name' => $authorName,
                        'status' => 1,
                        'created_at' => $oldBookAuthor->created_at,
                        'updated_at' => $oldBookAuthor->updated_at,
                    ];

                    DB::connection('new_edu')->table('book_authors')->insert($bookAuthorData);
                }
            }
        }
    }

    // cmd - php artisan migrate:tpsc-data templet_article
    public function bookPublisherMigrate($dynamicModel, $tblName)
    {
        DB::connection('new_edu')->table('book_publishers')->truncate();

        $oldBookPublishers = DB::connection('old_edu')
            ->table('templet_article')
            ->select('publisher', 'created_at', 'updated_at')
            ->distinct()
            ->whereNotIn('publisher', ['0', '00', '000'])
            ->whereRaw('TRIM(publisher) != ""')
            ->get();

        foreach ($oldBookPublishers as $oldBookPublisher) {
            $publisherName = trim($oldBookPublisher->publisher);

            if (!empty($publisherName) && $publisherName !== '00' && strlen($publisherName) > 1) {
                $existingPublisher = DB::connection('new_edu')->table('book_publishers')->where('name', $publisherName)->first();

                if (!$existingPublisher) {
                    $bookPublisherData = [
                        'school_id' => 1,
                        'name' => $publisherName,
                        'status' => 1,
                        'created_at' => $oldBookPublisher->created_at,
                        'updated_at' => $oldBookPublisher->updated_at,
                    ];
                    DB::connection('new_edu')->table('book_publishers')->insert($bookPublisherData);
                }
            }
        }
    }

    // cmd - php artisan migrate:tpsc-data templet_article
    public function bookEditionsMigrate($dynamicModel, $tblName)
    {
        DB::connection('new_edu')->table('book_editions')->truncate();

        $oldBookEditions = DB::connection('old_edu')
            ->table('templet_article')
            ->select('edition', 'created_at', 'updated_at')
            ->distinct()
            ->whereNotIn('edition', ['0', '00', '000'])
            ->whereRaw('TRIM(edition) != ""')
            ->get();

        foreach ($oldBookEditions as $oldBookEditions) {
            $editionName = trim($oldBookEditions->edition);

            if (!empty($editionName) && $editionName !== '00' && strlen($editionName) > 1) {
                $existingEdition = DB::connection('new_edu')->table('book_editions')->where('name', $editionName)->first();

                if (!$existingEdition) {
                    $bookEditionData = [
                        'school_id' => 1,
                        'name' => $editionName,
                        'status' => 1,
                        'created_at' => $oldBookEditions->created_at,
                        'updated_at' => $oldBookEditions->updated_at,
                    ];

                    DB::connection('new_edu')->table('book_editions')->insert($bookEditionData);
                }
            }
        }
    }

    // cmd - php artisan migrate:tpsc-data templet_article
    public function bookLanguageMigrate($dynamicModel, $tblName)
    {
        DB::connection('new_edu')->table('book_languages')->truncate();

        $oldBookLanguages = DB::connection('old_edu')
            ->table('templet_article')
            ->select('language', 'created_at', 'updated_at')
            ->distinct()
            ->whereNotIn('language', ['0', '00', '000'])
            ->whereRaw('TRIM(language) != ""')
            ->get();

        foreach ($oldBookLanguages as $oldBookLanguage) {
            $languageName = trim($oldBookLanguage->language);

            if (!empty($languageName) && $languageName !== '00' && strlen($languageName) > 1) {
                $existingLanguage = DB::connection('new_edu')->table('book_languages')->where('name', $languageName)->first();

                if (!$existingLanguage) {
                    $bookLanguageData = [
                        'school_id' => 1,
                        'name' => $languageName,
                        'status' => 1,
                        'created_at' => $oldBookLanguage->created_at,
                        'updated_at' => $oldBookLanguage->updated_at,
                    ];

                    DB::connection('new_edu')->table('book_languages')->insert($bookLanguageData);
                }
            }
        }
    }

    // cmd - php artisan migrate:tpsc-data templet_article
    public function bookAccessionsMigrate($dynamicModel, $tblName)
    {
        // templet_article [templet_id] = book_accessions [id]
        // article_info [article_name f.k - templet_id] = stock_books [book_accession_id f.k - book_accessions ]

        DB::connection('new_edu')->table('book_accessions')->truncate();
        $oldBookAccessions = DB::connection('old_edu')->table('templet_article')->get();

        foreach ($oldBookAccessions as $oldBookAccession) {

            $authorName = trim($oldBookAccession->author);
            $publisherName = trim($oldBookAccession->publisher);
            $editionName = trim($oldBookAccession->edition);
            $languageName = trim($oldBookAccession->language);

            $authorId = DB::connection('new_edu')->table('book_authors')
                ->where('name', $authorName)
                ->value('id');

            $publisherId = DB::connection('new_edu')->table('book_publishers')
                ->where('name', $publisherName)
                ->value('id');

            $editionId = DB::connection('new_edu')->table('book_editions')
                ->where('name', $editionName)
                ->value('id');

            $languageId = DB::connection('new_edu')->table('book_languages')
                ->where('name', $languageName)
                ->value('id');


            $bookAccessionsData = [
                "id" => $oldBookAccession->templet_id,
                'book_title' => $oldBookAccession->article_name,
                'school_id' => 1,

                'author' => $authorId,
                'publisher' => $publisherId,
                'edition' => $editionId,
                'language' => $languageId,
                'cell_number' => NULL,

                'phone' => $oldBookAccession->call_no,
                'isbn' => $oldBookAccession->isbn,
                'soft_copy' => NULL,
                'book_type' => 2,

                'status' => 1,
                'created_at' => $oldBookAccession->created_at,
                'updated_at' => $oldBookAccession->updated_at,
            ];

            DB::connection('new_edu')->table('book_accessions')->insert($bookAccessionsData);
        }
    }

    // cmd - php artisan migrate:tpsc-data stock_article
    public function bookStockMigrate($dynamicModel, $tblName)
    {
        DB::connection('new_edu')->table('stock_books')->truncate();

        $oldStocks = DB::connection('old_edu')->table('stock_article')->get();

        foreach ($oldStocks as $oldStock) {
            // ddA($oldStock);

            $bookAccessionId = DB::connection('new_edu')->table('book_accessions')
                ->where('book_title', $oldStock->article_name)
                ->value('id');

            $stockDate = isset($oldStock->stock_date) ? DateTime::createFromFormat('Y-m-d', $oldStock->stock_date) : null;
            $quantity = is_numeric($oldStock->quantity) ? (int) $oldStock->quantity : 0;

            $bookStockData = [
                'school_id' => 1,

                'book_accession_id' => $bookAccessionId,
                'stock_date' => $stockDate ? $stockDate->format('Y-m-d') : null,
                'net_price' => $oldStock->net_price,
                'purchase_price' => $oldStock->purchase_price,
                'discount' => $oldStock->discount,
                'quantity' => $quantity,
                'available_quantity' => 0,

                'status' => 1,
                'created_at' => $oldStock->created_at,
                'updated_at' => $oldStock->updated_at,
            ];

            DB::connection('new_edu')->table('stock_books')->insert($bookStockData);
        }
    }

    // cmd - php artisan migrate:tpsc-data manage_category
    public function storeCategoryMigrate($dynamicModel, $tblName)
    {
        DB::connection('new_edu')->table('store_categories')->truncate();
        $oldCategories = DB::connection('old_edu')->table('manage_category')->get();

        foreach ($oldCategories as $oldCategory) {
            $categoryData = [
                'school_id' => 1,
                'name' => $oldCategory->category_name,
                'status' => 1,
                'created_at' => $oldCategory->created_at,
                'updated_at' => $oldCategory->updated_at,
            ];

            DB::connection('new_edu')->table('store_categories')->insert($categoryData);
        }
    }

    // cmd - php artisan migrate:tpsc-data manage_products
    public function storeProductMigrate($dynamicModel, $tblName)
    {
        DB::connection('new_edu')->table('store_products')->truncate();
        $oldProducts = DB::connection('old_edu')->table('manage_products')->get();

        foreach ($oldProducts as $oldProduct) {

            $productData = [
                'school_id' => 1,
                'category_id' => $oldProduct->category_id,
                'name' => $oldProduct->products_name,
                'description' => $oldProduct->products_details,
                'status' => 1,
                'created_at' => $oldProduct->created_at,
                'updated_at' => $oldProduct->updated_at,
            ];

            DB::connection('new_edu')->table('store_products')->insert($productData);
        }
    }

    // cmd - php artisan migrate:tpsc-data manage_products_stock
    // public function storeStockInMigrate($dynamicModel, $tblName)
    // {
    //     DB::connection('new_edu')->table('stock_ins')->truncate();
    //     $oldStockIns = DB::connection('old_edu')->table('manage_products_stock')->get();

    //     foreach ($oldStockIns as $oldStockIn) {

    //         $stockInData = [
    //             'school_id' => 1,
    //             'product_id' => 26,
    //             'category_id' => $oldStockIn->category_id,
    //             'product_code' => 'P00021',
    //             'purchase_date' => $oldStockIn->stock_date,
    //             'sale_date' => NULL,
    //             'purchase_price' => $oldStockIn->purchase_price,
    //             'sale_price' => $oldStockIn->sell_price,
    //             'purchase_quantity' => $oldStockIn->quantity,
    //             'sale_quantity' => 0,

    //             'status' => 1,
    //             'created_at' => $oldStockIn->created_at,
    //             'updated_at' => $oldStockIn->updated_at,
    //         ];

    //         DB::connection('new_edu')->table('stock_ins')->insert($stockInData);
    //     }
    // }

    // cmd - php artisan migrate:tpsc-data sell_products
    // public function storeStockOutMigrate($dynamicModel, $tblName)
    // {
    //     DB::connection('new_edu')->table('stock_outs')->truncate();
    //     $oldStockOuts = DB::connection('old_edu')->table('sell_products')->get();

    //     foreach ($oldStockOuts as $oldStockOut) {

    //         $stockOutData = [
    //             'school_id' => 1,
    //             'product_id' => NULL,
    //             'product_code' => NULL,
    //             'stock_id' => NULL,
    //             'customer_type' => 'student',

    //             'customer_id' => $oldStockOut->buyer_id,
    //             'sale_date' => $oldStockOut->sell_date,

    //             'price' => $oldStockOut->price,
    //             'total_price' => $oldStockOut->total_price,
    //             'quantity' => $oldStockOut->quantity,

    //             'status' => 1,
    //             'created_at' => $oldStockOut->created_at,
    //             'updated_at' => $oldStockOut->updated_at,
    //         ];

    //         DB::connection('new_edu')->table('stock_outs')->insert($stockOutData);
    //     }
    // }

    // cmd - php artisan migrate:tpsc-data manage_dormitory
    public function manageDormitoryMigrate($dynamicModel, $tblName)
    {
        DB::connection('new_edu')->table('manage_dormitories')->truncate();
        $manageDormitories = DB::connection('old_edu')->table('manage_dormitory')->get();

        foreach ($manageDormitories as $manageDormitory) {

            $manageDormitoryData = [
                'dormitory_name' => $manageDormitory->dormitory_title,
                'category_id' => NULL,
                'author_id' => 1,
                'dormitory_no' => $manageDormitory->dormitory_no,
                'total_floor' => $manageDormitory->dormitory_floor,
                'total_room' => $manageDormitory->total_room_number,
                'total_seat' => $manageDormitory->total_seat_number,
                'location' => $manageDormitory->dormitory_location,
                'description' => $manageDormitory->description,

                'school_id' => 1,
                'status' => 1,
                'created_by' => 1,
                'created_at' => $manageDormitory->created_at,
                'updated_at' => $manageDormitory->updated_at,
            ];

            DB::connection('new_edu')->table('manage_dormitories')->insert($manageDormitoryData);
        }
    }

    // cmd - php artisan migrate:tpsc-data assign_dormitory
    public function assignDormitoryMigrate($dynamicModel, $tblName)
    {
        DB::connection('new_edu')->table('assign_dormitories')->truncate();
        $assignDormitories = DB::connection('old_edu')->table('assign_dormitory')->get();

        foreach ($assignDormitories as $assignDormitory) {
            $studentId = DB::connection('new_edu')->table('students')
                ->where('student_roll', $assignDormitory->student_roll)
                ->value('id');

            $assignDormitoryData = [
                'student_id' => $studentId,
                'dormitory_id' => 1,
                'floor_number' => 1,
                'room_number' => $assignDormitory->room_number,
                'seat_number' => $assignDormitory->seat_number,
                'arrive_date' => $assignDormitory->arrive_date,
                'invoice_type' => 2,
                'description' => $assignDormitory->description,

                'school_id' => 1,
                'status' => 1,
                'created_by' => 1,
                'created_at' => $assignDormitory->created_at,
                'updated_at' => $assignDormitory->updated_at,
            ];

            DB::connection('new_edu')->table('assign_dormitories')->insert($assignDormitoryData);
        }
    }

    // cmd - php artisan migrate:tpsc-data notice_board
    public function noticeBoardMigrate($dynamicModel, $tblName)
    {
        DB::connection('new_edu')->table('notice_boards')->truncate();
        $noticeBoards = DB::connection('old_edu')->table('notice_board')->get();

        foreach ($noticeBoards as $noticeBoard) {
            $noticeBoardData = [
                'title' => $noticeBoard->notice_title,
                'slug' => NULL,
                'subject' => $noticeBoard->notice_subject ?? NULL,
                'author' => $noticeBoard->author ?? NULL,
                'notice_to' => $noticeBoard->to ?? NULL,
                'notice' => $noticeBoard->Notice ?? NULL,

                'school_id' => 1,
                'status' => 1,
                'created_at' => $noticeBoard->created_at,
                'updated_at' => $noticeBoard->updated_at,
            ];

            DB::connection('new_edu')->table('notice_boards')->insert($noticeBoardData);
        }
    }

    // cmd - php artisan migrate:tpsc-data live_class
    public function liveClassMigrate($dynamicModel, $tblName)
    {
        DB::connection('new_edu')->table('live_classes')->truncate();
        $liveClasses = DB::connection('old_edu')->table('live_class')->get();

        foreach ($liveClasses as $liveClass) {
            $departments = is_string($liveClass->department) ? json_decode($liveClass->department, true) : $liveClass->department;
            $departmentId = null;

            if (!empty($departments) && is_array($departments)) {
                $firstDepartment = $departments[0];
                $departmentId = DB::connection('new_edu')->table('departments')
                    ->where('department_name', $firstDepartment)
                    ->value('id');
            }

            $sessionId = DB::connection('new_edu')->table('session_years')
                ->where('title', $liveClass->session)
                ->value('id');

            $noticeBoardData = [
                'topic' => $liveClass->topic,
                'date' => null,
                'start_time' => $liveClass->start_time ?? null,
                'end_time' => $liveClass->end_time ?? null,
                'duration' => $liveClass->duration ?? null,
                'teacher_id' => $liveClass->teacher_id ?? null,
                'meeting_id' => $liveClass->meeting_id ?? null,
                'meeting_link' => $liveClass->start_url ?? null,
                'class_id' => $liveClass->class_id ?? null,
                'department_id' => $departmentId,
                'session_year_id' => $sessionId,
                'file' => null,
                'school_id' => 1,
                'status' => $liveClass->status == '1' ? 1 : 0,
                'created_at' => $liveClass->created_at,
                'updated_at' => $liveClass->updated_at,
            ];
            DB::connection('new_edu')->table('live_classes')->insert($noticeBoardData);
        }
    }

    // Migration by newaz
    // cmd - php artisan migrate:tpsc-data applicant_student
    // no need migrate
    // public function applicantStudentMigrate($dynamicModel, $tblName)
    // {

    //     DB::connection('new_edu')->statement('SET FOREIGN_KEY_CHECKS=0;');

    //     DB::connection('new_edu')->table('admission_requests')->truncate();
    //     DB::connection('new_edu')->table('applicant_guardians')->truncate();
    //     DB::connection('new_edu')->table('applicant_previous_education')->truncate();
    //     DB::connection('new_edu')->table('applicant_permanent_addresses')->truncate();
    //     DB::connection('new_edu')->table('applicant_present_addresses')->truncate();

    //     // Re-enable foreign key checks
    //     DB::connection('new_edu')->statement('SET FOREIGN_KEY_CHECKS=1;');

    //     $old_datas = DB::connection('old_edu')->table('applicant_student')->get();

    //     if ($old_datas && $old_datas != null) {
    //         foreach ($old_datas as $old_data) {

    //             $existingUsers = DB::connection('new_edu')->table('users')
    //                 ->where('email', $old_data->applicant_id)
    //                 ->get();

    //             // If multiple records exist, delete all of them
    //             if ($existingUsers->isNotEmpty()) {
    //                 DB::connection('new_edu')->table('users')
    //                     ->where('email', $old_data->applicant_id)
    //                     ->delete();
    //             }

    //             // Get session, department, and section IDs
    //             $sessionId = DB::connection('new_edu')->table('session_years')
    //                 ->where('title', $old_data->session)
    //                 ->value('id');

    //             $departmentId = DB::connection('new_edu')->table('departments')
    //                 ->where('department_name', $old_data->department)
    //                 ->value('id');

    //             $sectionId = DB::connection('new_edu')->table('sections')
    //                 ->where('class_id', $old_data->class_id)
    //                 ->value('id');

    //             // Insert into users table and get user ID
    //             $userId = DB::connection('new_edu')->table('users')->insertGetId([
    //                 'username' => $old_data->student_name,
    //                 'email' => $old_data->applicant_id,
    //                 'password' => Hash::make('123456'),
    //                 'role_id' => 7,
    //                 'type' => 7,
    //                 'school_id' => 1,
    //                 'status' => 1,
    //                 'created_at' => now(),
    //                 'updated_at' => now(),
    //             ]);

    //             // Insert into admission_requests table and get the inserted ID
    //             $applicantRequestId = DB::connection('new_edu')->table('admission_requests')->insertGetId([
    //                 'user_id' => $userId,
    //                 'applicant_id' => $old_data->applicant_id,
    //                 'applicant_name_en' => $old_data->student_name,
    //                 'father_name_en' => $old_data->parent_name,
    //                 'father_phone' => $old_data->father_Phone,
    //                 'mother_name_en' => $old_data->mother_name,
    //                 'mother_phone' => $old_data->mother_Phone ?? null,
    //                 'blood_group' => $old_data->blood_group,
    //                 'session_id' => $sessionId,
    //                 'class_id' => $old_data->class_id,
    //                 'profile_photo' => $old_data->image,
    //                 'department_id' => $departmentId,
    //                 'section_id' => $sectionId,
    //                 'religion' => $old_data->religion,
    //                 'date_of_birth' => $old_data->birth_date,
    //                 'gender' => $old_data->gender,
    //                 'applicant_phone' => $old_data->phone,
    //                 'applicant_email' => $old_data->email,
    //                 'quota' => $old_data->quota,
    //                 'status' => 1,
    //                 'created_at' => $old_data->created_at,
    //                 'updated_at' => $old_data->updated_at,
    //             ]);

    //             // Insert into applicant_guardians table

    //             DB::connection('new_edu')->table('applicant_guardians')->insert([
    //                 'user_id' => $userId,
    //                 'admission_requests_id' => $applicantRequestId,
    //                 'guardian_name' => $old_data->ab_guardian_name,
    //                 'guardian_relation' => $old_data->ab_guardian_relation,
    //                 'guardian_phone' => $old_data->ab_guardian_number,
    //                 'created_at' => $old_data->created_at,
    //                 'updated_at' => $old_data->updated_at,
    //             ]);


    //             $eold_datas = DB::connection('old_edu')->table('applicant_student_educational_q')
    //                 ->get();

    //             foreach ($eold_datas as $eold_data) {

    //                 DB::connection('new_edu')->table('applicant_previous_education')->insert([
    //                     'admission_requests_id' => $applicantRequestId,
    //                     'user_id' => $userId,
    //                     'board_name' => $eold_data->borad ?? null,
    //                     'exam_name' => $eold_data->exam_name ?? null,
    //                     'reg_no' => $eold_data->reg_no ?? null,
    //                     'roll_no' => $eold_data->roll_no ?? null,
    //                     'group' => $eold_data->group ?? null,
    //                     'passing_year' => $eold_data->passing_year ?? null,
    //                     'gpa' => $eold_data->gpa ?? null,
    //                     'status' => 1,
    //                     'created_at' => $eold_data->created_at,
    //                     'updated_at' => $eold_data->updated_at,
    //                 ]);
    //             }

    //             // Process the present address for this applicant
    //             $addpreold_datas = DB::connection('old_edu')->table('applicant_student_child')
    //                 ->where('applicant_id', $old_data->applicant_id) // Match records with the current applicant
    //                 ->get();
    //             foreach ($addpreold_datas as $adold_data) {
    //                 $divisionId = DB::connection('new_edu')->table('divisions')
    //                     ->where('name', $adold_data->division)
    //                     ->value('id');
    //                 $districtId = DB::connection('new_edu')->table('districts')
    //                     ->where('name', $adold_data->home_district)
    //                     ->value('id');
    //                 $thanaId = DB::connection('new_edu')->table('upazilas')
    //                     ->where('name', $adold_data->thana)
    //                     ->value('id');
    //                 $unionId = DB::connection('new_edu')->table('unions')
    //                     ->where('name', $adold_data->union_name)
    //                     ->value('id');


    //                 DB::connection('new_edu')->table('applicant_present_addresses')->insert([
    //                     'admission_requests_id' => $applicantRequestId,
    //                     'user_id' => $userId,
    //                     'division' => $divisionId ?? null,
    //                     'district' => $districtId ?? null,
    //                     'upazila' => $thanaId ?? null,
    //                     'union' => $unionId ?? null,
    //                     'post_office' => $adold_data->post_office ?? null,
    //                     'village' => $adold_data->village_name ?? null,
    //                     'created_at' => $adold_data->created_at,
    //                     'updated_at' => $adold_data->updated_at,
    //                 ]);
    //             }

    //             // Process the permanent address for this applicant
    //             foreach ($addpreold_datas as $adold_data) {
    //                 $divisionId = DB::connection('new_edu')->table('divisions')
    //                     ->where('name', $adold_data->Pr_division)
    //                     ->value('id');
    //                 $districtId = DB::connection('new_edu')->table('districts')
    //                     ->where('name', $adold_data->Pr_district)
    //                     ->value('id');
    //                 $thanaId = DB::connection('new_edu')->table('upazilas')
    //                     ->where('name', $adold_data->pr_thana)
    //                     ->value('id');
    //                 $unionId = DB::connection('new_edu')->table('unions')
    //                     ->where('name', $adold_data->pr_union)
    //                     ->value('id');


    //                 DB::connection('new_edu')->table('applicant_permanent_addresses')->insert([
    //                     'admission_requests_id' => $applicantRequestId,
    //                     'user_id' => $userId,
    //                     'division' => $divisionId ?? null,
    //                     'district' => $districtId ?? null,
    //                     'upazila' => $thanaId ?? null,
    //                     'union' => $unionId ?? null,
    //                     'post_office' => $adold_data->pr_post_office ?? null,
    //                     'village' => $adold_data->pr_village ?? null,
    //                     'created_at' => $adold_data->created_at,
    //                     'updated_at' => $adold_data->updated_at,
    //                 ]);
    //             }
    //         }

    //         return "<h6 style='color: green'>Inserted</h6>";
    //     } else {
    //         return "<h6 style='color: red'>No Data Found</h6>";
    //     }
    // }

    // cmd - php artisan migrate:tpsc-data website_slider
    public function websiteSliderMigrate($dynamicModel, $tblName)
    {
        DB::connection('new_edu')->table('sliders')->truncate();
        $old_datas = DB::connection('old_edu')->table('website_slider')->get();
        // dd($old_datas);
        if ($old_datas && $old_datas != null) {
            foreach ($old_datas as $old_data) {
                $r_data = DB::connection('new_edu')->table('sliders')->insert([
                    'id' => $old_data->website_slider_id,
                    'title' => $old_data->title,
                    'image' => $old_data->image,
                    'status' => 1,
                    'school_id' => 1,
                    'created_at' => $old_data->created_at,
                    'updated_at' => $old_data->updated_at,
                ]);
            }
            return "<h6 style='color: green'>Inserted</h6>";
        } else {
            return "<h6 style='color: red'>Not Inserted</h6>";
        }
    }

    // cmd - php artisan migrate:tpsc-data website_contact
    public function websiteContactMigrate()
    {
        DB::connection('new_edu')->table('website_contacts')->truncate();
        $old_datas = DB::connection('old_edu')->table('website_contact')->get();
        // dd($old_datas);
        if ($old_datas && $old_datas != null) {
            foreach ($old_datas as $old_data) {
                $designationId = DB::connection('new_edu')->table('designations')
                    ->where('name', $old_data->designation)
                    ->value('id');
                $r_data = DB::connection('new_edu')->table('website_contacts')->insert([
                    'id' => $old_data->id,
                    'name' => $old_data->name,
                    'designation_id' => $designationId,
                    'phone' => $old_data->phone,
                    'email' => $old_data->email,
                    'status' => 1,
                    'school_id' => 1,
                    'created_at' => $old_data->created_at,
                    'updated_at' => $old_data->updated_at,
                ]);
            }
            return "<h6 style='color: green'>Inserted</h6>";
        } else {
            return "<h6 style='color: red'>Not Inserted</h6>";
        }
    }

    // cmd - php artisan migrate:tpsc-data website_course_category
    public function courseCategoriesMigrate()
    {
        DB::connection('new_edu')->table('course_categories')->truncate();
        $old_datas = DB::connection('old_edu')->table('website_course_category')->get();
        // dd($old_datas);
        if ($old_datas && $old_datas != null) {
            foreach ($old_datas as $old_data) {
                $r_data = DB::connection('new_edu')->table('course_categories')->insert([
                    'id' => $old_data->id,
                    'name' => $old_data->name,
                    'description' => $old_data->description,
                    'status' => 1,
                    'school_id' => 1,
                    'created_at' => $old_data->created_at,
                    'updated_at' => $old_data->updated_at,
                ]);
            }
            return "<h6 style='color: green'>Inserted</h6>";
        } else {
            return "<h6 style='color: red'>Not Inserted</h6>";
        }
    }

    // cmd - php artisan migrate:tpsc-data website_event
    public function websiteEventMigrate($dynamicModel, $tblName)
    {
        DB::connection('new_edu')->table('events')->truncate();
        $old_datas = DB::connection('old_edu')->table('website_event')->get();
        // dd($old_datas);
        if ($old_datas && $old_datas != null) {
            foreach ($old_datas as $old_data) {
                $r_data = DB::connection('new_edu')->table('events')->insert([
                    'id' => $old_data->website_event_id,
                    'title' => $old_data->title,
                    'slug' => Str::slug($old_data->title),
                    'description' => $old_data->description,
                    'event_type' => $old_data->type,
                    'image' => $old_data->image,
                    'status' => 1,
                    'school_id' => 1,
                    'created_at' => $old_data->created_at,
                    'updated_at' => $old_data->updated_at,
                ]);
            }
            return "<h6 style='color: green'>Inserted</h6>";
        } else {
            return "<h6 style='color: red'>Not Inserted</h6>";
        }
    }

    // cmd - php artisan migrate:tpsc-data website_setup
    public function websiteSetupsMigrate($dynamicModel, $tblName)
    {
        DB::connection('new_edu')->table('website_setups')->truncate();
        $old_datas = DB::connection('old_edu')->table('website_setup')->get();

        // Define the keys
        $key_names = [
            'about_us',
            'history',
            'mission_vision',
            'principle_message',
            'vice_principle_message',
            'md_message',
            'ed_message',
            'chairman_message',
            'a_p_message',
            'providan_link',
            'academic_calender_link'
        ];

        if ($old_datas && $old_datas != null) {
            foreach ($old_datas as $old_data) {
                foreach ($key_names as $key_name) {

                    // Determine the type based on the key name
                    $type = in_array($key_name, ['providan_link', 'academic_calender_link']) ? 'text' : 'textarea';

                    // Insert the data into the table
                    DB::connection('new_edu')->table('website_setups')->insert([
                        'school_id' => 1,
                        'key' => $key_name,
                        'type' => $type, // Set type based on condition
                        'display_name' => ucwords(str_replace('_', ' ', $key_name)),
                        'value' => $old_data->$key_name,
                        'created_at' => now(),
                        'updated_at' => $old_data->updated_at,
                    ]);
                }
            }

            return "<h6 style='color: green'>Inserted</h6>";
        } else {
            return "<h6 style='color: red'>Not Inserted</h6>";
        }
    }
}

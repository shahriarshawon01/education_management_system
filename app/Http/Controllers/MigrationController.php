<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class MigrationController extends Controller
{
    public function applicant_requests()
    {
        $old_datas = DB::connection('old_edu')->table('applicant_student')->get();

        if ($old_datas && $old_datas != null) {
            foreach ($old_datas as $old_data) {

                $existingUsers = DB::connection('new_edu')->table('users')
                    ->where('email', $old_data->applicant_id)
                    ->get();

                // If multiple records exist, delete all of them
                if ($existingUsers->isNotEmpty()) {
                    DB::connection('new_edu')->table('users')
                        ->where('email', $old_data->applicant_id)
                        ->delete();
                }

                // Get session, department, and section IDs
                $sessionId = DB::connection('new_edu')->table('session_years')
                    ->where('title', $old_data->session)
                    ->value('id');

                $departmentId = DB::connection('new_edu')->table('departments')
                    ->where('department_name', $old_data->department)
                    ->value('id');

                $sectionId = DB::connection('new_edu')->table('sections')
                    ->where('class_id', $old_data->class_id)
                    ->value('id');

                // Insert into users table and get user ID
                $userId = DB::connection('new_edu')->table('users')->insertGetId([
                    'username' => $old_data->student_name,
                    'email' => $old_data->applicant_id,
                    'password' => Hash::make('123456'),
                    'role_id' => 7,
                    'type' => 7,
                    'school_id' => 1,
                    'status' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Insert into admission_requests table and get the inserted ID
                $applicantRequestId = DB::connection('new_edu')->table('admission_requests')->insertGetId([
                    'user_id' => $userId,
                    'applicant_id' => $old_data->applicant_id,
                    'applicant_name_en' => $old_data->student_name,
                    'father_name_en' => $old_data->parent_name,
                    'father_phone' => $old_data->father_Phone,
                    'mother_name_en' => $old_data->mother_name,
                    'mother_phone' => $old_data->mother_Phone ?? null,
                    'blood_group' => $old_data->blood_group,
                    'session_id' => $sessionId,
                    'class_id' => $old_data->class_id,
                    'profile_photo' => $old_data->image,
                    'department_id' => $departmentId,
                    'section_id' => $sectionId,
                    'religion' => $old_data->religion,
                    'date_of_birth' => $old_data->birth_date,
                    'gender' => $old_data->gender,
                    'applicant_phone' => $old_data->phone,
                    'applicant_email' => $old_data->email,
                    'quota' => $old_data->quota,
                    'status' => 1,
                    'created_at' => $old_data->created_at,
                    'updated_at' => $old_data->updated_at,
                ]);

                // Insert into applicant_guardians table
                DB::connection('new_edu')->table('applicant_guardians')->insert([
                    'user_id' => $userId,
                    'admission_requests_id' => $applicantRequestId,
                    'guardian_name' => $old_data->ab_guardian_name,
                    'guardian_relation' => $old_data->ab_guardian_relation,
                    'guardian_phone' => $old_data->ab_guardian_number,
                    'created_at' => $old_data->created_at,
                    'updated_at' => $old_data->updated_at,
                ]);

                $eold_datas = DB::connection('old_edu')->table('applicant_student_educational_q')
                    ->get();

                foreach ($eold_datas as $eold_data) {
                    DB::connection('new_edu')->table('applicant_previous_education')->insert([
                        'admission_requests_id' => $applicantRequestId,
                        'user_id' => $userId,
                        'board_name' => $eold_data->borad ?? null,
                        'exam_name' => $eold_data->exam_name ?? null,
                        'reg_no' => $eold_data->reg_no ?? null,
                        'roll_no' => $eold_data->roll_no ?? null,
                        'group' => $eold_data->group ?? null,
                        'passing_year' => $eold_data->passing_year ?? null,
                        'gpa' => $eold_data->gpa ?? null,
                        'status' => 1,
                        'created_at' => $eold_data->created_at,
                        'updated_at' => $eold_data->updated_at,
                    ]);
                }

                // Process the present address for this applicant
                $addpreold_datas = DB::connection('old_edu')->table('applicant_student_child')
                    ->where('applicant_id', $old_data->applicant_id) // Match records with the current applicant
                    ->get();

                foreach ($addpreold_datas as $adold_data) {
                    $divisionId = DB::connection('new_edu')->table('divisions')
                        ->where('name', $adold_data->division)
                        ->value('id');
                    $districtId = DB::connection('new_edu')->table('districts')
                        ->where('name', $adold_data->home_district)
                        ->value('id');
                    $thanaId = DB::connection('new_edu')->table('upazilas')
                        ->where('name', $adold_data->thana)
                        ->value('id');
                    $unionId = DB::connection('new_edu')->table('unions')
                        ->where('name', $adold_data->union_name)
                        ->value('id');

                    DB::connection('new_edu')->table('applicant_present_addresses')->insert([
                        'admission_requests_id' => $applicantRequestId,
                        'user_id' => $userId,
                        'division' => $divisionId ?? null,
                        'district' => $districtId ?? null,
                        'upazila' => $thanaId ?? null,
                        'union' => $unionId ?? null,
                        'post_office' => $adold_data->post_office ?? null,
                        'village' => $adold_data->village_name ?? null,
                        'created_at' => $adold_data->created_at,
                        'updated_at' => $adold_data->updated_at,
                    ]);
                }

                // Process the permanent address for this applicant
                foreach ($addpreold_datas as $adold_data) {
                    $divisionId = DB::connection('new_edu')->table('divisions')
                        ->where('name', $adold_data->Pr_division)
                        ->value('id');
                    $districtId = DB::connection('new_edu')->table('districts')
                        ->where('name', $adold_data->Pr_district)
                        ->value('id');
                    $thanaId = DB::connection('new_edu')->table('upazilas')
                        ->where('name', $adold_data->pr_thana)
                        ->value('id');
                    $unionId = DB::connection('new_edu')->table('unions')
                        ->where('name', $adold_data->pr_union)
                        ->value('id');

                    DB::connection('new_edu')->table('applicant_permanent_addresses')->insert([
                        'admission_requests_id' => $applicantRequestId,
                        'user_id' => $userId,
                        'division' => $divisionId ?? null,
                        'district' => $districtId ?? null,
                        'upazila' => $thanaId ?? null,
                        'union' => $unionId ?? null,
                        'post_office' => $adold_data->pr_post_office ?? null,
                        'village' => $adold_data->pr_village ?? null,
                        'created_at' => $adold_data->created_at,
                        'updated_at' => $adold_data->updated_at,
                    ]);
                }

            }

            return "<h6 style='color: green'>Inserted</h6>";
        } else {
            return "<h6 style='color: red'>No Data Found</h6>";
        }
    }

    //slider migration
    public function slider()
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

    //website contact migration
    public function website_contact()
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

    //website course categories migration
    public function course_categories()
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

    //event migration
    public function event()
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

    //faculties migration
    // public function faculties(){
    //   // DB::connection('new_edu')->table('faculties')->truncate();
    //   $old_datas = DB::connection('old_edu')->table('website_faculties')->get();
    //   dd($old_datas);
    //   if($old_datas && $old_datas != null){
    //     foreach($old_datas as $old_data){
    //      $r_data = DB::connection('new_edu')->table('faculties')->insert([
    //           'id' => $old_data->website_faculties_id,
    //           'department_id' => $old_data->title,
    //           'slug' => Str::slug($old_data->title),
    //           'teacher_id' => $old_data->description,
    //           'msg_from_head' => $old_data->type,
    //           'lab_info' => $old_data->lab_info,
    //           'status' => 1,                       
    //           'school_id' => 1,
    //           'created_at' => $old_data->created_at,
    //           'updated_at' => $old_data->updated_at,
    //       ]);
    //   }
    //     return "<h6 style='color: green'>Inserted</h6>";
    //   }else{
    //     return "<h6 style='color: red'>Not Inserted</h6>";
    //   }
    // }

    //faculties migration
    public function website_setups()
    {
        DB::connection('new_edu')->table('website_setups')->truncate();
        $old_datas = DB::connection('old_edu')->table('website_setup')->get();
        // dd($old_datas);
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
                    DB::connection('new_edu')->table('website_setups')->insert([
                        'school_id' => 1,
                        'key' => $key_name,
                        'type' => 'textarea',
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

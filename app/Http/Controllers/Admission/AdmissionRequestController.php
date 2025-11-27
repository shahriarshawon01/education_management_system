<?php

namespace App\Http\Controllers\Admission;

use App\Models\Section;
use App\Models\Department;
use App\Models\SessionYear;
use Illuminate\Support\Str;
use App\Models\StudentClass;
use Illuminate\Http\Request;
use App\Models\AdmissionRequest;
use App\Models\ApplicantGuardian;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\ApplicantPresentAddress;
use App\Models\ApplicantPermanentAddress;
use App\Models\ApplicantPreviousEducation;
use Illuminate\Support\Facades\Validator;

class AdmissionRequestController extends Controller
{

    public $model;
    public function __construct()
    {
        $this->model = new AdmissionRequest();
    }

    public function admission_request(){
        return view("frontend.pages.admission.admission_request");
    }

    public function student_admission(Request $request){
        $input = $request->all();
        $admissionReq = new AdmissionRequest();
    
        // Get the validation rules from the model
        $rules = $admissionReq->rules();
        
        // Validate the request data against the rules
        $validate = Validator::make($input, $rules);
    
        if ($validate->fails()) {
            return returnData(2000, $validate->errors());
        }
        if(Auth::check()){
            $user_id = Auth::user()->id;
        }

        // admission request store
        $admissionReq->fill($input);
        $admissionReq->user_id = $user_id;
        $admissionReq->save();

        // previous education store
        $educations = $request->input('educations');
        if($educations){
            foreach($educations as $education){
                $prev_education = new ApplicantPreviousEducation();
                $prev_education->admission_requests_id = $admissionReq->id;
                $prev_education->user_id = $user_id;
                $prev_education->fill($education);
                $prev_education->save();
            }
        }
         // guardian info store
         $guardian_info = new ApplicantGuardian();
         $guardian_info->admission_requests_id = $admissionReq->id;
         $guardian_info->user_id = $user_id;
         $guardian_info->guardian_name = $request->guardian_name;
         $guardian_info->guardian_relation = $request->guardian_relation;
         $guardian_info->guardian_phone = $request->guardian_phone;
         $guardian_info->guardian_address = $request->guardian_address;
         $guardian_info->save();

         // present address store
         $present_address = new ApplicantPresentAddress();
         $present_address->admission_requests_id = $admissionReq->id;
         $present_address->user_id = $user_id;
         $present_address->division = $request->present_division;
         $present_address->district = $request->present_district;
         $present_address->upazila = $request->present_upazila;
         $present_address->union = $request->present_union;
         $present_address->post_office = $request->present_post_code;
         $present_address->village = $request->present_village;
         $present_address->save();

        // permanent address store
        $admission_requests_permanent_addresses = new ApplicantPermanentAddress();
        $admission_requests_permanent_addresses->user_id=$user_id;
        $admission_requests_permanent_addresses->admission_requests_id = $admissionReq->id;

        if($request->sameAsPresent_data == true){
            $admission_requests_permanent_addresses->division = $request->present_division;
            $admission_requests_permanent_addresses->district = $request->present_district;
            $admission_requests_permanent_addresses->upazila = $request->present_upazila;
            $admission_requests_permanent_addresses->union = $request->present_union;
            $admission_requests_permanent_addresses->post_office = $request->present_post_code;
            $admission_requests_permanent_addresses->village = $request->present_village;
        }
        else{
            $admission_requests_permanent_addresses->division = $request->permanent_division;
            $admission_requests_permanent_addresses->district = $request->permanent_district;
            $admission_requests_permanent_addresses->upazila = $request->permanent_upazila;
            $admission_requests_permanent_addresses->union = $request->permanent_union;
            $admission_requests_permanent_addresses->post_office = $request->permanent_post_code;
            $admission_requests_permanent_addresses->village = $request->permanent_village;
        }

        $admission_requests_permanent_addresses->save();

        return returnData(2000, 'Thank You For Admitted');
 
    }

    public function getSectionsAndDepartments($id)
    {
        $sections = Section::where('class_id', $id)->get();
        $departments = Department::where('class_id', $id)->get();

        return response()->json([
            'sections' => $sections,
            'departments' => $departments
        ]);
    }

    public function admission_data(){
        $data['classes'] = StudentClass::where('status', 1)->select('id','name')->get();
        $data['sections'] = Section::where('status', 1)->select('id','name')->get();
        $data['departments'] = Department::where('status', 1)->select('id','department_name')->get();
        $data['sessions'] = SessionYear::where('status', 1)->select('id','title')->get();
        $data['divisions'] = DB::table('divisions')->select('id','name','bn_name')->get();
        $data['auth_user'] = Auth::user();
        return returnData(2000, $data);
    }

    public function admission_get_location($type, $id){
        if($type=='divisions') {
            $data = DB::table('districts')->where('division_id','=', $id) ->select('id','division_id', 'name', 'bn_name')
            ->get();
            // dd(collect($data));
        }
        if($type=='union') {
            $data = DB::table('unions')->where('upazilla_id','=', $id) ->select('id','upazilla_id', 'name', 'bn_name')
            ->get();
            // dd(collect($data));
        }
        
        if($type=='upazila') {
            $data = DB::table('upazilas')->where('district_id', $id)->select('id', 'name', 'bn_name')->get();
        }
        if($type=='union') {
            $data = DB::table('unions')->where('upazilla_id', $id)->select('id', 'name', 'bn_name')->get();
        }
        return returnData(2000, $data);
    }

    public function upload_prev_edu_certificate(Request $request){
        if ($request->hasFile('prev_certificate')) {
        
            $uploadedPaths = [];

            $file = $request->file('prev_certificate');
            $path = storage_path('app/public/');
            $imageName = time() . '_' . Str::random(30) . '.'.$file->getClientOriginalExtension();
            $file->move($path, $imageName);
           
            return returnData(2000, ['uploaded_file_name' => $imageName]);
        } else {
            return returnData(3000, null, 'No images found.');
        }
    }

    public function admission_upload_profile_photo(Request $request)
    {
        $request->validate([
            'profile_photo' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);
        if ($request->hasFile('profile_photo')) {
            $file = $request->file('profile_photo');
            $path = storage_path('app/public/admission/');
            $imageName = time() . '_' . Str::random(30) . '.' . $file->getClientOriginalExtension();
            $file->move($path, $imageName);
    
            return response()->json([
                'status' => 2000,
                'result' => ['uploaded_file_name' => $imageName]
            ]);
        } else {
            return response()->json([
                'status' => 3000,
                'message' => 'No image found.'
            ]);
        }
    }

    public function admission_upload_birth_nid_certificate(Request $request)
    {
        $request->validate([
            'birth_nid_certificate' => 'required|image|mimes:jpeg,png,jpg,pdf|max:2048',
        ]);
        if ($request->hasFile('birth_nid_certificate')) {
            $file = $request->file('birth_nid_certificate');
            $path = storage_path('app/public/admission/');
            $imageName = time() . '_' . Str::random(30) . '.' . $file->getClientOriginalExtension();
            $file->move($path, $imageName);
    
            return response()->json([
                'status' => 2000,
                'result' => ['uploaded_nid_birth' => $imageName]
            ]);
        } else {
            return response()->json([
                'status' => 3000,
                'message' => 'No file found.'
            ]);
        }
    }
    

     
}

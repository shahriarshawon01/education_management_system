<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\School;
use App\Helpers\Helper;
use App\Models\Address;
use App\Models\Student;
use App\Models\Guardian;
use Illuminate\Http\Request;
use App\Models\AdmissionRequest;
use Illuminate\Support\Facades\DB;
use App\Exports\ApplicantDataExport;
use App\Models\StudentQualification;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\AdmissionPaymentStatus;
use App\Models\ApplicantPreviousEducation;
use App\Exports\ApplicantSortListDataExport;
use App\Exports\ApplicantFinalListDataExport;
use App\Exports\ApplicantMeritListDataExport;
use App\Exports\ApplicantWaitingListDataExport;
use App\Models\admission_requests_exam_mark_entry;
use App\Models\admission_subjectwise_marks_mapping;
use App\Models\Profile;

class ApplicantRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
     use Helper;

    public function __construct()
    {
        $this->model = new AdmissionRequest();
    }

    public function custom_short_list_status_change($id){
        $data = $this->model::where('id',$id)->first();
         if($data){
             $data->short_list = 1;
             $data->update();
             return returnData(2000, $data);
         }
         return returnData(5000, $data);
     }

     public function custom_preliminary_status($id){
        $data = $this->model::find($id);
         if($data){
             $data->short_list = 2;
             $data->update();
             return returnData(2000, $data);
         }
         return returnData(5000, $data);
     }
     public function custom_reject_status($id){
        $data = $this->model::find($id);
         if($data){
             $data->short_list = 3;
             $data->update();
             return returnData(2000, $data);
         }
         return returnData(5000, $data);
     }
     public function multiple_selected_status_changes($type,$id){
        $items = explode(',', $id);
        foreach($items as $item){
           $admission_status = $this->model::find($item);
           if($type === 'short_listed'){
              $admission_status->short_list = 1;
           }else if($type === 'preliminary_enroll'){
            $admission_status->short_list = 2;
           }else if($type === 'reject'){
            $admission_status->short_list = 3;
           }
           $admission_status->update();
        }

        return returnData(2000, null, 'Successfully Updated');
    }

    public function index()
    { 
        try {
            $data = $this->model
             ->select('admission_requests.*')
            ->where('admission_requests.status',1)
            ->with('class_info','session','section','department','previous_result')
            ->when(input('keyword'), function ($query) {
                $keyword = input('keyword');
                $query->where('applicant_id', 'Like', "%$keyword%");
            })

            ->when((input('class_id') || input('class_id')), function($query){
                $query->whereHas('class_info', function($query){
                    if(input('class_id')){
                        $query->where('class_id', input('class_id'));
                    }
                    if(input('session_id')){
                        $query->where('session_id', input('session_id'));
                    }
                    if(input('section_id')){
                        $query->where('section_id', input('section_id'));
                    }
                    if(input('department_id')){
                        $query->where('department_id', input('department_id'));
                    }
                });
            })
            ->where(function ($query) {
                $genders = [];
                if(input('g_Male')) {
                    $genders[] = 'Male';
                }
                if (input('g_Female')) {
                    $genders[] = 'Female';
                }
                if (input('g_Others')) {
                    $genders[] = 'Others';
                }
                if(count($genders) > 0) {
                    $query->whereIn('gender', $genders);
                }
            })
            ->where(function ($query) {
                $religions = [];
                if(input('r_Islam')) {
                    $religions[] = 'Islam';
                }
                if (input('r_Hinduism')) {
                    $religions[] = 'Hinduism';
                }
                if (input('r_Christianity')) {
                    $religions[] = 'Christianity';
                }
                if (input('r_Buddhism')) {
                    $religions[] = 'Buddhism';
                }
                if (input('r_Judaism')) {
                    $religions[] = 'Judaism';
                }
                if (input('r_Sikhism')) {
                    $religions[] = 'Sikhism';
                }
                if (input('r_Others')) {
                    $religions[] = 'Others';
                }
                if(count($religions) > 0) {
                    $query->whereIn('religion', $religions);
                }
            })

            ->where(function ($query) {
                $quotas = [];
                if(input('q_Freedom_Fighter')) {
                    $quotas[] = 'Freedom Fighter';
                }
                if (input('q_Physically_Challenged')) {
                    $quotas[] = 'Physically Challenged';
                }
                if (input('q_Tribe')) {
                    $quotas[] = 'Tribe';
                }
                if (input('q_None_Quota')) {
                    $quotas[] = 'None Quota';
                }
                if(count($quotas) > 0) {
                    $query->whereIn('quota', $quotas);
                }
            })
            ->when(input('gpa') || input('gpa_direction'), function($query) {
                $gpa = input('gpa');
                $direction = input('gpa_direction');

                $query->whereHas('previous_result', function($query) use ($gpa, $direction) {
                    if ($gpa) {
                        $query->where('gpa', $gpa);
                    }
                    if ($direction == 1) {
                        $query->orWhere('gpa', '>=', $gpa);
                    } elseif ($direction == 2) {
                        $query->orWhere('gpa', '<=', $gpa);
                    }
                });
            })
            ->orderBy('id','DESC')
            ->paginate(request()->input('perPage'));
           
            return returnData(2000, $data);

        } catch (\Exception $exception) {
            return returnData(5000, $exception->getMessage(), $exception->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    function view_application_details_backend($id){
            $admission_requests = $this->model::where(function ($query) use($id)  {
                $query->where('id', '=', $id);
            })
                ->with('class_info','session','section', 'department','previous_result','guardian','present_address','permanent_address')
                ->first();
            
            $school_id=$admission_requests->school_id;
    
        $data['school']=collect(School::
            first()->toArray());
    
            return view('frontend.pages.admission.application_details',compact('admission_requests','data'));
        }

        public function applicantExportExcel(){
            $data = AdmissionRequest::
            with('class_info','session','section', 'department','guardian','present_address')
            ->orderBy('id','ASC')->get();
            $export = new ApplicantDataExport($data);
            return Excel::download($export, 'applicant_list.xlsx');
        }

        public function admission_approved(Request $request){
           
            try {
                $data = $this->model::where('short_list',1)
                    ->with('class_info','session', 'section','department','previous_result')
                    ->when(input('keyword'), function ($query) {
                        $keyword = input('keyword');
                        $query->where('student_id', 'Like', "%$keyword%");
                    })
                    ->when((input('class_id') || input('class_id')), function($query){
                        $query->whereHas('class_info', function($query){
                            if(input('class_id')){
                                $query->where('class_id', input('class_id'));
                            }
                            if(input('session_id')){
                                $query->where('session_id', input('session_id'));
                            }
                            if(input('section_id')){
                                $query->where('section_id', input('section_id'));
                            }
                            if(input('department_id')){
                                $query->where('department_id', input('department_id'));
                            }
                        });
                    })
                    ->orderBy('id','DESC')
                    ->paginate(request()->input('perPage'));
    
                return returnData(2000, $data);
    
            } catch (\Exception $exception) {
                return returnData(5000, $exception->getMessage(), $exception->getMessage());
            }
    
        }

        public function remove_from_shortlist($id){
            $data = $this->model::find($id);
             if($data){
                 $data->short_list = 0;
                 $data->final_enroll_status = NULL;
                 $data->update();
                 return returnData(2000, $data);
             }
             return returnData(5000, $data);
         }

        public function multiple_selected_delete_status($id){

            $items = explode(',', $id);
           if(is_array($items)){
            foreach($items as $item){
                $admission_status = $this->model::find($item);
                 $admission_status->short_list = 0;
                $admission_status->update();
             }
           }else{
                $admission_status = $this->model::find($id);
                $admission_status->short_list = 0;
                $admission_status->update();
           }
            return returnData(2000, null, 'Successfully Removed');
        }

        public function applicantSortListExportExcel(){
            $data = AdmissionRequest::where('short_list',1)
            ->with('class_info','session','section', 'department','guardian','present_address')
            ->orderBy('id','ASC')->get();
            $export = new ApplicantSortListDataExport($data);
            return Excel::download($export, 'applicant_Sort_list.xlsx');
        }

        public function applicantFinalExportExcel(){
            $data = AdmissionRequest::where('final_enroll_status',1)
            ->with('class_info','session','section', 'department','guardian','present_address')
            ->orderBy('id','ASC')->get();
            $export = new ApplicantFinalListDataExport($data);
            return Excel::download($export, 'applicant_final_list.xlsx');
        }
        public function applicantMeritExportExcel(){
            $data = AdmissionRequest::where('merit_waiting_status',1)
            ->with('class_info','session','section', 'department','guardian','present_address')
            ->orderBy('id','ASC')->get();
            $export = new ApplicantMeritListDataExport($data);
            return Excel::download($export, 'applicant_merit_list.xlsx');
        }
        public function applicantWaitingExportExcel(){
            $data = AdmissionRequest::where('merit_waiting_status',2)
            ->with('class_info','session','section', 'department','guardian','present_address')
            ->orderBy('id','ASC')->get();
            $export = new ApplicantWaitingListDataExport($data);
            return Excel::download($export, 'applicant_waiting_list.xlsx');
        }

        public function custom_merit_status($id){
            $data = AdmissionRequest::find($id);
             if($data){
                 $data->merit_waiting_status = 0;
                 $data->short_list = 0;
                 $data->final_enroll_status = NULL;
                 admission_subjectwise_marks_mapping::where('applicant_id',$data->applicant_id)->delete();
                 admission_requests_exam_mark_entry::where('applicant_id',$data->applicant_id)->delete();
                 AdmissionPaymentStatus::where('applicant_id',$data->applicant_id)->delete();
                 $data->update();
                 return returnData(2000, $data);
             }
             return returnData(5000, $data);
         }

         public function studentStoreFromAdmission(Request $request){
            $admission_request = AdmissionRequest::
            with(['previous_result','guardian','present_address','permanent_address'])
            ->find($request->admission_id);

            if($admission_request){
                try {
                    DB::beginTransaction();
                   
                    $admission_request->final_enroll_status = 1;
                    $admission_request->admission_payment_status = 1;
                    $admission_request->save();
                   
                    $school_id = auth()->user()->school_id;
                    $user = User::where('id',$admission_request->user_id)->first();
                    $user->role_id = 7;
                    $user->type = 7;
                    $user->date_of_birth = $admission_request->date_of_birth ?? '';
                    $user->phone = $admission_request->applicant_phone ?? '';
                    $user->save();

                    foreach ($request->fees as $finalFees) {
                        if ($finalFees['checked'] == '1') {
                            // Check if the record already exists
                            $existingRecord = AdmissionPaymentStatus::where('user_id', $user->id)
                                ->where('applicant_id', $admission_request->applicant_id)
                                ->where('fees_type_id', $finalFees['id'])
                                ->where('fees_master_id', $finalFees['fess_master_id'])
                                ->where('class_id', $admission_request->class_id)
                                ->where('session_year_id', $admission_request->session_id)
                                ->where('section_id', $admission_request->section_id)
                                ->where('department_id', $admission_request->department_id)
                                ->first();
                    
                            if ($existingRecord) {
                                // Update the existing record
                                $existingRecord->total_amount = $finalFees['amount'];
                                $existingRecord->status = 1;
                                $existingRecord->save();
                            } else {
                                // Create a new record
                                $AdmissionPaymentStatus = new AdmissionPaymentStatus();
                                $AdmissionPaymentStatus->user_id = $user->id;
                                $AdmissionPaymentStatus->applicant_id = $admission_request->applicant_id;
                                $AdmissionPaymentStatus->school_id = $school_id;
                                $AdmissionPaymentStatus->fees_type_id = $finalFees['id'];
                                $AdmissionPaymentStatus->fees_master_id = $finalFees['fess_master_id'];
                                $AdmissionPaymentStatus->class_id = $admission_request->class_id;
                                $AdmissionPaymentStatus->session_year_id = $admission_request->session_id;
                                $AdmissionPaymentStatus->section_id = $admission_request->section_id;
                                $AdmissionPaymentStatus->department_id = $admission_request->department_id;
                                $AdmissionPaymentStatus->total_amount = $finalFees['amount'];
                                $AdmissionPaymentStatus->status = 1;
                                $AdmissionPaymentStatus->save();
                            }
                        }
                    }
                    
                    $student = new Student();
                    $student->fill([
                            'student_name_bn' => $admission_request->applicant_name_en,
                            'student_name_en' => $admission_request->applicant_name_bn,
                            'father_name_bn' => $admission_request->father_name_bn,
                            'father_name_en' => $admission_request->father_name_en,
                            'father_phone' => $admission_request->father_phone,
                            'mother_name_bn' => $admission_request->mother_name_bn,
                            'mother_name_en' => $admission_request->mother_name_en,
                            'mother_phone' => $admission_request->mother_phone,
                            'school_id' => $school_id,
                            'user_id' => $user->id,
                            'class_id' => $admission_request->class_id,
                            'session_year_id' => $admission_request->session_id,
                            'section_id' => $admission_request->section_id,
                            'department_id' => $admission_request->department_id,
                            'nationality' => $admission_request->nationality,
                            'date_of_birth' => $admission_request->date_of_birth,
                            'admission_date' => Carbon::now(),
                            'blood_group' => $admission_request->blood_group,
                            'height' => $admission_request->height,
                            'weight' => $admission_request->weight,
                            'father_profession' => $admission_request->father_profession,
                            'mother_profession' => $admission_request->mother_profession,
                            'yearly_income' => $admission_request->yearly_income,
                            'status' => $admission_request->status,
                        ]);
                        //gender entry
                        if($admission_request->gender == 'Male'){
                            $student->gender = 1;
                        }elseif($admission_request->gender == 'Female'){
                            $student->gender = 2;
                        }else{
                            $student->gender = 3;
                        }
                        //religion entry
                        if($admission_request->religion == 'Islam'){
                            $student->religion = 1;
                        }elseif($admission_request->religion == 'Hinduism'){
                            $student->religion = 2;
                        }elseif($admission_request->religion == 'Christianity'){
                            $student->religion = 3;
                        }elseif($admission_request->religion == 'Buddhism'){
                            $student->religion = 4;
                        }else{
                            $student->gender = 5;
                        }
                        
                        $student->save();

                         // Qualification entry
                         $qualifications = $admission_request->previous_result() ->get()->makeHidden(['admission_requests_id','user_id'])->toArray();
                        
                        $studentId = $student->id;
                        foreach ($qualifications as $qualification) {
                            $student_q = new StudentQualification();
                            $student_q->fill([
                                'student_id' => $studentId,
                                'roll_number' => $qualification['roll_no'],
                                'reg_number' => $qualification['reg_no'],
                                'exam_name' => $qualification['exam_name'],
                                'board_name' => $qualification['board_name'],
                                'group' => $qualification['group'],
                                'passing_year' => $qualification['passing_year'],
                                'gpa' => $qualification['gpa'],
                            ]);
                            $student_q->school_id = $school_id;
                            $student_q->student_id = $studentId;
                            $student_q->save();
                        }
                         // Guardian entry
                        $guardians = $admission_request->guardian()->get()->makeHidden(['admission_requests_id','user_id'])->toArray();
                        foreach ($guardians as $guardian) {
                            $guardianData = new Guardian();
                            $guardianData->fill($guardian);
                            $guardianData->relation = $guardian['guardian_relation'];
                            $guardianData->student_id = $studentId;
                            $guardianData->school_id = $school_id;
                            $guardianData->save();
                        }
                        // present address entry
                        $present_addresses = $admission_request->present_address()->get()->makeHidden(['admission_requests_id','user_id'])->toArray();
                        foreach ($present_addresses as $address) {
                            $addressData = new Address();
                            $addressData->fill($address);
                            $addressData->student_id = $studentId;
                            $addressData->school_id = $school_id;
                            $addressData->type = 1;
                            $addressData->save();
                        }
                        // permanent address entry
                        $permanent_addresses = $admission_request->permanent_address()->get()->makeHidden(['admission_requests_id','user_id'])->toArray();
                        foreach ($permanent_addresses as $address) {
                            $addressData = new Address();
                            $addressData->fill($address);
                            $addressData->student_id = $studentId;
                            $addressData->school_id = $school_id;
                            $addressData->type = 2;
                            $addressData->save();
                        }
    
                    DB::commit();
    
                    return returnData(2000, null, 'Student Successfully Admitted');
    
                } catch (\Exception $exception) {
                    return returnData(5000, $exception->getMessage(), $exception->getMessage());
                }
    
            }
         }

         function view_application_payment_details($id){
        
            $admission_requests = AdmissionRequest::where(function ($query) use($id)  {
                $query->where('id', '=', $id);
            })
                ->with(['payments','class_info','section','department','session'])
                ->first();
                $data['school']=collect(School::first()->toArray());
           
                return view('frontend.pages.admission.applicant_payment_details',compact('admission_requests','data'));
            }

}

<?php

namespace App\Http\Controllers;

use exam;
use App\Helpers\Helper;
use App\Models\Classes;
use Illuminate\Http\Request;
use App\Models\AdmissionRequest;
use App\Http\Controllers\Controller;
use App\Models\Admission\admission_requests;
use App\Models\admission_subjectwise_marks_mapping;
// use App\Models\Admission\admission_requests_communication;
use App\Models\admission_requests_exam_mark_entry;

class PreliminaryController extends Controller
{

    use Helper;

    public function __construct()
    {
        // $this->model = new admission_requests();
        $this->model = new AdmissionRequest();
        // $this->childModel = new admission_requests_communication();
    }

    public function custom_status_change($id){
    //    $data = admission_requests::find($id);
       $data = $this->model::find($id);
        if($data){
            $data->short_list = 1;
            $data->update();
            return returnData(2000, $data);
        }
        return returnData(5000, $data);
    }
    public function custom_preliminary_status($id){
        // $data = admission_requests::find($id);
        $data = $this->model::find($id);
         if($data){
             $data->short_list = 2;
             $data->update();
             return returnData(2000, $data);
         }
         return returnData(5000, $data);
     }
     public function custom_reject_status($id){
        // $data = admission_requests::find($id);
        $data = $this->model::find($id);
         if($data){
             $data->short_list= 3;
             $data->update();
             return returnData(2000, $data);
         }
         return returnData(5000, $data);
     }

     public function custom_remove_status($id){
        // $data = admission_requests::find($id);
        $data = $this->model::find($id);
         if($data){
             $data->short_list = 0;
             $data->final_enroll_status = NULL;
             $data->update();
             return returnData(2000, $data);
         }
         return returnData(5000, $data);
     }

    public function index()
    {
        try {
//            admission request
            $data = $this->model
            ->join('admission_requests_previous_results','admission_requests.id','admission_requests_previous_results.admission_request_id')
            // ->where('admission_requests.school_id',auth()->user()->school_id)
//                ->where('payment_status',1)
//                ->where('admit_print_status',1)
            ->with('class_info','shift','depertment','session','group','previous_result','payment','admission_requests_communications')

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
                    if(input('group_id')){
                        $query->where('group_id', input('group_id'));
                    }
                });
            })

            ->orderBy('admission_requests_previous_results.gpa','DESC')->paginate(request()->input('perPage'));
                // $admission_communication_data = $this->model
                // ->join('admission_requests_communications','admission_requests.id','admission_requests_communications.admission_request_id')
                // ->where('admission_requests.school_id',auth()->user()->school_id)
                // ->select('admission_requests_communications.father_name')->first();

                // $data('father_name') = trim($admission_communication_data->father_name);

            return returnData(2000, $data);

        } catch (\Exception $exception) {
            return returnData(5000, $exception->getMessage(), $exception->getMessage());
        }
    }

    public function preliminary_approved(Request $request){
        try {
            $data = $this->model
               ->leftJoin('admission_requests_exam_mark_entries', 'admission_requests.applicant_id', '=','admission_requests_exam_mark_entries.applicant_id')
            //    ->leftJoin('fees_masters','admission_requests.class_id','=','fees_masters.class_id')
            //    ->where('admission_requests.school_id', auth()->user()->school_id)
                ->where(function ($query) {
                    $query->whereNotNull('admission_requests_exam_mark_entries.mark')
                    ->Where('admission_requests.short_list',1);
                })
                ->where('mark', '>', 33)
                ->whereNotNull('admission_requests_exam_mark_entries.mark')
                // ->orWhere('admission_requests.short_list_status',2)

            ->select('admission_requests.*', 'admission_requests_exam_mark_entries.mark')
            ->with('class_info', 'department', 'session', 'section', 'previous_result')

            ->when((input('class_id') || input('session_id')), function($query){
                $query->whereHas('class_info', function($query){
                    if(input('class_id')){
                        $query->where('admission_requests.class_id', input('class_id'));
                    }
                    if(input('session_id')){
                        $query->where('admission_requests.session_id', input('session_id'));
                    }
                    if(input('section_id')){
                        $query->where('admission_requests.section_id', input('section_id'));
                    }
                    if(input('department_id')){
                        $query->where('admission_requests.department_id', input('department_id'));
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

            ->when(input('mark') || input('mark_direction'), function($query) {
                $mark = input('mark');
                $direction = input('mark_direction');

                $query->whereHas('previous_result', function($query) use ($mark, $direction) {
                    if ($mark) {
                        $query->where('mark', $mark);
                    }

                    if ($direction == 1) {
                        $query->orWhere('mark', '>=', $mark);
                    } elseif ($direction == 2) {
                        $query->orWhere('mark', '<=', $mark);
                    }
                });
            })

            ->orderBy('admission_requests_exam_mark_entries.mark','DESC')
            ->paginate(request()->input('perPage'));
           return returnData(2000, $data);

        } catch (\Exception $exception) {
            return returnData(5000, $exception->getMessage(), $exception->getMessage());
        }
    }

    public function merit_index(Request $request){

        try {
            $data = $this->model
               ->leftJoin('admission_requests_exam_mark_entries', 'admission_requests.applicant_id', '=','admission_requests_exam_mark_entries.applicant_id')
            //    ->leftJoin('fees_masters','admission_requests.class_id','=','fees_masters.class_id')
                // ->where('admission_requests.school_id', auth()->user()->school_id)
                ->where(function ($query) {
                $query->whereNotNull('admission_requests_exam_mark_entries.mark')
                // ->Where('admission_requests.short_list_status',1);
                ->Where('merit_waiting_status', '1');
                })
                ->whereNotNull('admission_requests_exam_mark_entries.mark')
                // ->orWhere('admission_requests.short_list_status',2)
                ->where('mark', '>', 33)

            ->select('admission_requests.*', 'admission_requests_exam_mark_entries.mark')
            ->with('class_info', 'section', 'department', 'session', 'previous_result')

            ->when((input('class_id') || input('session_id')), function($query){
                $query->whereHas('class_info', function($query){
                    if(input('class_id')){
                        $query->where('admission_requests.class_id', input('class_id'));
                    }
                    if(input('session_id')){
                        $query->where('admission_requests.session_id', input('session_id'));
                    }
                    if(input('section_id')){
                        $query->where('admission_requests.section_id', input('section_id'));
                    }
                    if(input('department_id')){
                        $query->where('admission_requests.department_id', input('department_id'));
                    }
                });
            })
            ->orderBy('admission_requests_exam_mark_entries.mark','DESC')
            ->paginate(request()->input('perPage'));

           return returnData(2000, $data);

        } catch (\Exception $exception) {
            return returnData(5000, $exception->getMessage(), $exception->getMessage());
        }
    }

    public function waiting_index(Request $request){

        try {
            $data = $this->model
               ->leftJoin('admission_requests_exam_mark_entries', 'admission_requests.applicant_id', '=','admission_requests_exam_mark_entries.applicant_id')
            //    ->leftJoin('fees_masters','admission_requests.class_id','=','fees_masters.class_id')
            //    ->where('admission_requests.school_id', auth()->user()->school_id)
                ->where(function ($query) {
                $query->whereNotNull('admission_requests_exam_mark_entries.mark')
                // ->Where('admission_requests.short_list_status',1);
                ->Where('merit_waiting_status', '2');
                })
                ->whereNotNull('admission_requests_exam_mark_entries.mark')
                // ->orWhere('admission_requests.short_list_status',2)
                ->where('mark', '>', 33)

            ->select('admission_requests.*', 'admission_requests_exam_mark_entries.mark')
            ->with('class_info', 'department', 'session', 'section', 'previous_result')

            ->when((input('class_id') || input('session_id')), function($query){
                $query->whereHas('class_info', function($query){
                    if(input('class_id')){
                        $query->where('admission_requests.class_id', input('class_id'));
                    }
                    if(input('session_id')){
                        $query->where('admission_requests.session_id', input('session_id'));
                    }
                    if(input('section_id')){
                        $query->where('admission_requests.section_id', input('section_id'));
                    }
                    if(input('department_id')){
                        $query->where('admission_requests.department_id', input('department_id'));
                    }
                });
            })
            ->orderBy('admission_requests_exam_mark_entries.mark','DESC')
            ->paginate(request()->input('perPage'));

           return returnData(2000, $data);

        } catch (\Exception $exception) {
            return returnData(5000, $exception->getMessage(), $exception->getMessage());
        }
    }

//    public function prilimaniry_result(Request $request) {
//     $query = admission_requests_exam_mark_entry::leftJoin('admission_subjectwise_marks_mappings', 'admission_requests_exam_mark_entries.id', 'admission_subjectwise_marks_mappings.marks_entry_id')
//         ->join('admission_requests', 'admission_requests_exam_mark_entries.applicant_id', 'admission_requests.applicant_id')
//         ->select('admission_requests.name', 'admission_requests.birthday', 'admission_requests_exam_mark_entries.applicant_id', 'admission_subjectwise_marks_mappings.subject_marks', 'admission_requests_exam_mark_entries.mark')
//         ->groupBy('admission_requests_exam_mark_entries.applicant_id');

//     $keyword = $request->input('keyword');

//     if ($keyword) {
//         $query->where('admission_requests_exam_mark_entries.applicant_id', $keyword);
//     }

//     $data['data_info'] = $query->get();

//     return returnData(2000, $data);
// }

public function prilimaniry_result(Request $request){
    
    $data['data_info'] = admission_requests_exam_mark_entry::join('admission_requests', 'admission_requests_exam_mark_entries.applicant_id', '=', 'admission_requests.applicant_id')
    ->join('schools', 'admission_requests_exam_mark_entries.school_id', '=', 'schools.id')
    ->join('session_years', 'admission_requests_exam_mark_entries.session_year_id', '=', 'session_years.id')
    ->select('schools.*', 'admission_requests.*', 'admission_requests_exam_mark_entries.mark','session_years.title as session_info')
    ->with(['class_info','subjectwise.subjectname', 'section','department'])
    ->when(input('applicant_id'), function ($query) {
        $query->where('admission_requests_exam_mark_entries.applicant_id', input('applicant_id'));
    })->get();

// return response()->json($data['data_info']);
    return returnData(2000, $data);
}

    public function create()
    {
        return view('admission_request');
    }


    public function store(Request $request)
    {

        $input = $request->all();
        Contact::create($input);

        return redirect('admission_request')->with('flash_message', 'Contact Addedd!');
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        //
    }


    public function update(Request $request, $id)
    {
        // dd($request->all(),$id);
        if (!can('admission_update')) {
            return $this->notPermitted();
        }

        try {
            $validate = $this->model->validate($request->all());

            if($validate->fails()){
                return returnData(3000, $validate->errors(), 'Validation Failed');
            }

            DB::beginTransaction();

            $data = $this->model->where('id', $request->input('id'))->first();

            if($data){
                $data->fill($request->all());
                $data->save();

                DB::commit();

                return returnData(2000, null, 'Admission Updated');
            }

            return returnData(5000, null, 'Admission Information Not found');

        } catch (\Exception $exception) {
            return returnData(5000, $exception->getMessage(), $exception->getMessage());
        }
    }

    // public function destroy($id)
    // {

    //     $data = $this->model->where('id', $id)->first();

    //     if($data){
    //         $data->delete();

    //         return returnData(2000, null, 'Successfully Deleted');
    //     }

    //     return returnData(2000, [], 'Data not found');
    // }


public function destroy($id)
   {
    $data = $this->model->where('id', $id)->first();
    admission_requests_exam_mark_entry::where('applicant_id',$data->applicant_id)->delete();
    if ($data) {
        // Update payment_status to 0
        $data->short_list = 0;
        $data->save();

        return returnData(2000, null, 'Remove Data Status');
    }

    return returnData(2000, [], 'Data not found');
  }


    public function custom_status($type,$id){
        // dd($type,$id);
        $data = $this->model->where('id', $id)->first();
        if($type=='short_list'){
            $data->update([
            'short_list'=>1
            ]);

            return returnData(2000, null, 'Successfully Updated');
        }
        // elseif($type=='admit_print_status'){
        //     $data->update([
        //     'admit_print_status'=>1
        //     ]);

        //     return returnData(2000, null, 'Successfully Updated');
        // }
        return returnData(2000, [], 'Not Updated');
    }

    public function multipleMeritWaitingChanges($type,$id){
        // dd($type);
        $data = $this->model->where('id', $id)->first();

        $items = explode(',', $id);
        // dd($items);
        foreach($items as $item){
           $admission_status = $this->model::find($item);

        //    dd($admission_status);
           if($type === 'merit_list'){
              $admission_status->merit_waiting_status = 1;
           }else if($type === 'waiting_list'){
            $admission_status->merit_waiting_status = 2;
           }else if($type === 'delete_list'){
            $admission_status->final_enroll_status = NULL;
            $admission_status->short_list = 0;
            $admission_status->merit_waiting_status = 0;
            admission_subjectwise_marks_mapping::where('applicant_id',$admission_status->applicant_id)->delete();
            admission_requests_exam_mark_entry::where('applicant_id',$admission_status->applicant_id)->delete();
           }
           $admission_status->update();
        }
        return returnData(2000, null, 'Successfully Updated');
    }

    public function multipleWaitingChanges($type,$id){
        // $data = $this->model->where('id', $id)->first();
        $items = explode(',', $id);
        // dd($items);
        foreach($items as $item){
           $admission_status = $this->model::find($item);
        //    dd($admission_status);
           if($type === 'merit_list'){
              $admission_status->merit_waiting_status = 1;
           }else if($type === 'delete_list'){
               $admission_status->short_list = 0;
               $admission_status->merit_waiting_status = 0;
               $admission_status->final_enroll_status = NULL;
               admission_subjectwise_marks_mapping::where('applicant_id',$admission_status->applicant_id)->delete();
               admission_requests_exam_mark_entry::where('applicant_id',$admission_status->applicant_id)->delete();
           }
           $admission_status->update();
        //    if($data){
        //     $data->delete;
        //    }
        }
        return returnData(2000, null, 'Successfully Updated');
    }

}
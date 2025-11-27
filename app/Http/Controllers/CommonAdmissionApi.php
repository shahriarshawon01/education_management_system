<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Helpers\DatabaseSupport;
use App\Http\Controllers\Controller;
use App\Models\Admission\admission_requests;
use App\Models\admission_requests_exam_mark_entry;
use App\Models\admission_subjectwise_marks_mapping;
use App\Models\AdmissionExam;
use App\Models\AdmissionRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class CommonAdmissionApi extends Controller
{
    use Helper;

    public function __construct()
    {
        $this->model = new AdmissionRequest();
    }

    public function index(Request $request)
    {
        $DatabaseSupport = new DatabaseSupport();
        $admission_exam = AdmissionExam::query();
    
        try {
           
            if (!$request->class_id || !$request->session_id) {
                return returnData(5000, 'Please select both class and session.', 'Class and session are required.');
            }
    
            $check_depertment_or_group = $DatabaseSupport->count_class_or_depertment($request, $admission_exam);
    
            if ($check_depertment_or_group <= 0) {
                return returnData(5000, 'Exam not created. Please create an exam first.', 'Exam not created. Please create an exam first.');
            }
    
            $data['admission_exam'] = $DatabaseSupport->select_exam($request, $admission_exam);
    
            $data_info_query = $this->model
                ->with(['class_info', 'session', 'department', 'section'])
                ->join('admission_subjects', function ($join) {
                    $join->on('admission_requests.class_id', '=', 'admission_subjects.class_id')
                        ->on('admission_requests.session_id', '=', 'admission_subjects.session_year_id')
                        ->on('admission_requests.section_id', '=', 'admission_subjects.section_id')
                        ->on('admission_requests.department_id', '=', 'admission_subjects.department_id');
                })
                ->leftJoin('admission_selection_subject_mappings', 'admission_subjects.id', '=', 'admission_selection_subject_mappings.admission_subject_id')
                ->leftJoin('admission_subjectwise_marks_mappings', function ($join) {
                    $join->on('admission_subjectwise_marks_mappings.applicant_id', '=', 'admission_requests.applicant_id')
                        ->on('admission_subjectwise_marks_mappings.subject_id', '=', 'admission_selection_subject_mappings.id');
                })
                ->leftJoin('admission_requests_exam_mark_entries', 'admission_subjectwise_marks_mappings.marks_entry_id', '=', 'admission_requests_exam_mark_entries.id')
                ->selectRaw("
                    admission_subjects.id as exam_id,
                    admission_requests.class_id,
                    admission_selection_subject_mappings.subject_name,
                    admission_selection_subject_mappings.marks,
                    admission_selection_subject_mappings.id as subject_id,
                    admission_requests.section_id,
                    admission_requests.department_id,
                    admission_requests.session_id,
                    admission_requests.id,
                    admission_requests.user_id,
                    admission_requests.applicant_id,
                    admission_requests.applicant_name_en,
                    COALESCE(admission_subjectwise_marks_mappings.subject_marks, admission_selection_subject_mappings.marks) as subject_marks
                ")
                ->where('admission_requests.short_list', 1)
                ->where('admission_requests.class_id', $request->class_id)
                ->where('admission_requests.session_id', $request->session_id);
    
            if ($request->section_id) {
                $data_info_query->where('admission_requests.section_id', $request->section_id);
            }
    
            if ($request->department_id) {
                $data_info_query->where('admission_requests.department_id', $request->department_id);
            }
    
            if ($request->applicant_id) {
                $data_info_query->where('admission_requests.applicant_id', $request->applicant_id);
            }
    
            $data['data_info'] = $data_info_query->get();
    
            if ($data['data_info']->isEmpty()) {
                return returnData(5000, 'No applicants found with the provided criteria.', 'No applicants found with the provided criteria.');
            }
    
            $data['data_info'] = $data_info_query->paginate(request()->input('perPage'));
    
            return returnData(2000, $data);
    
        } catch (\Exception $exception) {
          
            return returnData(5000, 'An error occurred while fetching the data.', $exception->getMessage());
        }
    }
    
    public function store_admission_mark(Request $request)
    {
        $DatabaseSupport = new DatabaseSupport;
        $admission_exam = AdmissionExam::query();
        $check_depertment_or_group = $DatabaseSupport->count_class_or_depertment($request, $admission_exam);
        $markData = $request->all();

            foreach ($markData as $eachMarks) {
                // dd($eachMarks);
                $total = 0;
                $subjectWiseMarks = [];
                foreach($eachMarks['subjectWiseData'] as $mark) {
                    $subjectWiseMarks[] = [
                        'applicant_id' => $mark['applicant_id'],
                        'school_id' => auth()->user()->school_id,
                        'subject_id' => $mark['subject_id'],
                        'subject_marks' => $mark['subject_marks'],
                        'status' => 0,
                    ];
                    $total += (int) $mark['subject_marks'];
                }

                $select_admission_mark = admission_requests_exam_mark_entry::where('applicant_id', $eachMarks['applicant_id'])->first();

                $applicat_user = AdmissionRequest::select('user_id')->where('applicant_id',$eachMarks['applicant_id'])->first();

                if($select_admission_mark) {
                    $select_admission_mark->user_id = $applicat_user ? $applicat_user->user_id : null;
                    $select_admission_mark->mark = $total;
                    $select_admission_mark->save();
                } else{
                    $select_admission_mark = new admission_requests_exam_mark_entry;
                    $select_admission_mark->user_id = $applicat_user ? $applicat_user->user_id : null;
                    $select_admission_mark->applicant_id =  $eachMarks['applicant_id'];
                    $select_admission_mark->class_id = $eachMarks['class_id'];
                    $select_admission_mark->session_year_id = $eachMarks['session_id'];
                    $select_admission_mark->section_id = $eachMarks['section_id'];
                    $select_admission_mark->department_id = $eachMarks['department_id'];
                    $select_admission_mark->school_id = auth()->user()->school_id;
                    $select_admission_mark->mark = $total;
                    $select_admission_mark->status = 0;
                    $select_admission_mark->save();
                }
                $withRef = [];
                foreach ($subjectWiseMarks as $insertMarkenty) {
                    $insertMarkenty['marks_entry_id'] = $select_admission_mark->id;
                    $withRef[] = $insertMarkenty;
                }
                DB::table('admission_subjectwise_marks_mappings')->where('marks_entry_id', $select_admission_mark->id)->delete();
                DB::table('admission_subjectwise_marks_mappings')->insert($withRef);
        }
        return returnData(2000, null, 'Successfully Inserted');
    }

}

<?php

namespace App\Http\Controllers;

use App\Models\School;
use Illuminate\Http\Request;
use App\Models\AdmissionRequest;
use App\Models\admission_requests_exam_mark_entry;
use App\Models\AdmissionExam;

class ApplicantDashboardController extends Controller
{
    public function index()
    {
        return view('frontend.pages.admission.applicant_dashboard');
    }

    public function approved_application()
    {
        return view('frontend.pages.admission.approved_application');
    }

    public function all_application_list()
    {
        $admission_requests = AdmissionRequest::where('user_id', auth()->user()->id)->get();
        return returnData(2000, $admission_requests);
    }
    public function approved_application_list()
    {
        ini_set('max_execution_time', 120);
        $admission_requests = AdmissionRequest::join('admission_exams', 'admission_requests.class_id', 'admission_exams.class_id')->where(function ($query) {
            $query->where('admission_exams.status', 1);
        })
            ->select('admission_requests.*')
            ->where('user_id', auth()->user()->id)
            ->get();
        return returnData(2000, $admission_requests);

    }

    function view_application_details($id)
    {

        $admission_requests = AdmissionRequest::where(function ($query) use ($id) {
            $query->where('id', '=', $id);
        })
            ->with('class_info', 'session', 'section', 'department', 'previous_result', 'guardian', 'present_address', 'permanent_address')
            ->first();

        $school_id = $admission_requests->school_id;

        $data['school'] = collect(School::
            first()->toArray());

        return view('frontend.pages.admission.application_details', compact('admission_requests', 'data'));

    }

    public function view_admit_card($id)
    {

        $data['school'] = School::first();

        $admission_requests = AdmissionRequest::where(function ($query) use ($id) {
            $query->where('id', '=', $id);
        })
            ->with('class_info', 'session', 'section', 'department', 'previous_result', 'guardian', 'present_address', 'permanent_address', 'exam')->first();
        $exam_shedule = AdmissionExam::leftJoin('admission_selection_mappings', 'admission_exams.id', 'admission_selection_mappings.admission_exam_id')
            ->select('admission_selection_mappings.starting_time', 'admission_selection_mappings.ending_time')
            ->first();

        return view('frontend.pages.admission.admit_card', compact('admission_requests', 'data', 'exam_shedule'));

    }

    public function exam_result(Request $request)
    {
        $data_info = admission_requests_exam_mark_entry::join('admission_requests', 'admission_requests_exam_mark_entries.applicant_id', '=', 'admission_requests.applicant_id')
            ->join('schools', 'admission_requests_exam_mark_entries.school_id', '=', 'schools.id')
            ->select('schools.*', 'admission_requests.*', 'admission_requests_exam_mark_entries.mark')
            ->with(['class_info', 'session', 'subjectwise', 'subjectname'])
            ->when(input('applicant_id'), function ($query) {
                $query->where('admission_requests_exam_mark_entries.applicant_id', input('applicant_id'));
            })
            ->when(auth()->check(), function ($query) {
                $query->where('admission_requests_exam_mark_entries.user_id', auth()->user()->id);
            })
            ->get();
        return view('frontend.pages.admission.exam_result', compact('data_info'));
    }

    public function payment_details()
    {
        $admission_requests = AdmissionRequest::join('admission_payment_statuses', 'admission_requests.applicant_id', '=', 'admission_payment_statuses.applicant_id')
            ->with(['payments', 'class_info', 'section', 'department', 'session'])
            ->first();
        $data['school'] = collect(School::first()->toArray());

        return view('frontend.pages.admission.applicant_dashboard_payment', compact('admission_requests', 'data'));
    }


}

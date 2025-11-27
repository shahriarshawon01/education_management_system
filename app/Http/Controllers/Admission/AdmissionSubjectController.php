<?php

namespace App\Http\Controllers\Admission;

use App\Helpers\Helper;
use App\Models\admission_selection_mapping;
use App\Models\admission_selection_subject_mapping;
use Illuminate\Http\Request;
use App\Models\AdmissionSubject;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class AdmissionSubjectController extends Controller
{
    use Helper;

    public function __construct()
    {
        $this->model = new AdmissionSubject();
    }

    public function index()
    {

        try {
            $data = $this->model::
            leftJoin('admission_selection_mappings','admission_subjects.id','admission_selection_mappings.admission_exam_id')
            ->select('admission_selection_mappings.*','admission_subjects.*')
            ->with(['class_info','section','current_sessions','selection_subject_mapping','department'])
            ->orderBy('session_year_id', 'DESC')
            ->orderBy('class_id', 'DESC')

            ->when((input('class_id') || input('class_id')), function($query){
                $query->whereHas('class_info', function($query){
                    if(input('class_id')){
                        $query->where('class_id', input('class_id'));
                    }
                    if(input('session_year_id')){
                        $query->where('session_year_id', input('session_year_id'));
                    }
                    if(input('section_id')){
                        $query->where('section_id', input('section_id'));
                    }
                    if(input('department_id')){
                        $query->where('department_id', input('department_id'));
                    }
                });
            })->paginate(request()->input('perPage'));
            return returnData(2000, $data);

        } catch (\Exception $exception) {
            return returnData(5000, $exception->getMessage(), $exception->getMessage());
        }
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {

        try {
            $validate = $this->model->validate($request->all());

            if($validate->fails()){
                return returnData(3000, $validate->errors(), 'Validation Failed');
            }

            DB::beginTransaction();

            $this->model->fill($request->all());
            $this->model->school_id = auth()->user()->school_id;
        
            $this->model->save();

            if($request->selection_subject_mapping  != null){
                foreach ($request->selection_subject_mapping as $key => $data1) {
                    $subjectMapping = new admission_selection_subject_mapping();
                    $subjectMapping->school_id = auth()->user()->school_id;
                    $subjectMapping->admission_subject_id = $this->model->id;
                    $subjectMapping->subject_name = $data1['subject_name'];
                    $subjectMapping->marks = $data1['marks'];
                    $subjectMapping->save();
                }
            }

            DB::commit();

            return returnData(2000, null, 'Successfully Inserted');

        } catch (\Exception $exception) {
            return returnData(5000, $exception->getMessage(),$exception->getFile(),$exception->getLine(), $exception->getMessage());
        }

    }

    public function show($id)
    {

    }

    public function edit($id)
    {

    }


    public function update(Request $request, $id)
    {
       
        try {
            $validate = $this->model->validateUpdate($request->all());

            if($validate->fails()){
                return returnData(3000, $validate->errors(), 'Validation Failed');
            }

            DB::beginTransaction();

            $data = $this->model->where('id', $request->input('id'))->first();
            $childModelData = admission_selection_subject_mapping::where('admission_subject_id', $data->id)->first();
            $this->model = AdmissionSubject::find($request->input('id'));

            if($data && $childModelData){
                    $data->fill($request->all());
                    $data->save();

                if(($request->selection_subject_mapping)){
                    admission_selection_subject_mapping::where('admission_subject_id',$id)->delete();
                    foreach ($request->selection_subject_mapping as $key => $info1) {
                        $subjectMapping = new admission_selection_subject_mapping();
                        $subjectMapping->school_id = auth()->user()->school_id;
                        $subjectMapping->admission_subject_id = $data->id;
                        $subjectMapping->subject_name = $info1['subject_name'];
                        $subjectMapping->marks = $info1['marks'];
                        $subjectMapping->save();
                    }
                }

                DB::commit();
                return returnData(2000, null, 'Successfully Updated');
            }
            return returnData(5000, null, 'User Information Not found');

        } catch (\Exception $exception) {
            return returnData(5000, $exception->getMessage(), $exception->getMessage());
        }
    }

    public function destroy($id)
    {
        $data = $this->model->where('id', $id)->first();

        if($data){
            $data->delete();
            admission_selection_subject_mapping::where('admission_subject_id',$id)->delete();

            return returnData(2000, null, 'Successfully Updated');
        }

        return returnData(2000, [], 'Admission Subject not found');
    }
}
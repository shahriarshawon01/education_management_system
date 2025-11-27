<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Models\admission_selection_mapping;
use App\Models\admission_selection_subject_mapping;
use Illuminate\Http\Request;
use App\Models\AdmissionExam;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class AdmissionExamController extends Controller
{
    use Helper;

    public function __construct()
    {
        $this->model = new AdmissionExam();
        $this->childModel = new admission_selection_mapping();
    }

    public function index()
    {
        if (!can('admission_exam_view')) {
            return $this->notPermitted();
        }
        try {
            $data = $this->model::
            leftJoin('admission_selection_mappings','admission_exams.id','admission_selection_mappings.admission_exam_id')
            ->select('admission_selection_mappings.*','admission_exams.*')
            ->with(['class_info','current_sessions','section','department'])
            ->orderBy('admission_exams.id', 'DESC')
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
        if (!can('admission_exam_add')) {
            return $this->notPermitted();
        }

        try {
            $validate = $this->model->validate($request->all());

            if($validate->fails()){
                return returnData(5000, $validate->errors(), 'Starting date & ending date required');
            }

            DB::beginTransaction();

            $this->model->fill($request->all());
            $this->model->school_id = auth()->user()->school_id;
            $this->model->venue =$request->venue;
            $this->model->requirements =$request->requirements;
            $this->model->save();


            $this->childModel->fill($request->all());
            $this->childModel->school_id = auth()->user()->school_id;
            $this->childModel->admission_exam_id = $this->model->id;
            $this->childModel->starting_time = $request->starting_time;
            $this->childModel->ending_time = $request->ending_time;
        
            $this->childModel->save();

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

        if (!can('admission_exam_update')) {
            return $this->notPermitted();
        }

        try {
            $validate = $this->model->validateUpdate($request->all());

            if($validate->fails()){
                return returnData(5000, $validate->errors(), 'Starting date & ending date required');
            }

            DB::beginTransaction();

            $data = $this->model->where('id', $request->input('id'))->first();
            $childModelData = admission_selection_mapping::where('admission_exam_id', $data->id)->first();
            $this->model = AdmissionExam::find($request->input('id'));

            if($data && $childModelData){
                    $data->fill($request->all());
                    $data->venue =$request->venue;
                   
                    $data->requirements =$request->requirements;
                    $data->save();

                    $childModelData->fill([
                        'school_id' => auth()->user()->school_id,
                        'admission_exam_id' => $this->model->id,
                        'starting_time' => $request->input('starting_time'),
                        'ending_time' => $request->input('ending_time'),
                    ]);
                    $childModelData->save();

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
        if (!can('admission_exam_delete')) {
            return $this->notPermitted();
        }

        $data = $this->model->where('id', $id)->first();

        if($data){
            $data->delete();
            admission_selection_mapping::where('admission_exam_id',$id)->delete();
           
            return returnData(2000, null, 'Successfully Deleted');
        }

        return returnData(2000, [], 'Department not found');
    }
}
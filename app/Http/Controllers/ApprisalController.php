<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Models\Apprisal;
use App\Models\ApprisalTemplate;
use App\Models\GradeNumber;
use Illuminate\Http\Request;

class ApprisalController extends Controller
{
    use Helper;
    public function __construct()
    {
        $this->model = new Apprisal();
    }

    public function index()
    {
        if (!can('appraisal_view')) {
            return $this->notPermitted();
        }
        try {
            $keyword = request()->input('keyword');
            $data = $this->model->with('student:id,student_name_en', 'teacher:id,name', 'staff:id,name', 'template:id,apprisal_for', 'student_class:id,name')
                ->when($keyword, function ($query) use ($keyword) {
                    $query->where(function($q) use ($keyword){
                        $q->orWhereHas('student', function($q) use ($keyword){
                            $q->where('student_name_en','like',"%$keyword%");
                        })
                        ->orWhereHas('template', function($q) use ($keyword){
                            $q->where('apprisal_for','like',"%$keyword%");
                        })
                        ->orWhereHas('teacher', function($q) use ($keyword){
                            $q->where('name','like',"%$keyword%");
                        })
                        ->orWhereHas('staff', function($q) use ($keyword){
                            $q->where('name','like',"%$keyword%");
                        });
                    }); 
                })
                ->latest()->paginate(input('perPage'));
            return returnData(2000, $data);
        } catch (\Exception $exception) {
            return returnData(5000, $exception->getMessage(), 'Whoops, Something Went Wrong..!!');
        }
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
       
        if (!can('appraisal_add')) {
            return $this->notPermitted();
        }
        $authID = auth()->user()->school_id;
        try {
            $input = $request->all();
            $validate = $this->model->validate($input);
            if ($validate->fails()) {
                return returnData(2000, $validate->errors());
            }
            $this->model->fill($input);
            $this->model->cgpa = round($request->input('cgpa'));
            $this->model->school_id = $authID;
            $this->model->get_mark = json_encode($request->input('get_mark'));
            $this->model->save();

            return returnData(2000, null, 'Successfully Inserted');
        } catch (\Exception $exception) {
            return returnData(5000, $exception->getMessage(), 'Whoops, Something Went Wrong..!!');
        }
    }


    public function show($id)
    {
        $data = $this->model->with('student:id,student_name_en', 'teacher:id,name', 'staff:id,name', 'template')
            ->where('id', $id)->first();
        
        if ($data->template) {
            $data->template->kra = json_decode($data->template->kra, associative: true);
            $data->template->wheightage = json_decode($data->template->wheightage, true);
        }

        if (!empty($data->get_mark)) {
            $data->get_mark = json_decode($data->get_mark, true);
        }
    
        return returnData(2000, $data);
    }
    

    public function edit($id)
    {

        $apprisalData = Apprisal::where('id', $id)->first();
        if (is_null($apprisalData->get_mark)) {
            return returnData(5000,null, 'Mark  Not Found');
        }
    
        $data = ApprisalTemplate::where('id', $apprisalData->template_id)->first();
        
        $mergedData = [];
        
        if ($data) {
            $kraData = json_decode($data->kra, true);
            $weightageData = json_decode($data->wheightage, true);
            $getMarkData = !empty($apprisalData->get_mark) ? json_decode($apprisalData->get_mark, true) : [];
    
            if (is_array($kraData) && is_array($weightageData)) {
                $count = min(count($kraData), count($weightageData));
                for ($i = 0; $i < $count; $i++) {
                    $mergedData[] = [
                        'kra' => $kraData[$i],
                        'weightage' => $weightageData[$i],
                        'get_mark' => $getMarkData[$i] ?? '', 
                    ];
                }
            }
        }
        
        $responseData = [
            'data' => $data,
            'mergedData' => $mergedData,
            'apprisalData' => $apprisalData,
            'apprisal_type' => $apprisalData->apprisal_type, 
            'apprisal_for' => $apprisalData->apprisal_for,
            'class_id' => $apprisalData->class_id, 
            'student_id' => $apprisalData->student_id,
            'teacher_id' => $apprisalData->teacher_id,
            'staff_id' => $apprisalData->staff_id,
        ];
        return returnData(2000, $responseData);
    }
    
    

    public function update(Request $request, $id)
    {
        if (!can('appraisal_update')) {
            return $this->notPermitted();
        }
    
        try {
            $input = $request->all();
            $authID = auth()->user()->school_id;
    
            $validation = $this->model->validate($input);
            if ($validation->fails()) {
                return response()->json(['status' => 2000, 'errors' => $validation->errors()], 200);
            }
    
            $data = $this->model->find($id);
            if (!$data) {
                return returnData(5000, null, 'Data Not Found');
            }
            
            if ($input['apprisal_type'] == '1') {
                $input['teacher_id'] = null;
                $input['staff_id'] = null;
            } elseif ($input['apprisal_type'] == '2') {
                $input['student_id'] = null;
                $input['staff_id'] = null;
            } elseif ($input['apprisal_type'] == '3') {
                $input['student_id'] = null;
                $input['teacher_id'] = null;
            }
    
            $getMarks = isset($input['get_mark']) && is_array($input['get_mark']) 
                        ? array_filter($input['get_mark'], fn($mark) => $mark !== null) 
                        : [];
    
            $data->fill($input);
            $data->cgpa = round($request->input('cgpa', 0)); // Default to 0 if no cgpa provided
            $data->school_id = $authID;
            $data->get_mark = !empty($getMarks) ? json_encode($getMarks) : null; // Encode getMarks or store null if empty
    
            $data->save();
    
            return returnData(2000, null, 'Successfully Updated');
        } catch (\Exception $exception) {
            return returnData(5000, $exception->getMessage(), 'Whoops, Something Went Wrong..!!');
        }
    }
    
    public function destroy($id)
    {
        if (!can('appraisal_delete')) {
            return $this->notPermitted();
        }
        try {
            $data = $this->model->where('id', $id)->first();
            if ($data) {
                $data->delete();
                return returnData(2000, $data, 'Successfully Deleted');
            }
            return returnData(5000, null, 'Data Not found');
        } catch (\Exception $exception) {
            return returnData(5000, $exception->getMessage(), 'Whoops, Something Went Wrong..!!');
        }
    }

    public function getTemplateData()
    {
        $templateId = request('template_id');
        $data = ApprisalTemplate::where('id', $templateId)->first();
        $apprisalData = Apprisal::where('template_id', $templateId)->first();
        $mergedData = [];
    
        if ($data) {
            $kraData = json_decode($data->kra, true);
            $weightageData = json_decode($data->wheightage, true);
            $getMarkData = !empty($apprisalData->get_mark) ? json_decode($apprisalData->get_mark, true) : [];
    
            if (is_array($kraData) && is_array($weightageData)) {
                $count = min(count($kraData), count($weightageData));
                for ($i = 0; $i < $count; $i++) {
                    $mergedData[] = [
                        'kra' => $kraData[$i],
                        'weightage' => $weightageData[$i],
                        // 'get_mark' => $getMarkData[$i] ?? '', // Add get_mark if available, otherwise empty string
                    ];
                }
            }
        }

        $responseData = [
            'data' => $data,
            'mergedData' => $mergedData,
        ];
        // dd($responseData);
        return returnData(2000, $responseData);
    }
    

}

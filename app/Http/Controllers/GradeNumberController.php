<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Models\Examination;
use App\Models\GradeNumber;
use Illuminate\Http\Request;

class GradeNumberController extends Controller
{
    use Helper;
    public function __construct()
    {
        $this->model = new GradeNumber();
    }

    public function index()
    {
        if (!can('grade_number_view')) {
            return $this->notPermitted();
        }
        try {
            $keyword = request()->input('keyword');

            $class = request()->input('class_id');
            $department = request()->input('department_id');
            $section = request()->input('section_id');

            $data = $this->model::with('subject:id,name,subject_mark', 'classes:id,name', 'department:id,department_name', 'section:id,name', 'session:id,title', 'exam:id,name', 'exam_type:id,type_name')
                ->when($keyword, function ($query) use ($keyword) {
                    $query->whereHas('subject', function ($q) use ($keyword) {
                        $q->where('name', 'like', "%$keyword%");
                    });
                })
                ->when($class, function ($query) use ($class) {
                    $query->where('class_id', $class);
                })->when($department, function ($query) use ($department) {
                    $query->where('department_id', $department);
                })->when($section, function ($query) use ($section) {
                    $query->where('section_id', $section);
                })

                ->orderBy('class_id')
                ->orderBy('id', 'desc')
                ->paginate(input('perPage'));

            $data->getCollection()->transform(function ($item) {
                $item->pass_mark = json_decode($item->pass_mark);
                $item->mark_component = json_decode($item->mark_component);
                $item->exam_mark = json_decode($item->exam_mark);
                $item->convert_num = json_decode($item->convert_num);
                $item->total_mark = json_decode($item->total_mark);
                return $item;
            });

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
        if (!can('grade_number_add')) {
            return $this->notPermitted();
        }

        $authID = auth()->user()->school_id;

        try {
            $input = $request->all();
            $validate = $this->model->validate($input);
            if ($validate->fails()) {
                return returnData(3000, $validate->errors());
            }

            // Check for duplicate before inserting
            $exists = $this->model->where('school_id', $authID)
                ->where('class_id', $input['class_id'])
                ->where('department_id', $input['department_id'])
                ->where('section_id', $input['section_id'])
                ->where('session_year_id', $input['session_year_id'])
                ->where('exam_id', $input['exam_id'])
                ->where('exam_type_id', $input['exam_type_id'])
                ->where('subject_id', $input['subject_id'])
                ->exists();

            if ($exists) {
                return returnData(3000, null, 'This subject is already assigned with the same exam configuration.');
            }

            $markComponents = [];
            $passMarks = [];
            $examMarks = [];
            $convertNums = [];
            $totalMarks = [];

            foreach ($input['exam_mark_type'] as $examMarkType) {
                $markComponents[] = $examMarkType['mark_component'];
                $passMarks[] = $examMarkType['pass_mark'];
                $examMarks[] = $examMarkType['exam_mark'];
                $convertNums[] = $examMarkType['convert_num'];
                $totalMarks[] = $examMarkType['total_mark'];
            }

            $this->model->fill($input);
            $this->model->school_id = $authID;

            $this->model->mark_component = json_encode($markComponents);
            $this->model->pass_mark = json_encode($passMarks);
            $this->model->exam_mark = json_encode($examMarks);
            $this->model->convert_num = json_encode($convertNums);
            $this->model->total_mark = json_encode($totalMarks);
            $this->model->save();

            return returnData(2000, null, 'Successfully Inserted');
        } catch (\Exception $exception) {
            return returnData(5000, $exception->getMessage(), 'Whoops, Something Went Wrong..!!');
        }
    }

    public function show($id)
    {

    }

    public function edit($id)
    {
        $data = $this->model::with('subject:id,name', 'classes:id,name', 'department:id,department_name', 'section:id,name', 'session:id,title')
            ->where('id', $id)
            ->first();

        if ($data) {
            $data->mark_component = json_decode($data->mark_component, true);
            $data->pass_mark = json_decode($data->pass_mark, true);
            $data->exam_mark = json_decode($data->exam_mark, true);
            $data->convert_num = json_decode($data->convert_num, true);
            $data->total_mark = json_decode($data->total_mark, true);
        }
        return returnData(2000, $data);
    }

    public function update(Request $request, $id)
    {
        if (!can('grade_number_update')) {
            return $this->notPermitted();
        }
        try {
            $input = $request->all();
            $authID = auth()->user()->school_id;
            $validation = $this->model->validate($input);

            if ($validation->fails()) {
                return returnData(3000, $validation->errors());
            }

            $data = $this->model->find($id);

            if (!$data) {
                return returnData(3000, null, 'Data Not Found');
            }

            // Duplicate check before updating
            $exists = $this->model->where('school_id', $authID)
                ->where('class_id', $input['class_id'])
                ->where('department_id', $input['department_id'])
                ->where('section_id', $input['section_id'])
                ->where('session_year_id', $input['session_year_id'])
                ->where('exam_id', $input['exam_id'])
                ->where('exam_type_id', $input['exam_type_id'])
                ->where('subject_id', $input['subject_id'])
                ->where('id', '!=', $id)
                ->exists();

            if ($exists) {
                return returnData(3000, null, 'This subject configuration already exists for another record.');
            }

            $markComponents = [];
            $passMarks = [];
            $examMarks = [];
            $convertNums = [];
            $totalMarks = [];
            if (isset($input['exam_mark_type'])) {
                foreach ($input['exam_mark_type'] as $examMarkType) {
                    $markComponents[] = $examMarkType['mark_component'];
                    $passMarks[] = $examMarkType['pass_mark'];
                    $examMarks[] = $examMarkType['exam_mark'];
                    $convertNums[] = $examMarkType['convert_num'];
                    $totalMarks[] = $examMarkType['total_mark'];
                }
            }
            $data->fill($input);
            $data->school_id = $authID;
            $data->mark_component = json_encode($markComponents);
            $data->pass_mark = json_encode($passMarks);
            $data->exam_mark = json_encode($examMarks);
            $data->convert_num = json_encode($convertNums);
            $data->total_mark = json_encode($totalMarks);
            $data->save();

            return returnData(2000, null, 'Successfully Updated');

        } catch (\Exception $exception) {
            return returnData(5000, $exception->getMessage(), 'Whoops, Something Went Wrong..!!');
        }
    }

    public function destroy($id)
    {
        if (!can('grade_number_delete')) {
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

    public function getExamType(Request $request)
    {
        $examTypes = Examination::where('class_id', $request->class_id)
            ->where('exam_id', $request->exam_id)
            ->where('status', 1)
            ->get(['id', 'type_name']);

        return response()->json(['examType' => $examTypes]);
    }
}

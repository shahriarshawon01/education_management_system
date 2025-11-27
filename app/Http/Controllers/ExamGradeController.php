<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Models\ExamGrade;
use Illuminate\Http\Request;

class ExamGradeController extends Controller
{
    use Helper;
    public function __construct()
    {
        $this->model = new ExamGrade();
    }

    public function index()
    {
        if (!can('examination_grade_view')) {
            return $this->notPermitted();
        }
        try {
            $keyword = request()->input('keyword');
            $gradeNumber = request()->input('grade_number_id');

            $data = $this->model->with('gradeNumber:id,grade_number')
                ->when($keyword, function ($query) use ($keyword) {
                    $query->where('grade_name', 'Like', "%$keyword%");
                })
                ->when($gradeNumber, function ($query) use ($gradeNumber) {
                    $query->where('grade_number_id', $gradeNumber);
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
        if (!can('examination_grade_add')) {
            return $this->notPermitted();
        }
        $schoolID = auth()->user()->school_id;
        try {
            $input = $request->all();
            $validate = $this->model->validate($input);
            if ($validate->fails()) {
                return returnData(2000, $validate->errors());
            }

            $this->model->fill($input);
            $this->model->school_id = $schoolID;
            $this->model->save();
            return returnData(2000, null, 'Successfully Inserted');
        } catch (\Exception $exception) {
            return returnData(5000, $exception->getMessage(), 'Whoops, Something Went Wrong..!!');
        }
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
        if (!can('examination_grade_update')) {
            return $this->notPermitted();
        }
        try {
            $input = $request->all();
            $schoolID = auth()->user()->school_id;
            $validation = $this->model->validate($input);
            if ($validation->fails()) {
                return response()->json(['status' => 2000, 'errors' => $validation->errors()], 200);
            }
            $data = $this->model->find($id);

            if ($data) {
                $data->update($input);
                $data->school_id = $schoolID;
                $data->save();
                return returnData(2000, null, 'Successfully Updated');
            }

            return returnData(5000, null, 'Data Not found');
        } catch (\Exception $exception) {
            return returnData(5000, $exception->getMessage(), 'Whoops, Something Went Wrong..!!');
        }
    }

    public function destroy($id)
    {
        if (!can('examination_grade_delete')) {
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
}

<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Models\WorkDepartment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EmployeeDepartmentController extends Controller
{
    use Helper;
    public function __construct()
    {
        $this->model = new WorkDepartment();
    }

    public function index()
    {
        if (!can('employee_department_view')) {
            return $this->notPermitted();
        }
        try {
            $keyword = request()->input('keyword');
            $data = $this->model
                ->when($keyword, function ($query) use ($keyword) {
                    $query->where('name', 'Like', "%$keyword%");
                })->orderby('id', 'desc')
                ->paginate(input('perPage'));
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
        if (!can('employee_department_add')) {
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
        if (!can('employee_department_update')) {
            return $this->notPermitted();
        }
        try {
            $schoolId = auth()->user()->school_id;
            $input = $request->all();
            $validation = $this->model->validate($input);
            if ($validation->fails()) {
                return response()->json(['status' => 5000, 'errors' => $validation->errors()], 200);
            }
            $data = $this->model->find($id);

            if ($data) {
                $data->fill($request->all());
                $data->school_id = $schoolId;
                $data->save();
                DB::commit();
                return returnData(2000, null, 'Successfully Updated');
            }

            return returnData(5000, null, 'Record Not found');
        } catch (\Exception $exception) {
            return returnData(5000, $exception->getMessage(), 'Whoops, Something Went Wrong..!!');
        }
    }

    public function destroy($id)
    {
        if (!can('employee_department_delete')) {
            return $this->notPermitted();
        }
        try {
            $data = $this->model->find($id); 
            if ($data) {
                $data->delete(); 
                return returnData(2000, null, 'Successfully Deleted'); 
            }
            return returnData(5000, null, 'Record Not Found');
        } catch (\Exception $exception) {
            return returnData(5000, $exception->getMessage(), 'Whoops, Something Went Wrong..!!');
        }
    }

}

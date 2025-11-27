<?php

namespace App\Http\Controllers\Employee;

use App\Helpers\Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Employee\EmployeeDesignation;

class EmployeeDesignationController extends Controller
{
    use Helper;
    public function __construct()
    {
        $this->model = new EmployeeDesignation();
    }

    public function index()
    {
        if (!can('employee_designation_view')) {
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
        if (!can('employee_designation_add')) {
            return $this->notPermitted();
        }
        $schoolId = auth()->user()->school_id;
        try {
            $input = $request->all();
            $validate = $this->model->validate($input);
            if ($validate->fails()) {
                return returnData(2000, $validate->errors());
            }

            $this->model->fill($input);
            $this->model->school_id = $schoolId;
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
        if (!can('employee_designation_update')) {
            return $this->notPermitted();
        }
        try {
            $input = $request->all();
            $schoolId = auth()->user()->school_id;
            $validation = $this->model->validate($input);
            if ($validation->fails()) {
                return response()->json(['status' => 5000, 'errors' => $validation->errors()], 5000);
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
        if (!can('employee_designation_delete')) {
            return $this->notPermitted();
        }
        try {
            $data = $this->model->where('id', $id)->first();
            if ($data) {
                $data->delete();
                return returnData(2000, $data, 'Successfully Deleted');
            }
            return returnData(5000, null, 'Record Not found');
        } catch (\Exception $exception) {
            return returnData(5000, $exception->getMessage(), 'Whoops, Something Went Wrong..!!');
        }
    }
}

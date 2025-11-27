<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use Illuminate\Support\Facades\DB;

class DepartmentController extends Controller
{
    use Helper;

    public function __construct()
    {
        $this->model = new Department();
    }
    public function index()
    {
        if (!can('department_view')) {
            return $this->notPermitted();
        }
        try {
            $keyword = request()->input('keyword');
            $data = $this->model->with('class:id,name')
                ->when($keyword, function ($query) use ($keyword) {
                    $query->where('department_name', 'Like', "%$keyword%");
                })
                ->orderBy('id', 'DESC')
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
        if (!can('department_add')) {
            return $this->notPermitted();
        }
        $schoolId = auth()->user()->school_id;
        $createdBy = auth()->user()->id;
        try {
            $validate = $this->model->validate($request->all());

            if ($validate->fails()) {
                return returnData(3000, $validate->errors(), 'Validation Failed');
            }
            DB::beginTransaction();
            $this->model->fill($request->all());
            $this->model->school_id = $schoolId;
            $this->model->created_by = $createdBy;
            $this->model->save();

            DB::commit();

            return returnData(2000, null, 'Successfully Inserted');
        } catch (\Exception $exception) {
            return returnData(5000, $exception->getMessage(), $exception->getMessage());
        }
    }

    public function show($id)
    {
        // if (!can('department_view')) {
        //     return $this->notPermitted();
        // }
        $data = $this->model->with(['class','users'])->where('id', $id)->first();
        return returnData(2000, $data);
    }

    public function edit(Department $department)
    {
        
    }

    public function update(Request $request, $id)
    {
        if (!can('department_update')) {
            return $this->notPermitted();
        }

        $schoolId = auth()->user()->school_id;
        $createdBy = auth()->user()->id;

        try {
            $department = $this->model->findOrFail($id);
            $validate = $this->model->validate($request->all());

            if ($validate->fails()) {
                return returnData(3000, $validate->errors(), 'Validation Failed');
            }
            $updateData = $request->except(['class']);
            DB::beginTransaction();

            $department->fill($updateData);
            $department->school_id = $schoolId;
            $department->created_by = $createdBy;
            $department->save();

            DB::commit();

            return returnData(2000, null, 'Successfully Updated');
        } catch (\Exception $exception) {
            return returnData(5000, $exception->getMessage(), 'Whoops, Something Went Wrong..!!');
        }
    }

    public function destroy($id)
    {
        if (!can('department_delete')) {
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

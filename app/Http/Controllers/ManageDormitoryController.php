<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Models\Dormitory\AssignDormitory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\ManageDormitory;

class ManageDormitoryController extends Controller
{
    use Helper;

    public function __construct()
    {
        $this->model = new ManageDormitory();
    }

    public function index()
    {
        if (!can('dormitory_view')) {
            return $this->notPermitted();
        }
        try {
            $keyword = request()->input('keyword');
            $data = $this->model->with('employee:id,name')
                ->when($keyword, function ($query) use ($keyword) {
                    $query->where('dormitory_name', 'Like', "%$keyword%");
                })->orderBy('id', 'DESC')
                ->paginate(input('perPage'));

            return returnData(2000, $data);
        } catch (\Exception $exception) {
            return returnData(5000, $exception->getMessage(), 'Whoops, Something Went Wrong..!!');
        }
    }

    public function store(Request $request)
    {
        if (!can('dormitory_add')) {
            return $this->notPermitted();
        }
        try {
            $schoolId = auth()->user()->school_id;
            $userId = auth()->user()->id;
            $input = $request->all();
            $validate = $this->model->validate($input);

            if ($validate->fails()) {
                return returnData(3000, $validate->errors(), 'Validation Failed');
            }
            DB::beginTransaction();

            $this->model->fill($request->all());
            $this->model->school_id = $schoolId;
            $this->model->created_by = $userId;
            $this->model->save();

            DB::commit();

            return returnData(2000, null, 'Dormitory Inserted');
        } catch (\Exception $exception) {
            return returnData(5000, $exception->getMessage(), $exception->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        if (!can('dormitory_update')) {
            return $this->notPermitted();
        }
        try {
            $input = $request->all();
            $validation = $this->model->validate($input);
            $schoolId = auth()->user()->school_id;
            $userId = auth()->user()->id;

            if ($validation->fails()) {
                return returnData(3000, $validation->errors(), 'Validation Failed');
            }
            unset($input['teacher']);

            DB::beginTransaction();
            $data = $this->model->where('id', $id)->first();

            if ($data) {
                $data->fill($input);
                $data->school_id = $schoolId;
                $data->created_by = $userId;
                $data->author_id = $request->input('author_id');
                $data->save();

                DB::commit();

                return returnData(2000, null, 'Dormitory Updated');
            }
            return returnData(5000, null, 'Dormitory Not found');
        } catch (\Exception $exception) {
            return returnData(5000, $exception->getMessage(), $exception->getMessage());
        }
    }

    public function destroy($id)
    {
        if (!can('dormitory_delete')) {
            return $this->notPermitted();
        }
        try {
            $data = $this->model->where('id', $id)->first();
            $relatedTransport = AssignDormitory::where('dormitory_id', $data->id)->first();
            if ($relatedTransport) {
                return returnData(5000, null, 'Cannot delete: This Dormitory Already Assigned');
            }
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

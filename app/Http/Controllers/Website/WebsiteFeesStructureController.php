<?php

namespace App\Http\Controllers\Website;


use App\Helpers\Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Website\WebsiteFeesStructure;

class WebsiteFeesStructureController extends Controller
{
    use Helper;

    public function __construct()
    {
        $this->model = new WebsiteFeesStructure();
    }

    public function index()
    {
        if (!can('course_category_view')) {
            return $this->notPermitted();
        }

        try {
            $keyword = request()->input('keyword');
            $data = $this->model 
            ->with('departments:id,department_name',)
                ->when($keyword, function ($query) use ($keyword) {
                    $query->whereHas('departments', function ($q) use ($keyword){
                        $q->where('department_name','like', "%$keyword%");
                    });
                })->paginate(input('perPage'));

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
        if (!can('course_category_add')) {
            return $this->notPermitted();
        }
        try {
            $input = $request->all();
            $schoolId = auth()->user()->school_id;
            $validate = $this->model->validate($input);
            if ($validate->fails()) {
                return returnData(3000, $validate->errors());
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
    }

    public function edit($id)
    {
    }

    public function update(Request $request, $id)
    {
        if (!can('course_category_update')) {
            return $this->notPermitted();
        }

        try {
            $validate = $this->model->validate($request->all());
            $schoolId = auth()->user()->school_id;
            if ($validate->fails()) {
                return returnData(3000, $validate->errors(), 'Validation Failed');
            }
            DB::beginTransaction();
            $data = $this->model->where('id', $request->input('id'))->first();

            if ($data) {
                $data->fill($request->all());
                $data->school_id = $schoolId;
                $data->save();

                DB::commit();

                return returnData(2000, null, 'Successfully Updated');
            }
            return returnData(5000, null, 'Menu Not found');
        } catch (\Exception $exception) {
            return returnData(5000, $exception->getMessage(), $exception->getMessage());
        }
    }

    public function destroy($id)
    {
        if (!can('course_category_delete')) {
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
<?php

namespace App\Http\Controllers;


use App\Helpers\Helper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\StudentClass;

class ClassController extends Controller
{
    use Helper;

    public function __construct()
    {
        $this->model = new StudentClass();
    }

    public function index()
    {
        try {
            $keyword = request()->input('keyword');
            $data = $this->model->with('teacher')
                ->when($keyword, function ($query) use ($keyword) {
                    $query->where('name', 'Like', "%$keyword%");
                })->latest()->paginate(input('perPage'));

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
        if (!can('class_add')) {
            return $this->notPermitted();
        }
        try {
            $input = $request->all();
            $validate = $this->model->validate($input);

            if ($validate->fails()) {
                if ($validate->errors()->has('numeric_name')) {
                    return returnData(5000, null, 'Class numeric name already exists.');
                }
                return returnData(5000, $validate->errors());
            }
            $this->model->fill($input);
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
        if (!can('class_update')) {
            return $this->notPermitted();
        }

        try {
            $input = $request->all();
            $schoolID = auth()->user()->school_id;

            $validation = $this->model->validate($input, $id);
            if ($validation->fails()) {
                return response()->json(['status' => 2000, 'errors' => $validation->errors()], 200);
            }

            $data = $this->model->find($id);

            if ($data) {
                $data->fill($request->all());
                $data->school_id = $schoolID;

                $data->save();

                return returnData(2000, null, 'Successfully Updated');
            }

            return returnData(5000, null, 'Data Not Found');
        } catch (\Exception $exception) {
            return returnData(5000, $exception->getMessage(), 'Whoops, Something Went Wrong..!!');
        }
    }

    public function destroy($id)
    {
        if (!can('class_delete')) {
            return $this->notPermitted();
        }
        try {
            $data = $this->model->where('id', $id)->first();
            if ($data) {
                $data->delete();
                return returnData(2000, null, 'Successfully Deleted');
            }

            return returnData(5000, null, 'Data Not found');
        } catch (\Exception $exception) {
            return returnData(5000, $exception->getMessage(), 'Whoops, Something Went Wrong..!!');
        }
    }
}

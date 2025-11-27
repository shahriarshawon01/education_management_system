<?php

namespace App\Http\Controllers\Canteen;

use Illuminate\Http\Request;
use App\Models\Canteen\MealTime;
use App\Http\Controllers\Controller;
use App\Helpers\Helper;

class MealTimeController extends Controller
{
    use Helper;
    public function __construct()
    {
        $this->model = new MealTime();
    }

    public function index()
    {
        if (!can('meal_time_view')) {
            return $this->notPermitted();
        }
        try {
            $keyword = request()->input('keyword');
            $authID = auth()->user();

            $data = $this->model
                ->where('school_id', $authID->school_id)
                ->when($keyword, function ($query) use ($keyword) {
                    $query->where('name', 'Like', "%$keyword%");
                })
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
        if (!can('meal_time_add')) {
            return $this->notPermitted();
        }
        $authID = auth()->user();
        try {
            $input = $request->all();
            $validate = $this->model->validate($input);
            if ($validate->fails()) {
                return returnData(2000, $validate->errors());
            }

            $this->model->fill($input);
            $this->model->school_id = $authID->school_id;
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
        if (!can('meal_time_update')) {
            return $this->notPermitted();
        }
        try {
            $input = $request->all();
            $authID = auth()->user();

            $validation = $this->model->validate($input);
            if ($validation->fails()) {
                return response()->json(['status' => 2000, 'errors' => $validation->errors()], 200);
            }
            $data = $this->model->find($id);

            if ($data) {
                $data->update($input);
                $data->school_id = $authID->school_id;
                return returnData(2000, null, 'Successfully Updated');
            }

            return returnData(5000, null, 'Data Not found');
        } catch (\Exception $exception) {
            return returnData(5000, $exception->getMessage(), 'Whoops, Something Went Wrong..!!');
        }
    }

    public function destroy($id)
    {
        if (!can('meal_time_delete')) {
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

<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\FeesType;
use Illuminate\Http\Request;
use \App\Helpers\Helper;
use Illuminate\Support\Facades\DB;

class FeesTypeController extends Controller
{
    use Helper;

    public function __construct()
    {
        $this->model = new FeesType();
    }

    public function index()
    {
        if (!can('fees_type_view')) {
            return $this->notPermitted();
        }

        try {
            $data = $this->model->school()
                ->when(input('keyword'), function ($query) {
                    $keyword = input('keyword');
                    $query->where('name', 'Like', "%$keyword%");
                })->paginate(request()->input('perPage'));

            return returnData(2000, $data);

        } catch (\Exception $exception) {
            return returnData(5000, $exception->getMessage(), $exception->getMessage());
        }
    }

    public function create()
    {

    }

    public function store(Request $request)
    {
        if (!can('fees_type_add')) {
            return $this->notPermitted();
        }

        try {
            $validate = $this->model->validate($request->all());

            if ($validate->fails()) {
                return returnData(3000, $validate->errors(), 'Validation Failed');
            }

            DB::beginTransaction();

            $this->model->fill($request->all());
            $this->model->school_id = auth()->user()->school_id;
            $this->model->save();

            DB::commit();

            return returnData(2000, null, 'Successfully Inserted');

        } catch (\Exception $exception) {
            return returnData(5000, $exception->getMessage(), $exception->getMessage());
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
        if (!can('fees_type_update')) {
            return $this->notPermitted();
        }

        try {
            $validate = $this->model->validate($request->all());

            if ($validate->fails()) {
                return returnData(3000, $validate->errors(), 'Validation Failed');
            }

            DB::beginTransaction();

            $data = $this->model->where('id', $request->input('id'))->first();

            if ($data) {
                $data->fill($request->all());
                $data->save();

                DB::commit();

                return returnData(2000, null, 'Successfully Updated');
            }

            return returnData(5000, null, 'User Information Not found');

        } catch (\Exception $exception) {
            return returnData(5000, $exception->getMessage(), $exception->getMessage());
        }
    }

    public function destroy($id)
    {
        if (!can('fees_type_delete')) {
            return $this->notPermitted();
        }

        $data = $this->model->where('id', $id)->first();

        if ($data) {
            $data->delete();

            return returnData(2000, null, 'Successfully Deleted');
        }

        return returnData(2000, [], 'Department not found');
    }
}

<?php

namespace App\Http\Controllers\Accounting;

use App\Helpers\Helper;
use App\Models\Component;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Accounting\StudentWaiver;
use Illuminate\Support\Facades\Validator;

class ComponentController extends Controller
{
    use Helper;
    public function __construct()
    {
        $this->model = new Component();
    }

    public function index()
    {
        if (!can('component_view')) {
            return $this->notPermitted();
        }
        try {
            $keyword = request()->input('keyword');
            $componentType = request()->input('component_type');

            $data = $this->model
                ->filterBySchoolId()
                ->when($keyword, function ($query) use ($keyword) {
                    $query->where('name', 'Like', "%$keyword%");
                })
                ->when($componentType, function ($query) use ($componentType) {
                    $query->where('component_type', 'Like', "%$componentType%");
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
        if (!can('component_add')) {
            return $this->notPermitted();
        }
        try {
            $input = $request->all();
            $user = auth()->user();

            $validate = $this->model->validate($input);
            if ($validate->fails()) {
                return returnData(5000, $validate->errors());
            }
            $this->model->fill($input);
            $this->model->school_id = $user->school_id;
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
    if (!can('component_update')) {
        return $this->notPermitted();
    }

    try {
        $input = $request->all();
        $user = auth()->user();

        // Use model's own validation method
        $validation = $this->model->validate($input);
        if ($validation->fails()) {
            return response()->json(['status' => 2000, 'errors' => $validation->errors()], 200);
        }

        $data = $this->model->find($id);

        if (!$data) {
            return returnData(5000, null, 'Data Not found');
        }

        // Update only provided fields
        $data->update([
            'name' => $request->name ?? $data->name,
            'component_type' => $request->component_type ?? $data->component_type,
            'value' => $request->value ?? $data->value,
            'school_id' => $user->school_id,
        ]);

        return returnData(2000, null, 'Successfully Updated');

    } catch (\Exception $exception) {
        return returnData(5000, $exception->getMessage(), 'Whoops, Something Went Wrong..!!');
    }
}



    public function destroy($id)
    {
        if (!can('component_delete')) {
            return $this->notPermitted();
        }
        try {
            $data = $this->model->where('id', $id)->first();
            $waiverData = StudentWaiver::where('component_id', $id)->first();
            if ($waiverData) {
                return returnData(5000, $data, 'First delete student waiver data');
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

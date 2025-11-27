<?php

namespace App\Http\Controllers\Transport;

use App\Helpers\Helper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Transport\TransportComponent;

class TransportComponentController extends Controller
{
    use Helper;
    public function __construct()
    {
        $this->model = new TransportComponent();
    }

    public function index()
    {
        if (!can('transport_component_view')) {
            return $this->notPermitted();
        }
        try {
            $schoolId = auth()->user()->school_id;

            $data = $this->model->with('component:id,name')
                ->where('school_id', $schoolId)
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
        if (!can('transport_component_add')) {
            return $this->notPermitted();
        }
        $user = auth()->user();
        $authId = auth()->user()->id;
        try {
            $input = $request->all();

             $componentId = $request->component_id;
            $exists = $this->model
                ->where('component_id', $componentId)
                ->exists();

            if ($exists) {
                return returnData(5000, null, 'This component already exists.');
            }
            
            $validate = $this->model->validate($input);
            if ($validate->fails()) {
                return returnData(2000, $validate->errors());
            }

            $this->model->fill($input);
            $this->model->school_id = $user->school_id;
            $this->model->created_by = $authId;
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
        if (!can('transport_component_update')) {
            return $this->notPermitted();
        }
        try {
            $input = $request->all();
            $schoolId = auth()->user()->school_id;
            $authUserId = auth()->user()->id;

            $validation = $this->model->validate($input);
            if ($validation->fails()) {
                return response()->json(['status' => 2000, 'errors' => $validation->errors()], 200);
            }
            $data = $this->model->find($id);

            if ($data) {
                $data->school_id   = $schoolId;
                $data->updated_by   = $authUserId;
                $data->update($input);
                return returnData(2000, null, 'Successfully Updated');
            }

            return returnData(5000, null, 'Data Not found');
        } catch (\Exception $exception) {
            return returnData(5000, $exception->getMessage(), 'Whoops, Something Went Wrong..!!');
        }
    }

    public function destroy($id)
    {
        if (!can('transport_component_delete')) {
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

<?php

namespace App\Http\Controllers\Transport;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Transport\AssignTransport;
use App\Models\Transport\TransportManage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransportManageController extends Controller
{
    use Helper;
    public function __construct()
    {
        $this->model = new TransportManage();
    }

    public function index()
    {
        if (!can('manage_transport_view')) {
            return $this->notPermitted();
        }
        
        try {
            $keyword = request()->input('keyword');
            
            // Fetch the data with the correct search logic
            $data = $this->model->with('routes:id,route_name')
                ->when($keyword, function ($query) use ($keyword) {
                    $query->where(function ($q) use ($keyword) {
                        $q->where('transport_name', 'like', "%$keyword%")
                          ->orWhereHas('routes', function ($q) use ($keyword) {
                              $q->where('route_name', 'like', "%$keyword%");
                          });
                    });
                })
                ->paginate(request()->input('perPage'));
            
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
        if (!can('manage_transport_add')) {
            return $this->notPermitted();
        }
        try {
            $user = Auth::user();
            $input = $request->all();
            $validate = $this->model->validate($input);
            if ($validate->fails()) {
                return returnData(2000, $validate->errors());
            }
            $input['school_id'] = $user->school_id;
            $this->model->fill($input);
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
        if (!can('manage_transport_update')) {
            return $this->notPermitted();
        }
        try {
            $user = Auth::user();
            $input = $request->all();
            $validation = $this->model->validate($input);
            if ($validation->fails()) {
                return response()->json(['status' => 2000, 'errors' => $validation->errors()], 200);
            }
            $input['school_id'] = $user->school_id;
            $data = $this->model->find($id);
            if ($data) {
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
        if (!can('manage_transport_delete')) {
            return $this->notPermitted();
        }
        try {
            $data = $this->model->where('id', $id)->first();
            $relatedTransport = AssignTransport::where('transport_id', $data->id)->first();
            if ($relatedTransport) {
                return returnData(5000, null, 'Cannot delete: This Transport Already Assigned');
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

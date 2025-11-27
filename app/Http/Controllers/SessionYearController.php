<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Models\SessionYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SessionYearController extends Controller
{
    use Helper;
    public function __construct()
    {
        $this->model = new SessionYear();
    }

    public function index()
    {
        if (!can('session_view')) {
            return $this->notPermitted();
        }
        try {
            $keyword = request()->input('keyword');
            $school = request()->input('school');
            $data = $this->model->filterBySchoolId()
                ->with('school:id,title')
                ->when($keyword, function ($query) use ($keyword) {
                    $query->where('title', 'Like', "%$keyword%");
                })
                ->when($school, function ($query) use ($school) {
                    $query->where('school_id', 'Like', "%$school%");
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
        if (!can('session_add')) {
            return $this->notPermitted();
        }

        try {
            $schoolId = auth()->user()->school_id;
            $input = $request->all();
            $validate = $this->model->validate($input);

            if ($validate->fails()) {
                if ($validate->errors()->has('title')) {
                    return returnData(5000, null, 'Session Year already exists.');
                }
                return returnData(5000, $validate->errors());
            }
            $this->model->fill($input);

            $this->model->title = $request->input('title');
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

        if (!can('session_update')) {
            return $this->notPermitted();
        }
        try {
            $input = $request->all();
            $schoolId = auth()->user()->school_id;
            $validation = $this->model->validate($input, $id);

            if ($validation->fails()) {
                if ($validation->errors()->has('title')) {
                    return returnData(5000, null, 'Session Year already exists.');
                }
                return returnData(5000, $validation->errors());
            }
            $data = $this->model->find($id);

            if ($data) {
                $data->fill($request->all());
                $data->school_id = $schoolId;
                $data->save();
                DB::commit();
                return returnData(2000, null, 'Successfully Updated');
            }
            return returnData(5000, null, 'Record Not Found');
        } catch (\Exception $exception) {
            return returnData(5000, $exception->getMessage(), 'Whoops, Something Went Wrong..!!');
        }
    }

    public function destroy($id)
    {
        if (!can('session_delete')) {
            return $this->notPermitted();
        }
        try {
            $data = $this->model->where('id', $id)->first();
            if ($data) {
                $data->delete();
                return returnData(2000, null, 'Successfully Deleted');
            }
            return returnData(5000, null, 'Record Not found');
        } catch (\Exception $exception) {
            return returnData(5000, $exception->getMessage(), 'Whoops, Something Went Wrong..!!');
        }
    }
}

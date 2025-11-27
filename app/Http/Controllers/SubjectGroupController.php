<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Models\Subject;
use App\Models\SubjectGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SubjectGroupController extends Controller
{
    use Helper;
    public function __construct()
    {
        $this->model = new SubjectGroup();
    }

    public function index()
    {
        if (!can('group_subject_view')) {
            return $this->notPermitted();
        }
        try {
            $keyword = request()->input('keyword');
            $data = $this->model
                ->when($keyword, function ($query) use ($keyword) {
                    $query->where('name', 'Like', "%$keyword%");
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
        if (!can('group_subject_add')) {
            return $this->notPermitted();
        }

        try {
            $schoolId = auth()->user()->school_id;
            $input = $request->all();
            $validate = $this->model->validate($input);

            if ($validate->fails()) {
                return returnData(3000, $validate->errors());
            }

            $this->model->fill($input);

            $this->model->name = $request->input('name');
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
        if (!can('group_subject_update')) {
            return $this->notPermitted();
        }
        try {
            $input = $request->all();
            $schoolId = auth()->user()->school_id;
            $validation = $this->model->validate($input);

            if ($validation->fails()) {

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
        if (!can('group_subject_delete')) {
            return $this->notPermitted();
        }
        try {
            $checkSubject = Subject::where('subject_group_id', $id)->first();
            if ($checkSubject) {
                return returnData(3000, null, 'Group subject cannot be deleted as it has related subject.');
            }

            SubjectGroup::where('id', $id)->delete();

            return returnData(2000, null, 'Successfully Deleted');
        } catch (\Exception $exception) {
            return returnData(5000, $exception->getMessage(), 'Whoops, Something Went Wrong..!!');
        }
    }
}

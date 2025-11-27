<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class SubjectController extends Controller
{
    use Helper;
    public function __construct()
    {
        $this->model = new Subject();
    }

    public function index()
    {
        if (!can('subjects_view')) {
            return $this->notPermitted();
        }
        try {
            $keyword = request()->input('keyword');

            $class = request()->input('class_id');
            $department = request()->input('department_id');
            $section = request()->input('section_id');

            $data = $this->model
                ->with('school:id,title', 'classes:id,name', 'sessions:id,title', 'departments:id,department_name', 'sections:id,name', 'teachers:id,name', 'groupSubject:id,name')
                ->when($keyword, function ($query) use ($keyword) {
                    $query->where('name', 'Like', "%$keyword%");
                })->when($class, function ($query) use ($class) {
                    $query->where('class_id', $class);
                })->when($department, function ($query) use ($department) {
                    $query->where('department_id', $department);
                })->when($section, function ($query) use ($section) {
                    $query->where('section_id', $section);
                })
                ->orderBy('class_id')
                ->orderByDesc('id')
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
        if (!can('subjects_add')) {
            return $this->notPermitted();
        }
        try {
            $input = $request->all();
            $schoolId = auth()->user()->school_id;
            $validate = $this->model->validate($input);
            if ($validate->fails()) {
                return returnData(2000, $validate->errors());
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
        if (!can('subjects_view')) {
            return $this->notPermitted();
        }
        $data = $this->model->where('id', $id)
            ->with(
                'school:id,title',
                'classes:id,name',
                'groupSubject:id,name',
                'sessions:id,title',
                'departments:id,department_name',
                'teachers:id,name'

            )->first();
        return returnData(2000, $data);
    }

    public function edit($id)
    {
        try {
            $resultData = $this->model->with([
                'classes:id,name',
                'sessions:id,title',
                'departments:id,department_name',
                'sections:id,name',
                'teachers:id,name'
            ])
                ->where('id', $id)
                ->first();
            if (!$resultData) {
                return returnData(5000, null, 'No record found.');
            }
            return returnData(2000, $resultData);
        } catch (\Exception $exception) {
            return returnData(5000, $exception->getMessage(), $exception->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        if (!can('subjects_update')) {
            return $this->notPermitted();
        }

        try {
            $input = $request->except(['school', 'classes', 'sessions', 'sections', 'departments', 'teachers', 'group_subject']);
            $validate = $this->model->validate($request->all());
            $schoolId = auth()->user()->school_id;
            if ($validate->fails()) {
                return returnData(3000, $validate->errors(), 'Validation Failed');
            }
            DB::beginTransaction();
            $data = $this->model->where('id', $id)->first();

            if ($data) {
                $data->fill($input);
                $data->school_id = $schoolId;
                $data->save();
                DB::commit();
                return returnData(2000, null, 'Successfully Updated');
            }
            return returnData(5000, null, 'Subject Menu Not found');
        } catch (\Exception $exception) {
            return returnData(5000, $exception->getMessage(), $exception->getMessage());
        }
    }

    public function destroy($id)
    {
        if (!can('subjects_delete')) {
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

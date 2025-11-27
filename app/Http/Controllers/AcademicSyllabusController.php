<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Models\AcademicSyllabus;
use App\Models\ApproveLeave;
use App\Models\Leave;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AcademicSyllabusController extends Controller
{
    use Helper;

    public function __construct()
    {
        $this->model = new AcademicSyllabus();
    }
    public function index()
    {
        if (!can('academic_syllabus_view')) {
            return $this->notPermitted();
        }
        $authID = auth()->user();
        try {
            $keyword = request()->input('keyword');
            $data = $this->model->with('class:id,name', 'department:id,department_name', 'subject:id,name',)
                ->where('school_id', $authID->school_id)
                ->when($keyword, function ($query) use ($keyword) {
                    $query->where('title', 'Like', "%$keyword%");
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
        if (!can('academic_syllabus_add')) {
            return $this->notPermitted();
        }
        $authID = auth()->user();
        try {
            $validate = $this->model->validate($request->all());

            if ($validate->fails()) {
                return returnData(3000, $validate->errors(), 'Validation Failed');
            }

            DB::beginTransaction();

            $this->model->fill($request->all());
            $this->model->school_id = $authID->school_id;
            $this->model->save();

            DB::commit();

            return returnData(2000, null, 'Successfully Inserted');
        } catch (\Exception $exception) {
            return returnData(5000, $exception->getMessage(), $exception->getMessage());
        }
    }

    public function show($id)
    {
    }


    public function edit($id)
    {
        $data = $this->model::leftJoin('departments','academic_syllabus.department_id','departments.id')
        ->leftJoin('subjects','academic_syllabus.subject_id','subjects.id')
        ->select('academic_syllabus.department_id', 'departments.department_name','academic_syllabus.subject_id')
        ->where('academic_syllabus.id',$id)->first();
        // dd($data);
        return returnData(2000, $data);
    }


    public function update(Request $request, $id)
    {
        // ddA($request);
        if (!can('academic_syllabus_update')) {
            return $this->notPermitted();
        }
        $authID = auth()->user();
        try {
            $validate = $this->model->validate($request->all());

            if ($validate->fails()) {
                return returnData(3000, $validate->errors(), 'Validation Failed');
            }

            DB::beginTransaction();

            $data = $this->model->where('id', $request->input('id'))->first();
         
            if ($data) {
                $data->fill($request->all());
                $data->school_id = $authID->school_id;
                $data->save();

                DB::commit();

                return returnData(2000, null, 'Successfully Updated');
            }

            return returnData(5000, null, 'Data Not found');
        } catch (\Exception $exception) {
            return returnData(5000, $exception->getMessage(), $exception->getMessage());
        }
    }

    public function destroy($id)
    {
        if (!can('academic_syllabus_delete')) {
            return $this->notPermitted();
        }
        $data = $this->model->where('id', $id)->first();

        if ($data) {
            $data->delete();

            return returnData(2000, null, 'Successfully Deleted');
        }

        return returnData(2000, [], 'Data not found');
    }
}

<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SectionController extends Controller
{
    use Helper;

    public function __construct()
    {
        $this->model = new Section();
    }

    public function index()
    {
        if (!can('section_view')) {
            return $this->notPermitted();
        }
        try {
            $keyword = request()->input('keyword');
            $class = request()->input('class_id');
            $data = $this->model
                ->with('classes:id,name')
                ->when($keyword, function ($query) use ($keyword) {
                    $query->where('name', 'Like', "%$keyword%");
                })->when($class, function ($query) use ($class) {
                    $query->where('class_id', 'Like', "%$class%");
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
        if (!can('section_add')) {
            return $this->notPermitted();
        }
        $schoolId = auth()->user()->school_id;
        try {
            $input = $request->all();

            $validate = $this->model->validate($input);
            if ($validate->fails()) {
                return returnData(3000, $validate->errors());
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
        //
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        if (!can('section_update')) {
            return $this->notPermitted();
        }

        $schoolId = auth()->user()->school_id;

        try {
            $section = $this->model->findOrFail($id);
            $validate = $this->model->validate($request->all());

            if ($validate->fails()) {
                return returnData(3000, $validate->errors(), 'Validation Failed');
            }
            $updateData = $request->except(['classes']);
            DB::beginTransaction();

            $section->fill($updateData);
            $section->school_id = $schoolId;
            $section->save();

            DB::commit();

            return returnData(2000, null, 'Successfully Updated');
        } catch (\Exception $exception) {
            return returnData(5000, $exception->getMessage(), 'Whoops, Something Went Wrong..!!');
        }
    }


    public function destroy($id)
    {
        if (!can('section_delete')) {
            return $this->notPermitted();
        }
        try {
            $data = $this->model->where('id', $id)->first();
            if ($data) {
                $data->delete();
                return returnData(2000, $data, 'Successfully Deleted');
            }

            return returnData(5000, null, 'Record not found');
        } catch (\Exception $exception) {
            return returnData(5000, $exception->getMessage(), 'Whoops, Something Went Wrong..!!');
        }
    }
}

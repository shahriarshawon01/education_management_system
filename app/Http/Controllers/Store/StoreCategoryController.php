<?php

namespace App\Http\Controllers\Store;

use App\Helpers\Helper;
use App\Models\Store\StoreCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class StoreCategoryController extends Controller
{
    use Helper;

    public function __construct()
    {
        $this->model = new StoreCategory();
    }

    public function index()
    {
        if (!can('category_view')) {
            return $this->notPermitted();
        }

        try {
            $data = $this->model->school()
                ->when(input('keyword'), function ($query) {
                    $keyword = input('keyword');
                    $query->where('name', 'Like', "%$keyword%");
                })
                ->orderBy('id','DESC')
                ->paginate(request()->input('perPage'));

            return returnData(2000, $data);

        } catch (\Exception $exception) {
            return returnData(5000, $exception->getMessage(), $exception->getMessage());
        }
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {

        if (!can('category_add')) {
            return $this->notPermitted();
        }
        try {
            $input = $request->all();
            $schoolId = auth()->user()->school_id;
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

    }

    public function edit($id)
    {

    }


    public function update(Request $request, $id)
    {
        if (!can('category_update')) {
            return $this->notPermitted();
        }

        try {
            $schoolId = auth()->user()->school_id;
            $validate = $this->model->validate($request->all());

            if($validate->fails()){
                return returnData(3000, $validate->errors(), 'Validation Failed');
            }
            $data = $this->model->find($id);

            if($data){
                $data->fill($request->all());
                $data->school_id = $schoolId;
                $data->save();
                DB::commit();

                return returnData(2000, null, 'Successfully Updated');
            }

            return returnData(5000, null, 'Record not found');

        } catch (\Exception $exception) {
            return returnData(5000, $exception->getMessage(), $exception->getMessage());
        }
    }

    public function destroy($id)
    {
        if (!can('category_delete')) {
            return $this->notPermitted();
        }

        $data = $this->model->where('id', $id)->first();

        if($data){
            $data->delete();

            return returnData(2000, null, 'Successfully Updated');
        }

        return returnData(2000, [], 'ExamCategory not found');
    }
}

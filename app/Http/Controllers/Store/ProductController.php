<?php

namespace App\Http\Controllers\Store;

use App\Helpers\Helper;

use App\Models\Store\StoreProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class ProductController extends Controller
{
    use Helper;

    public function __construct()
    {
        $this->model = new StoreProduct();
    }

    public function index()
    {
        if (!can('product_view')) {
            return $this->notPermitted();
        }

        try {
            $data = $this->model->school()
                ->with('store_category')
                ->when(input('keyword'), function ($query) {
                    $keyword = input('keyword');
                    $query->where('name', 'Like', "%$keyword%");
                })
                ->orderBy('id', 'DESC')
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
        if (!can('product_add')) {
            return $this->notPermitted();
        }

        try {
            $schoolId = auth()->user()->school_id;
            $validate = $this->model->validate($request->all());

            if ($validate->fails()) {
                return returnData(3000, $validate->errors(), 'Validation Failed');
            }

            DB::beginTransaction();

            $this->model->fill($request->all());
            $this->model->school_id = $schoolId;
            $this->model->save();

            DB::commit();

            return returnData(2000, null, 'Successfully Inserted');
        } catch (\Exception $exception) {
            return returnData(5000, $exception->getMessage(), $exception->getMessage());
        }
    }

    public function show($id) {}

    public function edit($id) {}


    public function update(Request $request, $id)
    {
        if (!can('product_update')) {
            return $this->notPermitted();
        }

        try {
            $validate = $this->model->validate($request->all());
            $schoolId = auth()->user()->school_id;

            if ($validate->fails()) {
                return returnData(3000, $validate->errors(), 'Validation Failed');
            }
            $data = $this->model->find($id);

            if ($data) {
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
        if (!can('product_delete')) {
            return $this->notPermitted();
        }

        $data = $this->model->where('id', $id)->first();

        if ($data) {
            $data->delete();

            return returnData(2000, null, 'Successfully Updated');
        }

        return returnData(2000, [], 'Hostel not found');
    }
}

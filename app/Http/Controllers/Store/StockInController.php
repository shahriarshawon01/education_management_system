<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use App\Models\Store\StockIn;
use Illuminate\Support\Facades\DB;

class StockInController extends Controller
{
    use Helper;

    public function __construct()
    {
        $this->model = new StockIn();
    }

    public function index()
    {
        if (!can('stock_in_view')) {
            return $this->notPermitted();
        }

        try {
            $data = $this->model->school()
                ->with('store_category', 'product')
                ->when(input('keyword'), function ($query) {
                    $keyword = input('keyword');
                    $query->where('name', 'Like', "%$keyword%");
                })->orderBy('id', 'DESC')
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
        if (!can('stock_in_add')) {
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

    public function show($id)
    {
        if (!can('subjects_view')) {
            return $this->notPermitted();
        }
        $data = $this->model->where('id', $id)
            ->with(
                'store_category:id,name',
                'product:id,name',

            )->first();

        return returnData(2000, $data);
    }

    public function edit($id)
    {
    }


    public function update(Request $request, $id)
    {
        if (!can('stock_in_update')) {
            return $this->notPermitted();
        }
        try {
            $validate = $this->model->validate($request->all());
            $schoolId = auth()->user()->school_id;
            if ($validate->fails()) {
                return returnData(3000, $validate->errors(), 'Validation Failed');
            }
            DB::beginTransaction();
            $data = $this->model->where('id', $request->input('id'))->first();
            if ($data) {
                $data->fill($request->all());
                $data->school_id = $schoolId;
                $data->save();
                DB::commit();
                return returnData(2000, null, 'Successfully Updated');
            }
            return returnData(5000, null, 'Record found');
        } catch (\Exception $exception) {
            return returnData(5000, $exception->getMessage(), $exception->getMessage());
        }
    }

    public function destroy($id)
    {
        if (!can('stock_in_delete')) {
            return $this->notPermitted();
        }
        $data = $this->model->where('id', $id)->first();
        if ($data) {
            $data->delete();
            return returnData(2000, null, 'Successfully Updated');
        }
        return returnData(5000, [], 'Record not found');
    }
}

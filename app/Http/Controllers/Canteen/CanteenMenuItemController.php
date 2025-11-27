<?php

namespace App\Http\Controllers\Canteen;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Canteen\CanteenMenuItem;
use App\Helpers\Helper;

class CanteenMenuItemController extends Controller
{
    use Helper;
    public function __construct()
    {
        $this->model = new CanteenMenuItem();
    }

    public function index()
    {
        if (!can('canteen_menu_item_view')) {
            return $this->notPermitted();
        }
        try {
            $keyword = request()->input('keyword');
            $data = $this->model
                ->when($keyword, function ($query) use ($keyword) {
                    $query->where('item_name', 'Like', "%$keyword%");
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
        if (!can('canteen_menu_item_add')) {
            return $this->notPermitted();
        }
        try {
            $input = $request->all();
            $validate = $this->model->validate($input);
            $validateItem = CanteenMenuItem::where('item_name', $request->item_name)->exists();

            if ($validateItem) {
                return returnData(3000, null, 'The item name has already been taken.');
            }
            if ($validate->fails()) {
                return returnData(2000, $validate->errors());
            }

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
        if (!can('canteen_menu_item_update')) {
            return $this->notPermitted();
        }
        try {
            $input = $request->all();
            $validateItem = CanteenMenuItem::where('item_name', $request->item_name)->exists();

            if ($validateItem) {
                return returnData(3000, null, 'The item name has already been taken.');
            }

            $validation = $this->model->validate($input);
            if ($validation->fails()) {
                return returnData(2000, $validation->errors());
            }
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
        if (!can('canteen_menu_item_delete')) {
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

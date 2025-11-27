<?php

namespace App\Http\Controllers\Library;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Library\BookEdition;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookEditionController extends Controller
{
    use Helper;

    public function __construct()
    {
        $this->model = new BookEdition();
    }

    public function index()
    {
        if (!can('edition_view')) {
            return $this->notPermitted();
        }
        try {
            $keyword = request()->input('keyword');
            $data = $this->model
                ->when($keyword, function ($query) use ($keyword) {
                    $query->where('name', 'Like', "%$keyword%");
                })->latest()->paginate(input('perPage'));

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
        if (!can('edition_add')) {
            return $this->notPermitted();
        }
        try {
            $input = $request->all();
            $user = Auth::user();
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
        if (!can('edition_update')) {
            return $this->notPermitted();
        }
        try {
            $input = $request->all();
            $user = Auth::user();
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
        if (!can('edition_delete')) {
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

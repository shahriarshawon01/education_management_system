<?php

namespace App\Http\Controllers\Accounting;

use App\Helpers\Helper;
use App\Models\Payment;
use Illuminate\Http\Request;
use App\Models\Accounting\Bank;
use App\Http\Controllers\Controller;

class BankController extends Controller
{

    use Helper;
    public function __construct()
    {
        $this->model = new Bank();
    }

    public function index()
    {
        if (!can('bank_view')) {
            return $this->notPermitted();
        }
        try {
            $keyword = request()->input('keyword');

            $data = $this->model
                ->when($keyword, function ($query) use ($keyword) {
                    $query->where('name', 'Like', "%$keyword%");
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
        if (!can('bank_add')) {
            return $this->notPermitted();
        }
        try {
            $input = $request->all();
            $user = auth()->user();
            $name = trim($request->name);
            $exists = $this->model
                ->where('name', $name)
                ->exists();

            if ($exists) {
                return returnData(5000, null, 'This bank name already exists.');
            }

            $this->model->fill($input);
            $this->model->school_id = $user->school_id;
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
        if (!can('bank_update')) {
            return $this->notPermitted();
        }

        try {
            $user = auth()->user();
            $name = trim($request->name);
            $exists = $this->model
                ->where('name', $name)
                ->where('school_id', $user->school_id)
                ->where('id', '!=', $id)
                ->exists();

            if ($exists) {
                return returnData(5000, null, 'This bank name already exists.');
            }

            $data = $this->model->find($id);
            if (!$data) {
                return returnData(5000, null, 'Data not found.');
            }

            $data->fill($request->all());
            $data->school_id = $user->school_id;
            $data->updated_by = $user->id;
            $data->save();

            return returnData(2000, null, 'Successfully Updated');
        } catch (\Exception $exception) {
            return returnData(5000, $exception->getMessage(), 'Whoops, Something Went Wrong..!!');
        }
    }

    public function destroy($id)
    {
        if (!can('bank_delete')) {
            return $this->notPermitted();
        }
        try {
            $data = $this->model->where('id', $id)->first();
            $paymentData = Payment::where('bank_id', $id)->first();
            if ($paymentData) {
                return returnData(5000, $data, 'First delete student payment data');
            }
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

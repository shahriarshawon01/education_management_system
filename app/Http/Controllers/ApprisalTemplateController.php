<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Models\ApprisalTemplate;
use App\Models\GradeNumber;
use Illuminate\Http\Request;

class ApprisalTemplateController extends Controller
{
    use Helper;
    public function __construct()
    {
        $this->model = new ApprisalTemplate();
    }

    public function index()
    {
        if (!can('appraisal_template_view')) {
            return $this->notPermitted();
        }
        try {
            $keyword = request()->input('keyword');
            $data = $this->model
                ->when($keyword, function ($query) use ($keyword) {
                    $query->where('apprisal_for', 'Like', "%$keyword%");
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
        if (!can('appraisal_template_add')) {
            return $this->notPermitted();
        }
    
        $authID = auth()->user()->school_id;
        try {
            $input = $request->all();
            $validate = $this->model->validate($input);
            if ($validate->fails()) {
                return returnData(2000, $validate->errors());
            }
    
            // Initialize arrays for kra and weightage
            $kraComponent = [];
            $weightage = [];
            $weightageTotal = 0; // Variable to hold the total weightage
    
            foreach ($input['apprisal'] as $apprisaldata) {
                $kraComponent[] = $apprisaldata['kra'];
                $weightage[] = $apprisaldata['wheightage'];
                $weightageTotal += (float)$apprisaldata['wheightage']; // Sum up the weightage
            }
    
            // Fill the model with the input data
            $this->model->fill($input);
            $this->model->school_id = $authID;
            $this->model->kra = json_encode($kraComponent);
            $this->model->wheightage = json_encode($weightage);
            $this->model->wheightage_total = $weightageTotal; // Store the total weightage
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
        $data = $this->model->where('id', $id)->first();
        if ($data) {
            $data->kra = json_decode($data->kra, true);
            $data->wheightage = json_decode($data->wheightage, true);
        }
        // dd($data);
        return returnData(2000, $data);
    }

    public function update(Request $request, $id)
{
    if (!can('appraisal_template_update')) {
        return $this->notPermitted();
    }
    try {
        $input = $request->all();
        $authID = auth()->user()->school_id;
        $validation = $this->model->validate($input);
        if ($validation->fails()) {
            return response()->json(['status' => 2000, 'errors' => $validation->errors()], 200);
        }

        $data = $this->model->find($id);

        if ($data) {
            $kraComponent = [];
            $wheightage = [];
            $weightageTotal = 0; // Initialize total weightage variable

            if (isset($input['apprisal'])) {
                foreach ($input['apprisal'] as $apprisaldata) {
                    $kraComponent[] = $apprisaldata['kra'];
                    $wheightage[] = $apprisaldata['wheightage'];
                    $weightageTotal += (float)$apprisaldata['wheightage']; // Sum up the weightage
                }
            }

            // Update model fields
            $data->fill($input);
            $data->school_id = $authID;
            $data->kra = json_encode($kraComponent);
            $data->wheightage = json_encode($wheightage);
            $data->wheightage_total = $weightageTotal; // Store the total weightage
            $data->save();

            return returnData(2000, null, 'Successfully Updated');
        }

        return returnData(5000, null, 'Data Not Found');
    } catch (\Exception $exception) {
        return returnData(5000, $exception->getMessage(), 'Whoops, Something Went Wrong..!!');
    }
}



    public function destroy($id)
    {
        if (!can('appraisal_template_delete')) {
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

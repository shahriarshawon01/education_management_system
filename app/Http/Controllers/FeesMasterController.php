<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\FeesMaster;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FeesMasterController extends Controller
{
    use Helper;

    public function __construct()
    {
        $this->model = new FeesMaster();
    }
    public function index()
    {
        if (!can('fees_master_view')) {
            return $this->notPermitted();
        }

        try {
            $data = $this->model->school()
                ->with('session', 'types', 'section', 'department', 'class_name', 'configs')
                // ->groupBy(groups: 'session_year_id')
                // ->with('configs')
                ->when((input('class_id') || input('class_id')), function ($query) {
                    $query->whereHas('class_name', function ($query) {
                        if (input('class_id')) {
                            $query->where('class_id', input('class_id'));
                        }
                        if (input('session_year_id')) {
                            $query->where('session_year_id', input('session_year_id'));
                        }
                        if (input('section_id')) {
                            $query->where('section_id', input('section_id'));
                        }
                        if (input('department_id')) {
                            $query->where('department_id', input('department_id'));
                        }
                    });
                })
                ->paginate(request()->input('perPage'));

            return returnData(2000, $data);

        } catch (\Exception $exception) {
            return returnData(5000, $exception->getMessage(), $exception->getMessage());
        }
    }

    public function store(Request $request)
    {
        if (!can('fees_master_add')) {
            return $this->notPermitted();
        }

        try {
            $validate = $this->model->validate($request->all());

            if ($validate->fails()) {
                return returnData(3000, $validate->errors(), 'Validation Failed');
            }

            DB::beginTransaction();


            $fees = $request->input('configs');
            foreach ($fees as $fee) {
                $edu = new FeesMaster();
                $edu->fill($fee);
                $edu->school_id = auth()->user()->school_id;
                $edu->class_id = $request->input('class_id');
                $edu->session_year_id = $request->input('session_year_id');
                $edu->section_id = $request->input('section_id');
                $edu->department_id = $request->input('department_id');
                $edu->date = $request->input('date');
                $edu->save();
            }

            DB::commit();

            return returnData(2000, null, 'Successfully Inserted');

        } catch (\Exception $exception) {
            return returnData(5000, $exception->getMessage(), $exception->getMessage());
        }
    }


    // public function store(Request $request)
    // {
    //     if (!can('fees_master_add')) {
    //         return $this->notPermitted();
    //     }
    //     try {
    //         $input = $request->all();
    //         $schoolId = auth()->user()->school_id;
    //         $validate = $this->model->validate($input);
    //         if ($validate->fails()) {
    //             return returnData(2000, $validate->errors());
    //         }
    //         $this->model->fill($input);
    //         $this->model->school_id = $schoolId;
    //         $this->model->save();
    //         return returnData(2000, null, 'Successfully Inserted');
    //     } catch (\Exception $exception) {
    //         return returnData(5000, $exception->getMessage(), 'Whoops, Something Went Wrong..!!');
    //     }
    // }

    public function show($class_id)
    {

        $sessionYearId = request()->input('session_year_id');

        $data = FeesMaster::school()
            ->with('session', 'types', 'section', 'department', 'class_name')
            ->when($sessionYearId, function ($query) use ($sessionYearId) {

                $query->where('session_year_id', 'like', "%" . $sessionYearId . "%");
            })
            ->orderBy('session_year_id', 'DESC')
            ->where('class_id', $class_id)
            ->get();

        return returnData(2000, [
            'data' => $data
        ]);
    }


    public function edit($class_id)
    {
        $data = FeesMaster::with('types')->where('class_id', $class_id)->get();
        return returnData(2000, [
            'data' => $data
        ]);

    }
    // public function update(Request $request, $id)
    // {
    //     if (!can('fees_master_update')) {
    //         return $this->notPermitted();
    //     }

    //     try {
    //         $validate = $this->model->validate($request->all());

    //         if($validate->fails()){
    //             return returnData(3000, $validate->errors(), 'Validation Failed');
    //         }

    //         DB::beginTransaction();

    //         // $data = $this->model->where('id', $request->input('id'))->first();

    //         FeesMaster::where('class_id', $request->class_id)->delete();

    //         $fees = $request->input('configs');
    //         foreach ($fees as $fee){
    //             $edu = new FeesMaster();
    //             $edu->fill($fee);
    //             $edu->school_id = auth()->user()->school_id;
    //             $edu->class_id = $request->input('class_id');
    //             $edu->session_year_id = $request->input('session_year_id');
    //             $edu->section_id = $request->input('section_id');
    //             $edu->department_id = $request->input('department_id');
    //             $edu->save();

    //             DB::commit();

    //             return returnData(2000, null, 'Successfully Updated');

    //         }

    //         return returnData(5000, null, 'User Information Not found');

    //     } catch (\Exception $exception) {
    //         return returnData(5000, $exception->getMessage(), $exception->getMessage());
    //     }
    // }

    public function update(Request $request, $id)
    {

        if (!can('fees_master_update')) {
            return $this->notPermitted();
        }

        try {
            $validate = $this->model->validate($request->all());

            if ($validate->fails()) {
                return returnData(3000, $validate->errors(), 'Validation Failed');
            }

            DB::beginTransaction(); // Start the transaction

            $classId = $request->input('class_id');
            $existingFees = FeesMaster::where('class_id', $classId)->get();
            $fees = $request->input('configs');

            $existingFeesIds = $existingFees->pluck('id')->toArray();
            $updatedFeesIds = [];

            foreach ($fees as $fee) {
                if (isset($fee['id']) && in_array($fee['id'], $existingFeesIds)) {
                    // Update existing fee
                    $edu = FeesMaster::find($fee['id']);
                    $edu->fill($fee);
                    $edu->save();
                    $updatedFeesIds[] = $fee['id'];
                } else {
                    // Create new fee
                    $edu = new FeesMaster();
                    $edu->fill($fee);
                    $edu->school_id = auth()->user()->school_id;
                    $edu->class_id = $classId;
                    $edu->date = $request->input('date');
                    $edu->session_year_id = $request->input('session_year_id');
                    $edu->section_id = $request->input('section_id');
                    $edu->department_id = $request->input('department_id');
                    $edu->save();
                    $updatedFeesIds[] = $edu->id;
                }
            }


            FeesMaster::where('class_id', $classId)
                ->whereNotIn('id', $updatedFeesIds)
                ->delete();

            DB::commit();

            return returnData(2000, null, 'Successfully Updated');
        } catch (\Exception $exception) {
            DB::rollBack();
            return returnData(5000, $exception->getMessage(), 'An error occurred while updating the records');
        }
    }


    public function destroy($id)
    {
        if (!can('fees_master_delete')) {
            return $this->notPermitted();
        }

        $data = $this->model->where('id', $id)
            ->get();

        if ($data->count() > 0) {
            $data->each(function ($record) {
                $record->delete();
            });
            return returnData(2000, null, 'Successfully Deleted');
        }

        return returnData(2000, [], 'Department not found');
    }
}
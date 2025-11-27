<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Models\Routine;
use Illuminate\Http\Request;

class RoutineController extends Controller
{
    use Helper;
    public function __construct()
    {
        $this->model = new Routine();
    }

    public function index()
    {
        if (!can('class_routines_view')) {
            return $this->notPermitted();
        }

        $general = new ConfigurationController();
        $data = $general->getGeneralData(['days']);
        $authID = auth()->user()->school_id;
        $session_year_id = request()->input('session_year_id');
        $class_id = request()->input('class_id');
        $section_id = request()->input('section_id');
        try {

            $routines = [];
            $routineCounts = [];

            foreach ($data['days'] as $key => $day) {
                $routine = Routine::where('school_id', $authID)->where('day', $key)
                    ->with('subject:id,name', 'teacher:id,name', 'classRoom:id,name', 'class:id,name', 'building:id,name', 'sessions:id,title', 'sections:id,name',)
                    ->when($session_year_id, function ($query) use ($session_year_id) {
                        $query->where('session_year_id', $session_year_id);
                    })
                    ->when($class_id, function ($query) use ($class_id) {
                        $query->where('class_id', $class_id);
                    })
                    ->when($section_id, function ($query) use ($section_id) {
                        $query->where('section_id', $section_id);
                    })
                    ->get()->toArray();

                $routineCounts[] = [
                    'period' => count($routine),
                ];

                $routines[$day] = $routine;
            }

            $maxPeriod = collect($routineCounts)->sortByDesc('period')->first();

            return returnData(2000, [
                'routines' => $routines,
                'period' => $maxPeriod['period']
            ]);
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
        if (!can('class_routines_add')) {
            return $this->notPermitted();
        }

        $authID = auth()->user()->school_id;

        try {
            $input = $request->all();
            $validate = $this->model->validate($input);
            if ($validate->fails()) {
                return returnData(3000, $validate->errors());
            }

            $this->model->fill($input);
            $this->model->school_id = $authID;
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
        if (!can('class_routines_update')) {
            return $this->notPermitted();
        }
        try {
            $input = $request->all();
            $schoolId = auth()->user()->school_id;
            $validation = $this->model->validate($input);
            if ($validation->fails()) {
                return response()->json(['status' => 2000, 'errors' => $validation->errors()], 200);
            }
            $data = $this->model->find($id);

            if ($data) {
                $input['school_id'] = $schoolId;
                $data->update($input);
                return returnData(2000, null, 'Successfully Updated');
            }

            return returnData(5000, null, 'Data Not Found');
        } catch (\Exception $exception) {
            return returnData(5000, $exception->getMessage(), 'Whoops, Something Went Wrong..!!');
        }
    }

    public function destroy($id)
    {
        if (!can('room_delete')) {
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

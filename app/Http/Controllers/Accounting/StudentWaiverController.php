<?php

namespace App\Http\Controllers\Accounting;

use App\Helpers\Helper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Accounting\StudentWaiver;
use Illuminate\Support\Facades\DB;

class StudentWaiverController extends Controller
{
    use Helper;
    public function __construct()
    {
        $this->model = new StudentWaiver();
    }

    public function index()
    {
        if (!can('student_waiver_view')) {
            return $this->notPermitted();
        }
        try {
            $keyword = request()->input('keyword');
            $class = request()->input('class_id');
            $section = request()->input('section_id');
            $componentId = request()->input('component_id');

            $data = $this->model
                ->with('student:id,student_name_en,student_roll,class_id,session_year_id', 'student.sessions:id,title', 'student.classes:id,name', 'component:id,name')
                ->when($keyword, function ($query) use ($keyword) {
                    $query->whereHas('student', function ($query) use ($keyword) {
                        $query->where('student_name_en', 'like', "%$keyword%")
                            ->orWhere('student_roll', 'like', "%$keyword%");
                    });
                })
                ->when($class, function ($query) use ($class) {
                    $query->where('class_id', $class);
                })
                ->when($section, function ($query) use ($section) {
                    $query->whereHas('student', function ($q) use ($section) {
                        $q->where('section_id', $section);
                    });
                })
                ->when($componentId, function ($query) use ($componentId) {
                    $query->where('group_id', 'Like', "%$componentId%");
                })->orderby('class_id', 'asc')
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
        if (!can('student_waiver_add')) {
            return $this->notPermitted();
        }

        try {
            $componentWaivers = $request->componentWaiver;
            if (!is_array($componentWaivers) || empty($componentWaivers)) {
                return returnData(5000, 'Invalid input data format.');
            }
            $studentId = $componentWaivers[0]['student_id'];
            if (is_null($studentId)) {
                return returnData(5000, 'Student ID is required.');
            }
            $existingComponents = StudentWaiver::where('student_id', $studentId)
                ->pluck('component_id')
                ->toArray();

            DB::beginTransaction();

            foreach ($componentWaivers as $waiverData) {
                $validate = $this->model->validate($waiverData);
                if ($validate->fails()) {
                    return returnData(5000, $validate->errors());
                }

                if (in_array($waiverData['component_id'], $existingComponents)) {
                    return returnData(5000, null, 'Duplicate Component');
                }

                StudentWaiver::create($waiverData);
            }

            DB::commit();
            return returnData(2000, null, 'Successfully Inserted');
        } catch (\Exception $exception) {
            DB::rollBack();
            return returnData(5000, $exception->getMessage(), 'Whoops, Something Went Wrong..!!');
        }
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        try {
            $studentWaiver = StudentWaiver::with(['student', 'component'])->findOrFail($id);
            $resultData = [
                'id' => $studentWaiver->id,
                'class_id' => $studentWaiver->class_id,
                'session_year_id' => $studentWaiver->session_year_id,
                'student_id' => $studentWaiver->student_id,
                'student_name' => $studentWaiver->student->student_name_en,
                'student_roll' => $studentWaiver->student->student_roll,
                'component_id' => $studentWaiver->component_id,
                'component_name' => $studentWaiver->component->name,
                'type' => $studentWaiver->type,
                'value' => $studentWaiver->value,
            ];
            return returnData(2000, $resultData);
        } catch (\Exception $exception) {
            return returnData(5000, $exception->getMessage(), 'Whoops, Something Went Wrong..!!');
        }
    }

    public function update(Request $request, $id)
    {
        if (!can('student_waiver_update')) {
            return $this->notPermitted();
        }

        try {
            $input = $request->all();

            $validateWaiver = $request->componentWaiver[0];
            if (is_null($validateWaiver['student_id'])) {
                return returnData(5000, 'Student ID is required.');
            }

            $existingComponents = StudentWaiver::where('student_id', $validateWaiver['student_id'])
                ->where('id', '!=', $id)
                ->pluck('component_id')
                ->toArray();

            if (in_array($validateWaiver['component_id'], $existingComponents)) {
                return returnData(5000, null, 'Duplicate Component');
            }

            DB::beginTransaction();
            $studentWaiver = StudentWaiver::findOrFail($id);

            $validate = $this->model->validate($validateWaiver);
            if ($validate->fails()) {
                return returnData(5000, $validate->errors());
            }

            $studentWaiver->update([
                'student_id' => $validateWaiver['student_id'],
                'component_id' => $validateWaiver['component_id'],
                'type' => $validateWaiver['type'],
                'value' => $validateWaiver['value'],
            ]);

            DB::commit();

            return returnData(2000, null, 'Student waiver updated successfully');
        } catch (\Exception $exception) {
            DB::rollBack();
            return returnData(5000, $exception->getMessage(), 'Whoops, Something Went Wrong..!!');
        }
    }

    public function destroy($id)
    {
        if (!can('student_waiver_delete')) {
            return $this->notPermitted();
        }

        try {
            DB::beginTransaction();
            $studentWaiver = StudentWaiver::findOrFail($id);
            $studentWaiver->delete();
            DB::commit();

            return returnData(2000, null, 'Student waiver deleted');
        } catch (\Exception $exception) {
            DB::rollBack();
            return returnData(5000, $exception->getMessage(), 'Whoops, Something Went Wrong..!!');
        }
    }
}

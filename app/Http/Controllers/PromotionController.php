<?php

namespace App\Http\Controllers;

use App\Models\Promotion;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Helpers\Helper;

class PromotionController extends Controller
{
    use Helper;
    public function __construct()
    {
        $this->model = new Promotion();
    }

    public function index()
    {
        if (!can('promotion_view')) {
            return $this->notPermitted();
        }
        try {
            $keyword = request()->input('keyword');
            $school_id = request()->input('school_id');

            $data = $this->model->with('student:id,student_name_en', 'oldClass:id,name', 'newClass:id,name', 'oldSection:id,name', 'newSection:id,name')
                ->when($keyword, function ($query) use ($keyword) {
                    $query->whereHas('student', function ($subQuery) use ($keyword) {
                        $subQuery->where('student_name_en', 'like', "%$keyword%");
                        $subQuery->orWhere('student_roll', 'like', "%$keyword%");
                    });
                })
                ->when($school_id, function ($query) use ($school_id) {
                    $query->where('school_id', 'Like', "%$school_id%");
                })->orderBy('id', 'desc')
                ->paginate(input('perPage'));

            return returnData(2000, $data);
        } catch (\Exception $exception) {
            return returnData(5000, $exception->getMessage(), 'Whoops, Something Went Wrong..!!');
        }
    }

    public function addPromotion(Request $request)
    {
        if (!can('promotion_add')) {
            return $this->notPermitted();
        }
        try {
            $school_id = auth()->user()->school_id;

            $validate = $this->model->validate($request->all());
            if ($validate->fails()) {
                return returnData(3000, $validate->errors(), 'Validation Failed');
            }

            DB::beginTransaction();

            foreach ($request->details as $studentData) {
                $student = Student::findOrFail($studentData['id']);
                $merit_roll = isset($studentData['promotion_roll']) && !empty($studentData['promotion_roll'])
                    ? $studentData['promotion_roll']
                    : $student->merit_roll;

                $optional_subject_id =
                    (isset($studentData['optional_subject_id']) && $studentData['optional_subject_id'] != "")
                    ? $studentData['optional_subject_id']
                    : $student->optional_subject_id;


                $this->model->updateOrCreate(
                    [
                        'student_id' => $studentData['id'],
                    ],
                    [
                        'school_id' => $school_id,
                        'class_id' => $request->class_id,
                        'promotion_class_id' => $studentData['promotion_class_id'],
                        'section_id' => $request->section_id,
                        'promotion_section_id' => $studentData['promotion_section_id'],
                        'student_roll' => $studentData['student_roll'],
                        'promotion_roll' => $merit_roll,
                        'department_id' => $studentData['department_id'],
                        'session_year_id' => $studentData['session_year_id'],
                        'promotion_session_id' => $studentData['promotion_session_id'],
                        'promotion_department_id' => $studentData['promotion_department_id'],
                    ]
                );

                $student->update([
                    'merit_roll' => $merit_roll ?? $student->merit_roll,
                    'class_id' => $studentData['promotion_class_id'],
                    'section_id' => $studentData['promotion_section_id'],
                    'session_year_id' => $studentData['promotion_session_id'],
                    'department_id' => $studentData['promotion_department_id'],
                    'optional_subject_id' => $optional_subject_id,
                ]);
            }

            DB::commit();

            return returnData(2000, null, 'Successfully Inserted');
        } catch (\Exception $exception) {
            DB::rollBack();
            return returnData(5000, $exception->getMessage(), $exception->getMessage());
        }
    }

    public function destroy($id)
    {
        if (!can('promotion_delete')) {
            return $this->notPermitted();
        }
        $data = $this->model->where('id', $id)->first();

        if ($data) {
            $data->delete();
            return returnData(2000, null, 'Successfully Deleted');
        }

        return returnData(2000, [], 'Data not found');
    }

    public function getPromotionData(Request $request)
    {
        $schoolId = auth()->user()->school_id;
        $sessionId = $request->session_year_id;
        $classId = $request->class_id;
        $sectionId = $request->section_id;
        $departmentId = $request->department_id;

        $query = DB::table('students')
            ->leftJoin('student_classes', 'students.class_id', '=', 'student_classes.id')
            ->leftJoin('sections', 'students.section_id', '=', 'sections.id')
            ->leftJoin('session_years', 'students.session_year_id', '=', 'session_years.id')
            ->leftJoin('departments', 'students.department_id', '=', 'departments.id')
            ->select(
                'students.id',
                'students.school_id',
                'students.student_roll',
                'students.student_name_en',
                'students.merit_roll',
                'students.class_id',
                'students.section_id',
                'students.session_year_id',
                'students.department_id',
                'student_classes.name as class_name',
                'sections.name as section_name',
                'session_years.title as session',
                'departments.department_name as department_name',
            )->where('students.status', 1)
            ->where('students.school_id', $schoolId)
            ->when($classId, function ($query, $classId) {
                $query->where('students.class_id', $classId);
            })
            ->when($sessionId, function ($query, $sessionId) {
                $query->where('students.session_year_id', $sessionId);
            })
            ->when($sectionId, function ($query, $sectionId) {
                $query->where('students.section_id', $sectionId);
            })
            ->when($departmentId, function ($query, $departmentId) {
                $query->where('students.department_id', $departmentId);
            });

        $result = $query->orderBy('merit_roll', 'asc')->get();

        return returnData(2000, $result);
    }

    public function getNewClassWiseData(Request $request)
    {
        $classId = $request->input('class_id');

        $sections = DB::table('sections')
            ->where('class_id', $classId)
            ->select('id', 'name')
            ->get();

        return returnData(2000, ['sections' => $sections]);
    }

    public function getOptionalSubjects(Request $request)
    {
        $classId = $request->input('class_id');

        $optionalSubjects = DB::table('subjects')
            ->where('class_id', $classId)
            ->where('is_optional', 2)
            ->where('status', 1)
            ->select('id', 'name', 'subject_code')
            ->get();

        return returnData(2000, ['optional_subjects' => $optionalSubjects]);
    }

    public function getClassWiseDepartment(Request $request)
    {
        $classId = $request->class_id;

        $department = DB::table('departments')
            ->where('class_id', $classId)
            ->select('id', 'department_name')
            ->get();

        return returnData(2000, ['department' => $department]);
    }

    public function show($id)
    {
        if (!can('promotion_view')) {
            return $this->notPermitted();
        }
        $data = $this->model->where('id', $id)->with(
            'student:id,student_name_en',
            'oldClass:id,name',
            'oldSection:id,name',
            'oDepartment:id,department_name',
            'newDepartment:id,department_name',
            'newSection:id,name',
            'newClass:id,name'
        )->first();

        return returnData(2000, $data);
    }
}

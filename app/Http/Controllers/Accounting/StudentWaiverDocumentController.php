<?php

namespace App\Http\Controllers\Accounting;

use App\Helpers\Helper;
use App\Models\Student;
use Illuminate\Http\Request;
use App\Models\Employee\Employee;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Accounting\StudentWaiver;
use App\Models\Accounting\StudentWaiverDocument;

class StudentWaiverDocumentController extends Controller
{
    use Helper;
    public function __construct()
    {
        $this->model = new StudentWaiverDocument();
    }

    public function index()
    {
        if (!can('student_waiver_document_view')) {
            return $this->notPermitted();
        }
        try {
            $keyword = request()->input('keyword');
            $class = request()->input('class_id');
            $section = request()->input('section_id');

            $data = $this->model
                ->select(
                    'student_waiver_documents.*',
                    'students.student_name_en as student_name_en',
                    'students.student_roll as student_roll',
                    'students.merit_roll as merit_roll',
                    'students.father_name_en as father_name_en',
                    'students.father_phone as father_phone',
                    'students.mother_name_en as mother_name_en',
                    'students.mother_phone as mother_phone',
                    'students.date_of_birth as date_of_birth',
                    'students.photo as student_photo',
                    'students.class_id',
                    'students.section_id',
                    'students.session_year_id',
                    'students.department_id'
                )

                ->join('students', 'student_waiver_documents.student_id', '=', 'students.id')
                ->with(
                    'student:id,student_name_en,student_roll,merit_roll,session_year_id,class_id',
                    'student.sessions:id,title',
                    'student.classes:id,name',
                    'createdUser:id,username',
                    'updatedUser:id,username',
                    'teachers:id,employee_id,name,phone,nid',
                )->when($keyword, function ($query) use ($keyword) {
                    $query->where(function ($query) use ($keyword) {
                        $query->where('students.student_name_en', 'Like', "%$keyword%")
                            ->orWhere('students.student_roll', 'Like', "%$keyword%");
                    });
                })
                ->when($section, function ($query) use ($section) {
                    $query->where(function ($query) use ($section) {
                        $query->where('students.section_id', '=', $section)
                            ->orWhereNull('students.section_id');
                    });
                })
                ->when($class, function ($query) use ($class, $keyword) {
                    $query->where(function ($query) use ($class) {
                        $query->where('students.class_id', '=', $class)
                            ->orWhereNull('students.class_id');
                    });

                    if (!is_null($keyword)) {
                        $query->where('students.merit_roll', $keyword);
                    }
                    $query->orderBy('students.merit_roll', 'asc');
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
        if (!can('student_waiver_document_add')) {
            return $this->notPermitted();
        }

        $authUser = auth()->user();

        try {
            $input = $request->all();
            if (in_array((int) $input['reason'], [1, 3, 4, 6])) {
                $input['reference_id'] = $input['student_id'];
            }
            $validate = $this->model->validate($input);

            if ($validate->fails()) {
                return returnData(3000, $validate->errors(), 'Validation failed');
            }

            $this->model->fill($input);
            $this->model->created_by = $authUser->id;

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
        try {
            $data = $this->model
                ->with([
                    'student:id,student_name_en,student_roll,merit_roll,session_year_id,class_id,department_id,section_id',
                    'student.sessions:id,title',
                    'student.classes:id,name',
                    'student.departments:id,department_name',
                    'student.sections:id,name',
                    'teachers:id,employee_id,name,phone,nid',
                    'staffs:id,employee_id,name,phone,nid',
                ])
                ->find($id);

            if (!$data) {
                return returnData(5000, null, 'Record not found');
            }

            switch ((int) $data->reason) {
                case 1:
                case 3:
                    $data->reference_info = [
                        'name' => $data->reference_name,
                        'code' => $data->reference_code,
                        'phone' => $data->reference_phone,
                        'nid' => $data->reference_nid,
                    ];
                    break;

                case 2:
                    $staffOrTeacher = $data->staffs ?? $data->teachers;
                    if ($staffOrTeacher) {
                        $data->reference_info = [
                            'name' => $staffOrTeacher->name,
                            'employee_id' => $staffOrTeacher->employee_id,
                            'phone' => $staffOrTeacher->phone,
                            'nid' => $staffOrTeacher->nid,
                            'department' => optional($staffOrTeacher->department)->name ?? '',
                        ];
                    } else {
                        $data->reference_info = null;
                    }
                    break;

                case 4:
                case 5:
                    $student = $data->student;
                    $data->reference_info = $student ? [
                        'name' => $student->student_name_en,
                        'roll' => $student->student_roll,
                        'class' => $student->classes->name ?? '',
                        'department' => $student->departments->department_name ?? '',
                        'section' => $student->sections->name ?? '',
                        'session' => $student->sessions->title ?? '',
                        'merit_roll' => $student->merit_roll,
                    ] : null;
                    break;

                case 6:
                    $student = $data->student;
                    $data->reference_info = $student ? [
                        'name' => $student->student_name_en,
                        'roll' => $student->student_roll,
                        'class' => $student->classes->name ?? '',
                        'department' => $student->departments->department_name ?? '',
                        'section' => $student->sections->name ?? '',
                        'session' => $student->sessions->title ?? '',
                        'merit_roll' => $student->merit_roll,
                    ] : null;
                    break;
            }

            return returnData(2000, $data);
        } catch (\Exception $e) {
            return returnData(5000, $e->getMessage(), 'Whoops! Something went wrong.');
        }
    }

    public function update(Request $request, $id)
    {
        if (!can('student_waiver_document_update')) {
            return $this->notPermitted();
        }

        $authUser = auth()->user();

        try {
            $input = $request->all();
            if (in_array((int) $input['reason'], [1, 3, 4, 6])) {
                $input['reference_id'] = $input['student_id'];
            }

            $validate = $this->model->validate($input);
            if ($validate->fails()) {
                return returnData(3000, $validate->errors(), 'Validation failed');
            }

            $data = $this->model->find($id);

            if (!$data) {
                return returnData(5000, null, 'Data Not Found');
            }
            $data->fill($input);
            $data->updated_by = $authUser->id;

            $data->save();

            return returnData(2000, null, 'Successfully Updated');
        } catch (\Exception $exception) {
            return returnData(5000, $exception->getMessage(), 'Whoops, Something Went Wrong..!!');
        }
    }

    public function destroy($id)
    {
        if (!can('student_waiver_document_delete')) {
            return $this->notPermitted();
        }
        try {
            $data = $this->model->where('id', $id)->first();
            if (!$data) {
                return returnData(5000, null, 'Data Not Found');
            }
            $hasWaiver = StudentWaiver::where('student_id', $data->student_id)->exists();

            if ($hasWaiver) {
                return returnData(3000, null, 'Cannot delete: This student has an existing waiver. Please delete the waiver first.');
            }

            $data->delete();

            return returnData(2000, null, 'Successfully Deleted');
        } catch (\Exception $exception) {
            return returnData(5000, $exception->getMessage(), 'Whoops, Something Went Wrong..!!');
        }
    }

    public function getChildMeritStudent()
    {
        $roll = request()->input('student_roll');
        $auth = auth()->user()->school_id;
        $student = Student::where('student_roll', $roll)->first();

        if ($student) {
            $data = DB::table('students')
                ->leftJoin('student_classes', 'students.class_id', '=', 'student_classes.id')
                ->leftJoin('departments', 'students.department_id', '=', 'departments.id')
                ->leftJoin('sections', 'students.section_id', '=', 'sections.id')
                ->leftJoin('session_years', 'students.session_year_id', '=', 'session_years.id')
                ->select(
                    'students.*',
                    'student_classes.name as class_name',
                    'departments.department_name as department_name',
                    'sections.name as section_name',
                    'session_years.title as session_name',
                )
                ->where('students.status', 1)
                ->where('students.school_id', $auth)
                ->where('students.student_roll', $roll)
                ->first();

            return returnData(2000, $data, "Merit Child Student details successfully retrieved.");
        }
        return returnData(3000, null, "Student details not retrieved.");
    }

    public function getTpscStaff()
    {
        $employeeId = request()->input('employee_id');
        $authSchoolId = auth()->user()->school_id;

        $teacher = Employee::where('employee_id', $employeeId)
            ->where('school_id', $authSchoolId)
            ->where('status', 1)
            ->first();

        if ($teacher) {
            $data = DB::table('employees')
                ->leftJoin('designations', 'employees.designation_id', '=', 'designations.id')
                ->leftJoin('work_departments', 'employees.department_id', '=', 'work_departments.id')
                ->leftJoin('users', 'employees.user_id', '=', 'users.id')
                ->select(
                    'employees.*',
                    'designations.name as designation',
                    'work_departments.name as department',
                    'users.email',
                    DB::raw("'teacher' as type")
                )
                ->where('employees.employee_id', $employeeId)
                ->where('employees.school_id', $authSchoolId)
                ->first();

            return returnData(2000, $data, "Teacher details successfully retrieved.");
        }

        return returnData(3000, null, "Employee not found in either Teacher or Staff.");
    }
}

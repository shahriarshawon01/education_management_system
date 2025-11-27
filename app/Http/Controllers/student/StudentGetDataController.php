<?php

namespace App\Http\Controllers\student;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Routine;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Examination;
use App\Models\NoticeBoard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\ConfigurationController;

class StudentGetDataController extends Controller
{
    public function getEmployees()
    {
        $schoolID = auth()->user()->school_id;
        try {
            $teacher = Student::join('employees', 'students.responsible_teacher_id', '=', 'employees.id')
                ->join('employee_departments', 'employees.department_id', 'employee_departments.id')
                ->join('employee_designations', 'employees.designation_id', 'employee_designations.id')
                ->join('users', 'employees.user_id', 'users.id')
                ->where('students.school_id', $schoolID)
                ->where('employees.employee_type', 1)
                ->where('class_id', students('class_id'))
                ->groupBy('students.responsible_teacher_id')
                ->select('employees.name as name', 'users.email as email', 'employees.phone as phone', 'employee_departments.name as department_name', 'employee_designations.name as designation_name')
                ->paginate(request()->input('perPage'));

            return returnData(2000, $teacher);
        } catch (\Exception $exception) {
            return returnData(5000, $exception->getMessage(), $exception->getMessage());
        }
    }

    public function getNotice()
    {
        $schoolID = auth()->user()->school_id;
        try {
            $data = NoticeBoard::where('school_id', $schoolID)
                ->orderBy('id', 'desc')
                ->paginate(request()->input('perPage'));

            return returnData(2000, $data);
        } catch (\Exception $exception) {
            return returnData(5000, $exception->getMessage(), $exception->getMessage());
        }
    }

    public function getSubjects()
    {
        $schoolID = auth()->user()->school_id;
        try {
            $data = Subject::with('teachers', 'classes:id,name')
                ->where('school_id', $schoolID)
                ->where('session_year_id', students('session_year_id'))
                ->where('class_id', students('class_id'))
                ->paginate(request()->input('perPage'));

            return returnData(2000, $data);
        } catch (\Exception $exception) {
            return returnData(5000, $exception->getMessage(), $exception->getMessage());
        }
    }

    public function getRoutine()
    {
        $general = new ConfigurationController();
        $data = $general->getGeneralData(['days']);
        $authID = auth()->user()->school_id;
        try {

            $routines = [];
            $routineCounts = [];

            foreach ($data['days'] as $key => $day) {
                $routine = Routine::where('school_id', $authID)->where('day', $key)
                    ->where('session_year_id', students('session_year_id'))
                    ->where('class_id', students('class_id'))
                    ->with('subject:id,name', 'teacher:id,name', 'classRoom:id,name', 'class:id,name', 'building:id,name', 'sessions:id,title', 'sections:id,name',)
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
            return returnData(5000, $exception->getMessage(), $exception->getMessage());
        }
    }

    public function getExamRoutine()
    {
        $schoolID = auth()->user()->school_id;

        try {
            $data = Examination::with('class:id,name', 'session:id,title')
                ->where('school_id', $schoolID)
                ->where('session_year_id', students('session_year_id'))
                ->where('class_id', students('class_id'))
                ->paginate(request()->input('perPage'));
            return returnData(2000, $data);
        } catch (\Exception $exception) {
            return returnData(5000, $exception->getMessage(), $exception->getMessage());
        }
    }

    public function getResult()
    {
        $exam_id = request()->input('exam_id');
        $schoolID = auth()->user()->school_id;
        $markComponentData = [];
        $examMarkDataData = [];

        $marksData = DB::table('manage_marks')
            ->leftJoin('students', 'manage_marks.student_id', '=', 'students.id')
            ->where('manage_marks.school_id', $schoolID)
            ->where('manage_marks.session_year_id', students('session_year_id'))
            ->where('students.id', students('id'))
            ->where('manage_marks.class_id', students('class_id'))
            ->where('manage_marks.exam_id', $exam_id)
            ->where('manage_marks.department_id', students('department_id'))
            ->select('manage_marks.exam_mark_data', 'manage_marks.total_mark', 'students.id as student_id', 'manage_marks.grade_number_id')
            ->first();

        if ($marksData) {
            $examMarkData = json_decode($marksData->exam_mark_data, true);

            $gradeNumbersData = DB::table('grade_numbers')
                ->where('id', $marksData->grade_number_id)
                ->select('pass_mark', 'mark_component')
                ->first();

            $passMarkData = json_decode($gradeNumbersData->pass_mark, true);
            $markComponents = json_decode($gradeNumbersData->mark_component, true);

            $allMarksPass = true;
            $failedComponents = [];
            foreach ($examMarkData as $index => $mark) {
                if (!isset($passMarkData[$index]) || $mark < $passMarkData[$index]) {
                    $allMarksPass = false;
                    $failedComponents[] = $markComponents[$index];
                }
            }

            if (is_array($markComponents)) {
                foreach ($markComponents as &$component) {
                    $component = [
                        'name' => $component,
                    ];
                }
            }
            if (is_array($examMarkData)) {
                foreach ($examMarkData as &$component) {
                    $component = [
                        'name' => $component,
                    ];
                }
            }
            $markComponentData = $markComponents;
            $examMarkDataData = $examMarkData;

            $query = DB::table('manage_marks')
                ->leftJoin('students', 'manage_marks.student_id', '=', 'students.id')
                ->leftJoin('schools', 'manage_marks.school_id', '=', 'schools.id')
                ->leftJoin('session_years', 'manage_marks.session_year_id', '=', 'session_years.id')
                ->leftJoin('subjects', 'manage_marks.subject_id', '=', 'subjects.id')
                ->leftJoin('student_classes', 'manage_marks.class_id', '=', 'student_classes.id')
                ->leftJoin('examinations', 'manage_marks.exam_id', '=', 'examinations.id')
                ->leftJoin('grade_numbers', 'manage_marks.grade_number_id', '=', 'grade_numbers.id')
                ->leftJoin('departments', 'manage_marks.department_id', '=', 'departments.id')
                ->leftJoin('exam_grades', function ($join) {
                    $join->on('grade_numbers.id', '=', 'exam_grades.grade_number_id')
                        ->whereRaw('manage_marks.total_mark >= exam_grades.mark_from')
                        ->whereRaw('manage_marks.total_mark <= exam_grades.mark_to');
                })
                ->select(
                    'manage_marks.*',
                    'students.student_name_en as student_name',
                    'students.student_roll as student_roll',
                    'schools.title as school_name',
                    'schools.institute_code as ein_name',
                    'schools.address as address',
                    'student_classes.name as class_name',
                    'session_years.title as session_name',
                    'subjects.name as subject_name',
                    'examinations.name as exam_name',
                    'departments.department_name as department_name',
                    'grade_numbers.grade_number as grade_number',
                    DB::raw($allMarksPass ? "exam_grades.grade_name" : "'F' as grade_name"),
                    DB::raw($allMarksPass ? "exam_grades.grade_point" : "'F' as grade_point"),
                    DB::raw($allMarksPass ? "'Pass' as exam_status" : "'Fail' as exam_status"),
                    DB::raw($allMarksPass ? "'' as failed_components" : "'" . implode(", ", $failedComponents) . "' as failed_components")
                )
                ->where('manage_marks.school_id', $schoolID)
                ->where('manage_marks.session_year_id', students('session_year_id'))
                ->where('manage_marks.class_id', students('class_id'))
                ->where('students.id', students('id'))
                ->where('manage_marks.department_id', students('department_id'))
                ->where('manage_marks.exam_id', $exam_id);

            $data = $query->get();
        } else {
            $data = collect();
        }

        $single = $data->first();
        $responseData = [
            'data' => $data,
            'component' => $markComponentData,
            'examMarkData' => $examMarkDataData,
            'student_name' => $single ? $single->student_name : '',
            'student_roll' => $single ? $single->student_roll : '',
            'class_name' => $single ? $single->class_name : '',
            'session_name' => $single ? $single->session_name : '',
            'department_name' => $single ? $single->department_name : '',
            'school_name' => $single ? $single->school_name : '',
            'ein_name' => $single ? $single->ein_name : '',
            'address' => $single ? $single->address : '',
            'exam_status' => $single ? $single->exam_status : '',
            'failed_components' => $single && $single->exam_status != 'Pass' ? $single->failed_components : '',
        ];

        return returnData(2000, $responseData);
    }

    public function getPayments(Request $request, $student_id = null)
    {
        try {
            $month = $request->input('month');
            $year = $request->input('year');
            $authId = $student_id ?: auth()->user()->id;
            $schoolID = auth()->user()->school_id;

            $studentId = Student::where('user_id', $authId)->value('id');

            $data['data'] = DB::table('invoices')
                ->leftJoin('students', 'invoices.invoice_type_id', '=', 'students.id')
                ->leftJoin('schools', 'invoices.school_id', '=', 'schools.id')
                ->leftJoin('session_years', 'students.session_year_id', '=', 'session_years.id')
                ->leftJoin('student_classes', 'students.class_id', '=', 'student_classes.id')
                ->leftJoin(
                    DB::raw('(SELECT invoice_id, SUM(total_payed) as total_payed, MAX(date) as last_payment_date 
                        FROM payments 
                        WHERE paid_by_type = 1
                        GROUP BY invoice_id) as payments'),
                    'invoices.id',
                    '=',
                    'payments.invoice_id'
                )
                ->select(
                    'invoices.*',
                    'schools.title as school_name',
                    'students.student_name_en as student_name',
                    'session_years.title as session_name',
                    'student_classes.name as class_name',
                    'payments.last_payment_date as payment_date',
                    DB::raw('IFNULL(payments.total_payed, 0) as total_payed'),
                    DB::raw('invoices.total_payable - IFNULL(payments.total_payed, 0) as due_amount')
                )
                ->where('invoices.invoice_type', 1)
                ->where('invoices.school_id', $schoolID)
                ->where('invoices.invoice_type_id', $studentId)
                ->when($month, function ($q) use ($month) {
                    return $q->whereMonth('invoices.date', $month);
                })
                ->when($year, function ($q) use ($year) {
                    return $q->whereYear('invoices.date', $year);
                })
                ->groupBy(
                    'invoices.id',
                    'schools.title',
                    'students.student_name_en',
                    'session_years.title',
                    'student_classes.name',
                    'payments.total_payed',
                    'payments.last_payment_date'
                )
                ->orderBy('invoices.date', 'desc')
                ->get();

            $data['option'] = (object) [
                'total_payable' => $data['data']->sum('total_payable'),
                'total_payed' => $data['data']->sum('total_payed'),
                'total_due' => $data['data']->sum('due_amount'),
            ];

            return returnData(2000, $data);
        } catch (\Exception $exception) {
            return returnData(5000, $exception->getMessage(), $exception->getMessage());
        }
    }

    public function getCanteenMeal(Request $request)
    {
        $month = $request->input('month');
        $year = $request->input('year');
        $memberId = auth()->user()->id;
        $memberType = 1;
        $studentId = Student::where('user_id', $memberId)->first()->id;

        if (!$month || !$year) {
            return returnData(3000, null, 'Month, Year, Member and Member Type are required.');
        }

        $mealTimes = DB::table('meal_times')
            ->where('status', 1)
            ->pluck('name', 'id')
            ->toArray();

        $sales = DB::table('canteen_daily_sales as cds')
            ->select(
                'cds.date',
                'cds.meal_time_id',
                DB::raw('SUM(cds.amount) as total_amount')
            )
            ->where('cds.is_canteen_member', 1)
            ->where('cds.sale_type', 2)
            ->where('cds.member_id', $studentId)
            ->where('cds.member_type', $memberType)
            ->whereMonth('cds.date', $month)
            ->whereYear('cds.date', $year)
            ->groupBy('cds.date', 'cds.meal_time_id')
            ->orderBy('cds.date')
            ->get();

        if ($sales->isEmpty()) {
            return returnData(5000, null, 'No daily ledger found for this member.');
        }

        $formatted = [];
        foreach ($sales as $row) {
            $date = $row->date;
            $mealName = $mealTimes[$row->meal_time_id] ?? 'Unknown';

            if (!isset($formatted[$date])) {
                $formatted[$date] = ['date' => $date, 'total_amount' => 0];

                foreach ($mealTimes as $name) {
                    $formatted[$date][$name] = 0;
                }
            }

            $formatted[$date][$mealName] += $row->total_amount;
            $formatted[$date]['total_amount'] += $row->total_amount;
        }

        $memberInfo = null;
        if ($memberType == 1) {
            $memberInfo = DB::table('students as s')
                ->leftJoin('student_classes as sc', 's.class_id', '=', 'sc.id')
                ->where('s.id', $studentId)
                ->select(
                    's.student_name_en as name',
                    's.student_roll as roll',
                    's.id as member_id',
                    'sc.name as class_name'
                )->first();
        }

        return returnData(2000, [
            'member_info' => $memberInfo,
            'meal_columns' => array_values($mealTimes),
            'rows' => array_values($formatted),
            'month' => Carbon::createFromDate(null, $month)->format('F'),
            'year' => $year,
            'print_date' => now()->format('Y-m-d')
        ], 'Daily member ledger data retrieved.');
    }

    public function getProfileData()
    {
        $schoolID = auth()->user();

        try {
            $data = User::leftJoin('students', 'users.id', '=', 'students.user_id')
                ->where('users.school_id', $schoolID->school_id)
                ->where('users.id', $schoolID->id)
                ->select(
                    'users.*',
                    'students.father_phone as father_phone',
                    'students.photo as photo'
                )
                ->first();

            return returnData(2000, $data);
        } catch (\Exception $exception) {
            return returnData(5000, $exception->getMessage(), $exception->getMessage());
        }
    }

    public function getUpdateProfile(Request $request)
    {
        $user = Auth::user();
        $student = Student::where('user_id', $user->id)->first();
        if ($user->type == 5) {
            $student->student_phone = $request->phone;
            $student->photo = $request->image;
            $student->student_name_en = $request->username;

            $student->save();

            $user->username = $request->input('username');
            $user->phone = $request->input('phone');
            $user->email = $request->input('email');
            $user->layout = $request->input('layout');
            $user->image = $request->input('image');
            $user->save();
            if ($request->input('password')) {
                if (!Hash::check($request->old_password, $user->password)) {
                    return returnData(3000, null, 'Old password is incorrect');
                }
                $user->password = Hash::make($request->password);
            }
            $user->save();
            return returnData(2000, null, 'Successfully Updated');
        }

        return returnData(2000, null, 'Successfully Updated');
    }

    public function getLibrary()
    {
        $schoolID = auth()->user()->school_id;
        try {
            $data = DB::table('book_issues')
                ->leftJoin('book_issues_details', 'book_issues.id', '=', 'book_issues_details.issue_id')
                ->leftJoin('book_accessions', 'book_issues_details.book_accession_id', '=', 'book_accessions.id')
                ->selectRaw('book_issues.*,book_issues_details.*,book_accessions.title as book_name')
                ->where('book_issues.school_id', $schoolID)
                ->where('book_issues.student_id', students('id'))
                ->paginate(request()->input('perPage'));
            return returnData(2000, $data);
        } catch (\Exception $exception) {
            return returnData(5000, $exception->getMessage(), $exception->getMessage());
        }
    }
}

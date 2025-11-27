<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use App\Models\Staff;
use App\Models\Module;
use App\Models\School;
use App\Helpers\Helper;
use App\Models\Invoice;
use App\Models\Parents;
use App\Models\Payment;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Building;
use App\Models\ExamName;
use App\Models\FeesType;
use App\Models\ClassRoom;
use App\Models\Component;
use App\Models\Department;
use App\Models\Permission;
use App\Models\Examination;
use App\Models\GradeNumber;
use App\Models\SessionYear;
use App\Models\StudentClass;
use App\Models\SubjectGroup;
use Illuminate\Http\Request;
use App\Models\Configuration;
use App\Models\ExamComponent;
use App\Models\LeaveCategory;
use App\Models\ComponentGroup;
use App\Models\InvoiceDetails;
use App\Models\PaymentDetails;
use App\Models\Accounting\Bank;
use App\Models\ManageDormitory;
use App\Models\ApprisalTemplate;
use App\Models\Canteen\MealTime;
use App\Models\Employee\Employee;
use App\Models\Library\BookAuthor;
use App\Models\Library\Membership;
use App\Models\Library\StockBooks;
use App\Models\Store\StoreProduct;
use Illuminate\Support\Facades\DB;
use App\Models\Library\BookEdition;
use App\Models\Store\StoreCategory;
use App\Http\Controllers\Controller;
use App\Models\Library\BookLanguage;
use Illuminate\Support\Facades\Auth;
use App\Models\Library\BookAccession;
use App\Models\Library\BookPublisher;
use App\Models\Website\CourseCategory;
use App\Models\Canteen\CanteenMenuItem;
use App\Models\Transport\TransportRoute;
use App\Models\Canteen\CanteenMenuRoster;
use App\Models\Transport\TransportManage;
use App\Models\Employee\EmployeeDepartment;
use App\Models\Employee\EmployeeDesignation;

class ConfigurationController extends Controller
{
    use Helper;

    public function __construct()
    {
        $this->model = new Configuration();
    }

    public function getConfigurations()
    {
        $role_id = auth()->user()->role_id;
        $user_id = auth()->user()->id;

        $school_id = request()->input('school_id');

        $data['school'] = [];
        $data['user'] = User::where('id', $user_id)->first();

        $data['config'] = configs(['image', 'name']);
        $data['image'] = null;

        $permissions = Permission::whereHas('role_permissions', function ($query) use ($role_id) {
            $query->where('role_id', $role_id);
        })->get();

        $permittedModules = collect($permissions)->pluck('module_id');
        $data['permissions'] = collect($permissions)->pluck('name');
        $data['institutes'] = School::where('status', 1)->get();
        $data['school_id'] = $school_id;

        $data['menus'] = Module::where('parent_id', 0)
            ->whereIn('id', $permittedModules)
            ->with([
                'submenus' => function ($query) use ($permittedModules) {
                    $query->with('submenus');
                    $query->whereIn('id', $permittedModules);
                }
            ])->get();

        return returnData(2000, $data);
    }

    public function getGeneralData($requestArray = false)
    {
        $array = $requestArray ? $requestArray : request()->all();
        $user = auth()->user();
        $data = [];

        if (in_array('authLoginUser', $array)) {
            $data['authLoginUser'] = User::where('users.id', $user->id)->where('users.status', 1)
                ->leftJoin('roles', 'users.role_id', '=', 'roles.id')
                ->select('users.*', 'roles.id', 'roles.display_name as role_name')
                ->first();
        }

        if (in_array('days', $array)) {
            $data['days'] = [
                'saturday' => 'Saturday',
                'sunday' => 'Sunday',
                'monday' => 'Monday',
                'tuesday' => 'Tuesday',
                'wednesday' => 'Wednesday',
                'thursday' => 'Thursday',
            ];
        }

        if (in_array('library_member', $array)) {
            $memberships = DB::table('memberships')
                ->where('memberships.school_id', $user->school_id)
                ->get();

            foreach ($memberships as $m) {
                $m->student_name = null;
                $m->student_roll = null;
                $m->teacher_name = null;
                $m->employee_id = null;

                if ($m->type == 1) { // student
                    $student = DB::table('students')->where('id', $m->member_id)->first();
                    if ($student) {
                        $m->student_name = $student->student_name_en;
                        $m->student_roll = $student->student_roll;
                    }
                } elseif ($m->type == 2) { // teacher
                    $teacher = DB::table('employees')->where('id', $m->member_id)->first();
                    if ($teacher) {
                        $m->teacher_name = $teacher->name;
                        $m->employee_id = $teacher->employee_id;
                    }
                }
            }

            $data['library_member'] = $memberships;
        }

        if (in_array('student_library_member', $array)) {
            $data['student_library_member'] = Membership::where('type', 1)->where('school_id', $user->school_id)->get();
        }
        if (in_array('teacher_library_member', $array)) {
            $data['teacher_library_member'] = Membership::where('type', 2)->where('school_id', $user->school_id)->get();
        }
        if (in_array('books', $array)) {
            $data['books'] = BookAccession::where('status', 1)->where('school_id', $user->school_id)->get();
        }
        if (in_array('stocked_books', $array)) {
            $data['stocked_books'] = StockBooks::where('stock_books.status', 1)
                ->where('stock_books.school_id', $user->school_id)
                // ->where('stock_books.available_quantity', '>', 0)
                ->leftJoin('book_accessions', 'stock_books.book_accession_id', '=', 'book_accessions.id')
                ->select('stock_books.*', 'book_accessions.book_title', 'book_accessions.id as b_id')
                ->get();
        }

        if (in_array('religion', $array)) {
            $data['religion'] = [
                'Islam' => 'Islam',
                'Hinduism' => 'Hinduism',
                'Christianity' => 'Christianity',
                'Buddhism' => 'Buddhism',
                'Judaism' => 'Judaism',
                'Sikhism' => 'Sikhism',
                'Others' => 'Others',
            ];
        }

        if (in_array('gender', $array)) {
            $data['gender'] = [
                'Male' => 'Male',
                'Female' => 'Female',
                'Others' => 'Others',
            ];
        }

        if (in_array('quota', $array)) {
            $data['quota'] = [
                'Freedom Fighter' => 'Freedom Fighter',
                'Physically Challenged' => 'Physically Challenged',
                'Tribe' => 'Tribe',
                'None Quota' => 'None Quota',
            ];
        }

        if (in_array('componentName', $array)) {
            $data['componentName'] = Component::where('status', 1)->where('component_type',1)->where('school_id', $user->school_id)->get();
        }

        if (in_array('exam_component', $array)) {
            $data['exam_component'] = ExamComponent::where('status', 1)->where('school_id', $user->school_id)->get();
        }

        if (in_array('books_author', $array)) {
            $data['books_author'] = BookAuthor::where('status', 1)->where('school_id', $user->school_id)->get();
        }
        if (in_array('books_edition', $array)) {
            $data['books_edition'] = BookEdition::where('status', 1)->where('school_id', $user->school_id)->get();
        }
        if (in_array('books_language', $array)) {
            $data['books_language'] = BookLanguage::where('status', 1)->where('school_id', $user->school_id)->get();
        }
        if (in_array('books_publishers', $array)) {
            $data['books_publishers'] = BookPublisher::where('status', 1)->where('school_id', $user->school_id)->get();
        }
        if (in_array('routes', $array)) {
            $data['routes'] = TransportRoute::where('status', 1)->where('school_id', $user->school_id)->get();
        }
        if (in_array('transports', $array)) {
            $data['transports'] = TransportManage::where('transport_manages.status', 1)
                ->leftJoin('transport_routes', 'transport_manages.route_id', '=', 'transport_routes.id')
                ->select('transport_manages.*', 'transport_routes.route_name', 'transport_routes.route_fare')
                ->where('transport_manages.school_id', $user->school_id)->get();
        }

        if (in_array('module', $array)) {
            $data['module'] = Module::where('parent_id', 0)->get();
        }
        if (in_array('designation', $array)) {
            $data['designation'] = EmployeeDesignation::where('status', 1)->where('school_id', $user->school_id)->get();
        }

        if (in_array('leaveCategory', $array)) {
            $data['leaveCategory'] = LeaveCategory::where('status', 1)->where('school_id', $user->school_id)->get();
        }
        if (in_array('staff', $array)) {
            $data['staff'] = Staff::where('status', 1)->where('school_id', $user->school_id)->get();
        }
        if (in_array('fees_typee', $array)) {
            $data['fees_typee'] = FeesType::where('status', 1)->get();
        }
        if (in_array('fees_types', $array) || isset($array['fees_types'])) {
            $data['fees_types'] = FeesType::selectRaw("fees_types.name, fees_types.id,fees_masters.amount, 0 as paid, 0 as checked, fees_masters.id as fess_master_id")
                ->where('fees_masters.school_id', auth()->user()->school_id)
                ->join('fees_masters', 'fees_types.id', '=', 'fees_masters.type')
                ->where(function ($query) use ($array) {
                    if (isset($array['fees_types']['class_id'])) {
                        $query->where('class_id', $array['fees_types']['class_id']);
                    }
                    if (isset($array['fees_types']['section_id'])) {
                        $query->where('section_id', $array['fees_types']['section_id']);
                    }
                    if (isset($array['fees_types']['department_id'])) {
                        $query->where('department_id', $array['fees_types']['department_id']);
                    }
                    if (isset($array['fees_types']['session_year_id'])) {
                        $query->where('session_year_id', $array['fees_types']['session_year_id']);
                    }
                })
                ->get();
        }

        if (in_array('examName', $array)) {
            $data['examName'] = ExamName::where('status', 1)
                ->where('school_id', $user->school_id)
                ->get();
        }

        if (in_array('examinationType', $array) || isset($array['examinationType'])) {
            $query = Examination::where('status', 1);
            if (isset($array['examinationType']['exam_id']) && $array['examinationType']['exam_id']) {
                $query->where('exam_id', $array['examinationType']['exam_id']);
            }
            if (isset($array['examClass']['class_id']) && $array['examClass']['class_id']) {
                $query->where('class_id', $array['examClass']['class_id']);
            }
            $data['examinationType'] = $query->get();
        }

        if (in_array('examGradeNumber', $array)) {
            $data['examGradeNumber'] = GradeNumber::where('status', 1)->where('school_id', $user->school_id)->get();
        }

        if (in_array('employeeDepartment', $array)) {
            $data['employeeDepartment'] = EmployeeDepartment::where('status', 1)->where('school_id', $user->school_id)->get();
        }
        if (in_array('teachers', $array)) {
            $data['teachers'] = Employee::where('status', 1)->where('employee_type', 1)->where('school_id', $user->school_id)->get();
        }
        if (in_array('staffs', $array)) {
            $data['staffs'] = Staff::where('status', 1)->where('school_id', $user->school_id)->get();
        }
        if (in_array('apprisalTemplte', $array)) {
            $data['apprisalTemplte'] = ApprisalTemplate::where('status', 1)->where('school_id', $user->school_id)->get();
        }
        if (in_array('building', $array)) {
            $data['building'] = Building::where('status', 1)->where('school_id', $user->school_id)->get();
        }
        if (in_array('canteenComponent', $array)) {
            $data['canteenComponent'] = Component::where('status', 1)->where('component_type', 2)->where('school_id', $user->school_id)->get();
        }
        if (in_array('dormitoryComponent', $array)) {
            $data['dormitoryComponent'] = Component::where('status', 1)->where('component_type', 3)->where('school_id', $user->school_id)->get();
        }
        if (in_array('transportComponent', $array)) {
            $data['transportComponent'] = Component::where('status', 1)->where('component_type', 4)->where('school_id', $user->school_id)->get();
        }
        if (in_array('componentGroup', $array) || isset($array['componentGroup'])) {
            $data['componentGroup'] = ComponentGroup::where('status', 1)
                ->where(function ($query) use ($array) {
                    if (isset($array['componentGroup']['school_id'])) {
                        $query->where('school_id', $array['componentGroup']['school_id']);
                    }
                })->get();
        }
        if (in_array('session', $array)) {
            $data['session'] = SessionYear::where('status', 1)->where('school_id', $user->school_id)->orderBy('id', 'desc')->get();
        }
        if (in_array('bank', $array)) {
            $data['bank'] = Bank::where('status', 1)->where('school_id', $user->school_id)->get();
        }

        if (in_array('class', $array) || isset($array['class'])) {
            $data['class'] = StudentClass::where('status', 1)->where('status', 1)->where(function ($query) use ($array) {
                if (isset($array['class']['school_id'])) {
                    $query->where('school_id', $array['class']['school_id']);
                }
            })->get();
        }

        if (in_array('students', $array) || isset($array['students'])) {
            $key = isset($array['students']['objectName']) ? $array['students']['objectName'] : 'students';

            $data[$key] = Student::where(function ($query) use ($array) {
                if (isset($array['students']['school_id'])) {
                    $query->where('school_id', $array['students']['school_id']);
                }
                if (isset($array['students']['class_id'])) {
                    $query->where('class_id', $array['students']['class_id']);
                }
            })->where('status', 1)->get();
        }
        // student payment invoice
        if (in_array('invoice', $array) || isset($array['invoice'])) {
            $data['invoice'] = Invoice::where(function ($query) use ($array) {
                if (isset($array['invoice']['invoice_type_id'])) {
                    $query->where('invoice_type_id', $array['invoice']['invoice_type_id']);
                }
            })->orderBy('id', 'desc')->get();
        }

        if (in_array('class', $array) || isset($array['class'])) {
            $data['class'] = StudentClass::where(function ($query) use ($array) {
                if (isset($array['class']['school_id'])) {
                    $query->where('school_id', $array['class']['school_id']);
                }
            })->get();
        }

        // account section student class
        if (in_array('account_student_class', $array) || isset($array['account_student_class'])) {
            $data['account_student_class'] = StudentClass::where(function ($query) use ($array) {
                if (isset($array['class']['school_id'])) {
                    $query->where('school_id', $array['account_student_class']['school_id']);
                }
            })
                ->whereNotIn('id', [29, 30, 31])
                ->get();
        }

        if (in_array('section', $array) || isset($array['section'])) {
            $key = isset($array['section']['objectName']) ? $array['section']['objectName'] : 'section';
            $data[$key] = DB::table('sections')
                ->where(function ($query) use ($array) {
                    if (isset($array['section']['class_id'])) {
                        $query->where('class_id', $array['section']['class_id']);
                    }
                })->get();
        }

        // new Section/class
        if (in_array('new_class', $array) || isset($array['new_class'])) {
            $data['new_class'] = StudentClass::where(function ($query) use ($array) {
                if (isset($array['new_class']['school_id'])) {
                    $query->where('school_id', $array['new_class']['school_id']);
                }
            })->get();
        }

        if (in_array('new_section', $array) || isset($array['new_section'])) {
            $key = isset($array['new_section']['objectName']) ? $array['new_section']['objectName'] : 'new_section';
            $data[$key] = DB::table('sections')
                ->where(function ($query) use ($array) {
                    if (isset($array['new_section']['class_id'])) {
                        $query->where('class_id', $array['new_section']['class_id']);
                    }
                })->get();
        }


        if (in_array('groupSubject', $array)) {
            $data['groupSubject'] = SubjectGroup::where('status', 1)->select('id', 'name')->get();
        }

        if (in_array('subjects', $array) || isset($array['subjects'])) {
            $data['subjects'] = Subject::where(function ($query) use ($array) {
                if (isset($array['subjects']['department_id'])) {
                    $query->where('department_id', $array['subjects']['department_id']);
                }
            })->get();
        }

        // section wise subject in manage mark
        if (in_array('section_subjects', $array) || isset($array['section_subjects'])) {
            $query = Subject::query();

            if (!empty($array['section_subjects']['section_id'])) {
                $query->where('section_id', $array['section_subjects']['section_id']);
            }

            if (!empty($array['department_subjects']['department_id'])) {
                $query->where('department_id', $array['department_subjects']['department_id']);
            }

            $data['section_subjects'] = $query->get();
        }

        // manage mark component teacher wise subject show
        if (in_array('manage_mark_subjects', $array) || isset($array['manage_mark_subjects'])) {
            $authUserId = auth()->user()->id;
            $authTeacher = Employee::where('user_id', $authUserId)->select('id', 'name', 'user_id')->first();

            if ($authTeacher) {
                $query = Subject::where('teacher_id', $authTeacher->id);

                if (!empty($array['manage_mark_subjects']['section_id'])) {
                    $query->where('section_id', $array['manage_mark_subjects']['section_id']);
                }

                if (!empty($array['manage_mark_subjects']['id'])) {
                    $query->orWhere('id', $array['manage_mark_subjects']['id']);
                }

                if (!empty($array['department_subjects']['department_id'])) {
                    $query->where('department_id', $array['department_subjects']['department_id']);
                }

                $data['manage_mark_subjects'] = $query->get();
            } else {
                $data['manage_mark_subjects'] = collect();
            }
        }

        if (in_array('departments', $array) || isset($array['departments'])) {
            $data['departments'] = Department::where(function ($query) use ($array) {
                if (isset($array['departments']['class_id'])) {
                    $query->where('class_id', $array['departments']['class_id']);
                }
            })->get();
        }
        if (in_array('classRoom', $array) || isset($array['classRoom'])) {
            $data['classRoom'] = ClassRoom::where(function ($query) use ($array) {
                if (isset($array['classRoom']['building_id'])) {
                    $query->where('building_id', $array['classRoom']['building_id']);
                }
            })->get();
        }

        if (in_array('school', $array)) {
            $user = auth()->user();
            $data['school'] = isset($user->school_id) ? School::where('id', $user->school_id)->get() : School::get();
        }
        if (in_array('parent', $array)) {
            $user = auth()->user();
            $data['parent'] = isset($user->parent_id) ? Parents::where('id', $user->parent_id)->get() : Parents::get();
        }

        if (in_array('role', $array)) {
            $data['role'] = Role::where('status', 1)->get();
        }
        if (in_array('students', $array)) {
            $data['students'] = Student::where('status', 1)->select('id', 'student_name_en', 'student_roll', 'reg_number')->get();
        }

        if (in_array('department', $array)) {
            $data['department'] = Department::where('status', 1)->where('school_id', $user->school_id)->get();
        }
        if (in_array('website_course', $array)) {
            $data['website_course'] = CourseCategory::where('status', 1)->get();
        }

        if (in_array('store_category', $array)) {
            $data['store_category'] = StoreCategory::where('status', 1)->get();
        }
        if (in_array('product', $array)) {
            $data['product'] = StoreProduct::where('status', 1)->get();
        }

        if (in_array('date', $array)) {
            $data['date'] = date('Y-M-d');
        }

        if (in_array('division', $array) || isset($array['division'])) {
            $key = isset($array['division']['objectName']) ? $array['division']['objectName'] : 'division';
            $data[$key] = DB::table('divisions')->get();
        }

        if (in_array('district', $array) || isset($array['district'])) {
            $key = isset($array['district']['objectName']) ? $array['district']['objectName'] : 'district';
            $data[$key] = DB::table('districts')
                ->where(function ($query) use ($array) {
                    if (isset($array['district']['division_id'])) {
                        $query->where('division_id', $array['district']['division_id']);
                    }
                })->get();
        }
        if (in_array('upazila', $array) || isset($array['upazila'])) {
            $key = isset($array['upazila']['objectName']) ? $array['upazila']['objectName'] : 'upazila';
            $data[$key] = DB::table('upazilas')
                ->where(function ($query) use ($array) {
                    if (isset($array['upazila']['district_id'])) {
                        $query->where('district_id', $array['upazila']['district_id']);
                    }
                })->get();
        }

        if (in_array('union', $array) || isset($array['union'])) {
            $key = isset($array['union']['objectName']) ? $array['union']['objectName'] : 'union';
            $data[$key] = DB::table('unions')
                ->where(function ($query) use ($array) {
                    if (isset($array['union']['upazilla_id'])) {
                        $query->where('upazilla_id', $array['union']['upazilla_id']);
                    }
                })->get();
        }

        if (in_array('users', $array)) {
            if (auth()->user()->is_superadmin) {
                $data['users'] = User::where('status', 1)->get();
            } else {
                $data['users'] = User::where('id', auth()->user()->id)->where('status', 1)->get();
            }
        }
        if (in_array('dormitoryAuthor', $array)) {
            $data['dormitoryAuthor'] = Employee::where('status', 1)->where('school_id', $user->school_id)->get();
        }
        if (in_array('dormitory', $array)) {
            $data['dormitory'] = ManageDormitory::where('status', 1)->where('school_id', $user->school_id)->get();
        }

        // for daily collection report 
        if (in_array('collectBy', $array)) {
            $data['collectBy'] = DB::table('payments')->where('payments.school_id', $user->school_id)
                ->join('users', 'payments.created_by', '=', 'users.id')
                ->select('users.id', 'users.username')
                ->distinct()
                ->get();
        }

        // canteen menu setup
        if (in_array('mealTimes', $array)) {
            $data['mealTimes'] = MealTime::select('id', 'name')->where('status', 1)->get();
        }

        if (in_array('menuItem', $array)) {
            $data['menuItem'] = CanteenMenuItem::select('id', 'item_name')->get();
        }

        if ($requestArray) {
            return $data;
        }

        return returnData(2000, $data);
    }

    public function getGeneralDependencyData()
    {
        $array = request()->all();
    }

    public function getStudentDashboard()
    {
        try {
            $student = User::with(
                'student',
                'student.departments:id,department_name',
                'student.sessions:id,title',
                'student.sections:id,name',
                'student.classes:id,name',
                'student.address',
            )
                ->where('id', auth()->user()->id)->first();

            return returnData(2000, $student);
        } catch (\Exception $exception) {
            return response()->json(returnData(5000, $exception->getMessage(), 'Whoops, Something Went Wrong..!!'));
        }
    }

    public function index()
    {
        try {

            $keyword = request()->input('keyword');
            $datas = $this->model
                ->when($keyword, function ($query) use ($keyword) {
                    $query->where('display_name', 'Like', "%$keyword%");
                })
                ->paginate($this->perPage);
            return returnData(2000, $datas);
        } catch (\Exception $exception) {
            return response()->json(returnData(5000, $exception->getMessage(), 'Whoops, Something Went Wrong..!!'));
        }
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        try {
            $input = $request->all();
            $validate = $this->model->validate($input);
            if ($validate->fails()) {
                return returnData(2000, $validate->errors());
            }

            $this->model->fill($input);
            $this->model->save();

            return returnData(2000, $this->model, 'Successfully Inserted');
        } catch (\Exception $exception) {
            return returnData(5000, $exception->getMessage(), 'Whoops, Something Went Wrong..!!');
        }
    }

    public function show($id)
    {
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        try {
            $input = $request->all();

            $validate = $this->model->validate($input);
            if ($validate->fails()) {
                return returnData(2000, $validate->errors());
            }

            $data = $this->model->where('id', $input['id'])->first();

            if ($data) {
                $data->fill($input);
                $data->save();

                return returnData(2000, $this->model, 'Successfully Updated');
            }

            return returnData(5000, $this->model, 'Data not found');
        } catch (\Exception $exception) {
            return returnData(5000, $exception->getMessage(), 'Whoops, Something Went Wrong..!!');
        }
    }

    public function destroy($id)
    {
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

    public function getComponentBySchoolId(Request $request)
    {
        $school_id = auth()->user()->school_id;
        $data = Component::selectRaw("*, 0 as waiver, 0 as net_pay")->where('component_type', 1)->where('school_id', $school_id)->get();
        return returnData(2000, $data);
    }

    public function getComponentByGroupId(Request $request)
    {
        $school_id = auth()->user()->school_id;
        $group_id = $request->group_id;
        $data = Component::selectRaw("*, 0 as waiver, 0 as net_pay")->where('school_id', $school_id)->where('group_id', $group_id)->get();
        return returnData(2000, $data);
    }

    public function getStudent(Request $request)
    {
        $query = request()->input('student_roll');
        $paymentCategory = (int) request()->input('payment_category');
        $authSchoolId = (int) Auth::user()->school_id;

        $data = DB::table('students')
            ->leftJoin('student_classes', 'students.class_id', '=', 'student_classes.id')
            ->leftJoin('departments', 'students.department_id', '=', 'departments.id')
            ->leftJoin('sections', 'students.section_id', '=', 'sections.id')
            ->leftJoin('session_years', 'students.session_year_id', '=', 'session_years.id')
            ->leftJoin('schools', 'students.school_id', '=', 'schools.id')
            ->leftJoin('assign_canteens', function ($join) use ($authSchoolId) {
                $join->on('assign_canteens.consumer_id', '=', 'students.id')
                    ->where('assign_canteens.consumer_type', 1)
                    ->where('assign_canteens.school_id', $authSchoolId)
                    ->where('assign_canteens.status', 1);
            })
            ->select(
                'students.id',
                'students.student_name_en',
                'students.student_roll',
                'students.student_phone',
                'students.father_phone',
                'students.merit_roll',
                'student_classes.id as class_id',
                'student_classes.name as class_name',
                'departments.id as department_id',
                'departments.department_name',
                'sections.id as section_id',
                'sections.name as section_name',
                'session_years.id as session_id',
                'session_years.title as session_name',
                'schools.title as school_name',
                'schools.id as school_id',
                DB::raw('CASE WHEN assign_canteens.id IS NOT NULL THEN 1 ELSE 0 END as is_canteen_member')
            )
            ->where('students.status', 1)
            ->where('students.school_id', $authSchoolId)
            ->where(function ($q) use ($query) {
                $q->where('students.student_name_en', 'like', "%{$query}%")
                    ->orWhere('students.student_name_bn', 'like', "%{$query}%")
                    ->orWhere('students.student_roll', 'like', "%{$query}%");
            })
            ->orderBy('students.merit_roll', 'asc')
            ->get();

        if ($data->count() > 0) {
            return returnData(2000, $data, "Student details successfully retrieved.");
        }

        return returnData(5000, null, "Student details not retrieved.");
    }

    public function getCanteenMember(Request $request)
    {
        $authSchoolId = (int) Auth::user()->school_id;

        $students = DB::table('assign_canteens as ac')
            ->join('students as s', function ($join) {
                $join->on('ac.consumer_id', '=', 's.id')
                    ->where('ac.consumer_type', 1);
            })
            ->where('ac.status', 1)
            ->where('ac.school_id', $authSchoolId)
            ->select(
                'ac.id as assign_id',
                's.id as member_id',
                DB::raw('1 as consumer_type'),
                's.student_name_en as member_name',
                's.student_roll as member_code'
            );

        $employees = DB::table('assign_canteens as ac')
            ->join('employees as e', function ($join) {
                $join->on('ac.consumer_id', '=', 'e.id')
                    ->where('ac.consumer_type', 2);
            })
            ->where('ac.status', 1)
            ->where('ac.school_id', $authSchoolId)
            ->select(
                'ac.id as assign_id',
                'e.id as member_id',
                DB::raw('2 as consumer_type'),
                'e.name as member_name',
                'e.employee_id as member_code'
            );

        $data = DB::query()
            ->fromSub(function ($q) use ($students, $employees) {
                $q->from($students->unionAll($employees), 'tbl');
            }, 'final')
            ->orderBy('assign_id', 'desc')
            ->get();

        if ($data->count()) {
            return returnData(2000, $data, "Canteen member details retrieved successfully.");
        }

        return returnData(5000, null, "No canteen member found.");
    }

    public function getEmployee(Request $request)
    {
        $employeeId = request()->input('employee_id');
        $authSchoolId = (int) Auth::user()->school_id;

        $employees = DB::table('employees')
            ->leftJoin('employee_designations', 'employees.designation_id', '=', 'employee_designations.id')
            ->leftJoin('employee_departments', 'employees.department_id', '=', 'employee_departments.id')
            ->leftJoin('users', 'employees.user_id', '=', 'users.id')
            ->leftJoin('schools', 'employees.school_id', '=', 'schools.id')

            // Optional join to check canteen membership
            ->leftJoin('assign_canteens', function ($join) use ($authSchoolId) {
                $join->on('assign_canteens.consumer_id', '=', 'employees.id')
                    ->where('assign_canteens.consumer_type', 2) // 2 = Employee
                    ->where('assign_canteens.school_id', $authSchoolId)
                    ->where('assign_canteens.status', 1);
            })
            ->select(
                'employees.*',
                'employee_designations.name as designation',
                'employee_departments.name as department',
                'users.email',
                'schools.title as school_name',
                'schools.id as school_id',
                DB::raw("
                CASE 
                    WHEN employees.employee_type = 1 THEN 'teacher'
                    WHEN employees.employee_type = 2 THEN 'staff'
                    WHEN employees.employee_type = 3 THEN 'support_staff'
                    ELSE 'unknown'
                END as type
            "),
                DB::raw('CASE WHEN assign_canteens.id IS NOT NULL THEN 1 ELSE 0 END as is_canteen_member')
            )
            ->where('employees.status', 1)
            ->where('employees.school_id', $authSchoolId)
            ->where('employees.employee_id', 'LIKE', "{$employeeId}%")
            ->orderBy('employees.id', 'asc')
            ->get();

        if ($employees->count() > 0) {
            return returnData(2000, $employees, "Employee details successfully retrieved.");
        }

        return returnData(3000, null, "Employee not found.");
    }

    public function getInvoices(Request $request)
    {
        $userId = $request->user_id;
        $paymentFor = (int) $request->payment_for; // 1=student, 2=teacher, 3=staff
        $invoiceCategory = (int) $request->invoice_category;

        $categoryNames = [
            1 => 'Academic',
            2 => 'Canteen',
            3 => 'Dormitory',
            4 => 'Transport',
            5 => 'Canteen Cash'
        ];

        $categoryName = $categoryNames[$invoiceCategory] ?? 'Invoice';

        $typeLabel = match ($paymentFor) {
            1 => 'Student',
            2 => 'Teacher',
            3 => 'Staff',
            default => 'Consumer'
        };

        $invoices = DB::table('invoices as i')
            ->leftJoin(DB::raw('(SELECT invoice_id, SUM(total_payed) as total_paid
                             FROM payments GROUP BY invoice_id) as p'), 'p.invoice_id', '=', 'i.id')
            ->where('i.invoice_type', $paymentFor)
            ->where('i.invoice_type_id', $userId)
            ->where('i.invoice_category', $invoiceCategory)
            ->where(function ($q) {
                $q->whereNull('p.total_paid')
                    ->orWhereRaw('p.total_paid < i.total_payable');
            })
            ->select('i.id', 'i.invoice_code', 'i.total_payable', DB::raw('COALESCE(p.total_paid, 0) as total_paid'))
            ->get();

        if ($invoices->isEmpty()) {
            return returnData(3000, null, "{$typeLabel} has no {$categoryName} invoices.");
        }

        return returnData(2000, $invoices);
    }

    public function getUserWiseInvoice(Request $request)
    {
        $invoice_id = $request->invoice_id;
        $consumer_type = $request->payment_for;
        $invoice_type_id = $request->invoice_type_id;

        if (!$invoice_id) {
            return returnData(5000, null, 'Invoice ID is required.');
        }

        $invoice = DB::table('invoices')->where('id', $invoice_id)->first();
        if (!$invoice) {
            return returnData(3000, null, 'Invoice not found.');
        }

        if ((int) $invoice->invoice_type !== (int) $consumer_type) {
            return returnData(3000, null, 'Invalid invoice for this consumer.');
        }

        if ((int) $invoice->invoice_type_id != (int) $invoice_type_id) {
            return returnData(3000, null, 'Invalid consumer for this invoice.');
        }

        // Check if already paid
        $total_paid = DB::table('payments')->where('invoice_id', $invoice_id)->sum('total_payed');
        if ($invoice->total_payable == $total_paid) {
            return returnData(3000, null, 'Payment is already complete.');
        }

        $paidDetailsSubquery = DB::table('payment_details')
            ->select('component_id', DB::raw('SUM(payed) as total_payed'))
            ->join('payments', 'payment_details.payments_id', '=', 'payments.id')
            ->where('payments.invoice_id', $invoice_id)
            ->groupBy('component_id');

        $data = DB::table('invoice_details')
            ->select(
                'invoice_details.*',
                'components.name as component_name',
                DB::raw('COALESCE(payment_details.total_payed, 0) AS paid'),
                DB::raw('invoice_details.payable_amount - COALESCE(payment_details.total_payed, 0) AS payable_amount'),
                DB::raw('invoice_details.payable_amount - COALESCE(payment_details.total_payed, 0) AS actual_payable')
            )
            ->leftJoinSub($paidDetailsSubquery, 'payment_details', function ($join) {
                $join->on('invoice_details.components_id', '=', 'payment_details.component_id');
            })
            ->leftJoin('components', 'invoice_details.components_id', '=', 'components.id')
            ->where('invoice_details.invoice_id', $invoice_id)
            ->get();

        return returnData(2000, $data);
    }

    public function getCashInvoice()
    {
        try {
            $authSchoolId = auth()->user()->school_id;
            $invoices = Invoice::where('school_id', $authSchoolId)
                ->where('invoice_category', 5)
                ->get();

            $result = [];

            foreach ($invoices as $invoice) {
                $total_paid = Payment::where('invoice_id', $invoice->id)->sum('total_payed');
                if ($invoice->total_payable <= $total_paid) {
                    continue;
                }
                $components = InvoiceDetails::selectRaw("
                invoice_details.*,
                COALESCE(payment_details.total_payed, 0) AS paid,
                invoice_details.payable_amount - COALESCE(payment_details.total_payed, 0) AS payable_amount,
                invoice_details.payable_amount - COALESCE(payment_details.total_payed, 0) AS actual_payable
            ")
                    ->leftJoinSub(
                        PaymentDetails::selectRaw('component_id, SUM(payed) as total_payed')
                            ->join('payments', 'payment_details.payments_id', '=', 'payments.id')
                            ->where('payments.invoice_id', $invoice->id)
                            ->groupBy('component_id'),
                        'payment_details',
                        'invoice_details.components_id',
                        '=',
                        'payment_details.component_id'
                    )
                    ->where('invoice_details.invoice_id', $invoice->id)
                    ->with('components:id,name')
                    ->get();

                $result[] = [
                    'id' => $invoice->id,
                    'invoice_code' => $invoice->invoice_code,
                    'date' => $invoice->date,
                    'payment_type' => $invoice->payment_type ?? null,
                    'components' => $components
                ];
            }

            if (empty($result)) {
                return returnData(5000, null, 'No unpaid cash payment invoices found.');
            }

            return returnData(2000, $result);
        } catch (\Exception $e) {
            return returnData(5000, null, 'Something went wrong: ' . $e->getMessage());
        }
    }

    public function getStudentWiseInvoice(Request $request)
    {
        $invoice_id = $request->invoice_id;
        $invoice = Invoice::where('id', $invoice_id)->first();

        if ($invoice) {
            $total_paid = Payment::where('invoice_id', $invoice_id)->sum('total_payed');

            if ($invoice->total_payable == $total_paid) {
                return returnData(3000, null, 'Payment is already complete.');
            }

            $data = InvoiceDetails::selectRaw("
            invoice_details.*,
            COALESCE(payment_details.total_payed, 0) AS paid,
            invoice_details.payable_amount - COALESCE(payment_details.total_payed, 0) AS payable_amount,
            invoice_details.payable_amount - COALESCE(payment_details.total_payed, 0) AS actual_payable
        ")
                ->leftJoinSub(
                    PaymentDetails::selectRaw('component_id, SUM(payed) as total_payed')
                        ->join('payments', 'payment_details.payments_id', '=', 'payments.id')
                        ->where('payments.invoice_id', $invoice_id)
                        ->groupBy('component_id'),
                    'payment_details',
                    'invoice_details.components_id',
                    '=',
                    'payment_details.component_id'
                )
                ->where('invoice_details.invoice_id', $invoice_id)
                ->with('components:id,name')
                ->get();

            return returnData(2000, $data);
        } else {
            return returnData(5000, null, 'Invoice not found.');
        }
    }

    // include advance invoice logic
    public function classWiseStudent(Request $request)
    {
        $classId = $request->input('class_id');
        $school_id = auth()->user()->school_id;
        $student_id = $request->input('student_id');
        $sessionId = $request->input('session_year_id');
        $isAdvance = $request->input('is_advance_status');
        $month = $request->input('month');

        $invoiceStudentIds = [];

        $data['class_total_students'] = Student::where('school_id', $school_id)
            ->where('class_id', $classId)
            ->where('session_year_id', $sessionId)
            ->where('status', 1)
            ->count();

        if ($isAdvance == 'exclude' && $month) {
            $invoiceStudentIds = DB::table('invoices')
                ->where('month', $month)
                ->where('is_advance', 'advance')
                ->pluck('invoice_type_id')
                ->toArray();
        }

        $data['students'] = Student::selectRaw("*, 1 as checked")
            ->where('school_id', $school_id)
            ->when($student_id, function ($query) use ($student_id) {
                $query->where('id', $student_id);
            })
            ->where('class_id', $classId)
            ->where('session_year_id', $sessionId)
            ->where('status', 1)
            ->when($month && $isAdvance, function ($query) use ($invoiceStudentIds) {
                $query->whereNotIn('id', $invoiceStudentIds);
            })
            ->orderBy('student_roll', 'ASC')
            ->get();

        $data['student_count'] = count($data['students']);
        $data['studentIds'] = collect($data['students'])->pluck('id')->toArray();

        return returnData(2000, $data);
    }

    public function getWaiverStudentInvoice(Request $request)
    {
        $studentId = $request->input('student_id');

        $data = Component::selectRaw(
            'components.*,
            student_waivers.student_id as waiver_student_id,
            student_waivers.component_id as waiver_component_id,
            student_waivers.type as waiver_type,
            student_waivers.value as waiver_value'
        )
            ->leftJoin('student_waivers', function ($join) use ($studentId) {
                $join->on('components.id', '=', 'student_waivers.component_id')
                    ->where('student_waivers.student_id', '=', $studentId);
            })
            ->get();

        return returnData(2000, $data);
    }

    public function getMenuItems(Request $request)
    {
        $request->validate([
            'menu_date' => 'required|date',
            'meal_time_id' => 'required|integer',
        ]);

        $menuDate = $request->menu_date;
        $mealTimeId = $request->meal_time_id;

        $menuItems = CanteenMenuRoster::where('menu_date', $menuDate)
            ->where('meal_time_id', $mealTimeId)
            ->with(['menuItem:id,item_name'])
            ->get(['id', 'menu_item_id', 'price']);

        $formatted = $menuItems->map(function ($item) {
            return [
                'id' => $item->menu_item_id,
                'item_name' => $item->menuItem ? $item->menuItem->item_name : '',
                'price' => $item->price,
            ];
        });

        return response()->json([
            'status' => 2000,
            'menuItems' => $formatted
        ]);
    }
}

<?php

namespace App\Http\Controllers\Store;

use App\Models\Staff;

use App\Helpers\Helper;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Http\Request;
use App\Models\Store\StockOut;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Store\StockIn;

class StockOutController extends Controller
{
    use Helper;
    public function __construct()
    {
        $this->model = new StockOut();
    }

    public function index()
    {
        if (!can('stock_out_view')) {
            return $this->notPermitted();
        }

        try {
            $data = $this->model->with("product:id,name", "stock_in:id,product_id,product_code")
                ->when(input('keyword'), function ($query) {
                    $keyword = input('keyword');
                    $query->where('name', 'LIKE', "%$keyword%");
                })
                ->orderBy('id', 'DESC')
                ->paginate(request()->input('perPage'));

            foreach ($data as $item) {
                switch ($item->customer_type) {
                    case 'student':
                        $customer = Student::with('user:id,email')->find($item->customer_id);
                        $item->customer = $customer ? $customer->only(['student_roll', 'student_name_en', 'classes', 'departments']) + [
                            'email' => $customer->user->email ?? null,
                        ] : null;
                        break;
                    case 'teacher':
                        $customer = Teacher::with('user:id,email')->find($item->customer_id);
                        $item->customer = $customer ? $customer->only(['employee_id', 'name', 'department']) + [
                            'email' => $customer->user->email ?? null,
                            // 'phone' => $customer->user->profile->phone ?? null,
                        ] : null;
                        break;
                    case 'staff':
                        $customer = Staff::with('user:id,email')->find($item->customer_id);
                        $item->customer = $customer ? $customer->only(['employee_id', 'name', 'department']) + [
                            'email' => $customer->user->email ?? null,
                            // 'phone' => $customer->user->profile->phone ?? null,
                        ] : null;
                        break;
                    default:
                        $item->customer = null;
                        break;
                }
            }
            return returnData(2000, $data);
        } catch (\Exception $exception) {
            return returnData(5000, $exception->getMessage(), $exception->getMessage());
        }
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        if (!can('stock_out_view')) {
            return $this->notPermitted();
        }
        try {
            $schoolId = auth()->user()->school_id;
            $validate = $this->model->validate($request->all());
            if ($validate->fails()) {
                return returnData(3000, $validate->errors(), 'Validation Failed');
            }
            DB::beginTransaction();

            $this->model->fill($request->all());
            $this->model->school_id = $schoolId;
            $this->model->save();

            $stockIn = StockIn::where('id', $request->stock_id)
                ->where('school_id', $schoolId)
                ->first();

            $saleQuantitySum =  $stockIn->sale_quantity + $request->quantity;

            if ($stockIn) {
                $stockIn->sale_date = $request->sale_date;
                $stockIn->sale_quantity = $saleQuantitySum;
                $stockIn->save();
            }
            DB::commit();

            return returnData(2000, null, 'Successfully Inserted');
        } catch (\Exception $exception) {
            return returnData(5000, $exception->getMessage(), $exception->getMessage());
        }
    }

    public function show($id)
    {
        if (!can('stock_out_view')) {
            return $this->notPermitted();
        }
        $data = $this->model->with("product:id,name", "stock_in:id,product_id,product_code")->where('id', $id)->first();
        if ($data) {
            switch ($data->customer_type) {
                case 'student':
                    $data->customer = Student::find($data->customer_id);
                    break;
                case 'teacher':
                    $data->customer = Teacher::find($data->customer_id);
                    break;
                case 'staff':
                    $data->customer = Staff::find($data->customer_id);
                    break;
                default:
                    $data->customer = null;
                    break;
            }
        } else {
            return returnData(5000, 'Record not found');
        }
        return returnData(2000, $data);
    }

    public function edit($id)
    {
        try {
            $resultData = $this->model->with('product:id,name', 'stock_in:id,product_id,product_code,purchase_quantity,sale_quantity')
                ->where('id', $id)
                ->first();

            if (!$resultData) {
                return returnData(5000, null, 'Record not found');
            }
            switch ($resultData->customer_type) {
                case 'student':
                    $customer = Student::with('user:id,email')->find($resultData->customer_id);
                    $resultData->customer = $customer ? $customer->only(['student_roll', 'student_name_en', 'classes', 'departments', 'sections']) + [
                        'email' => $customer->user->email ?? null,
                        // 'phone' => $customer->user->profile->phone ?? null,
                    ] : null;
                    break;
                case 'teacher':
                    $customer = Teacher::with('user:id,email')->find($resultData->customer_id);
                    $resultData->customer = $customer ? $customer->only(['employee_id', 'name', 'department']) + [
                        'email' => $customer->user->email ?? null,
                        'phone' => $customer->user->profile->phone ?? null,
                    ] : null;
                    break;
                case 'staff':
                    $customer = Staff::with('user:id,email')->find($resultData->customer_id);
                    $resultData->customer = $customer ? $customer->only(['employee_id', 'name', 'department']) + [
                        'email' => $customer->user->email ?? null,
                        'phone' => $customer->user->profile->phone ?? null,
                    ] : null;
                    break;
                default:
                    $resultData->customer = null;
                    break;
            }
            return returnData(2000, $resultData);
        } catch (\Exception $exception) {
            return returnData(5000, $exception->getMessage(), $exception->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        if (!can('stock_out_update')) {
            return $this->notPermitted();
        }
        try {
            $validate = $this->model->validate($request->all());
            $schoolId = auth()->user()->school_id;

            if ($validate->fails()) {
                return returnData(3000, $validate->errors(), 'Validation Failed');
            }
            DB::beginTransaction();
            $data = $this->model->where('id', $request->input('id'))->first();
            $previousQuantity = $data->quantity;

            if ($data) {
                $data->fill($request->all());
                $data->school_id = $schoolId;
                $data->save();

                $stockIn = StockIn::where('id', $request->stock_id)
                    ->where('school_id', $schoolId)
                    ->first();

                if ($stockIn) {
                    $quantityDifference = ($request->quantity - $previousQuantity);
                    $newSaleQuantity = $stockIn->sale_quantity + $quantityDifference;

                    if ($newSaleQuantity > $stockIn->purchase_quantity) {
                        // DB::rollBack();
                        return returnData(5000, null, 'Sale quantity cannot exceed purchase quantity.');
                    }
                    $stockIn->sale_date = $request->sale_date;
                    $stockIn->sale_quantity = $newSaleQuantity;
                    $stockIn->save();
                }

                DB::commit();
                return returnData(2000, null, 'Successfully Updated');
            }

            return returnData(5000, null, 'Record Not Found');
        } catch (\Exception $exception) {
            // DB::rollBack();
            return returnData(5000, $exception->getMessage(), 'An error occurred');
        }
    }

    public function destroy($id)
    {
        if (!can('stock_out_delete')) {
            return $this->notPermitted();
        }
        $data = $this->model->where('id', $id)->first();
        if ($data) {
            $data->delete();

            return returnData(2000, null, 'Successfully Deleted');
        }
        return returnData(2000, [], 'Stock out not found');
    }
    public function getProduct()
    {
        $productCode = request()->input('product_code');
        $authSchoolId = auth()->user()->school_id;
        $product = StockIn::where('product_code', $productCode)->first();

        $purchaseQuantity = $product->purchase_quantity;
        $saleQuantity = $product->sale_quantity;

        if ($purchaseQuantity == $saleQuantity) {
            return returnData(5000, null, 'Product Is Not Available In Stock');
        }

        if ($product) {
            $data = DB::table('stock_ins')
                ->leftJoin('store_products', 'stock_ins.product_id', '=', 'store_products.id')
                ->select(
                    'stock_ins.id as stock_ins_id',
                    'store_products.id as store_products_id',
                    'stock_ins.product_code',
                    'stock_ins.purchase_date',
                    'stock_ins.purchase_price',
                    'stock_ins.sale_price',
                    'stock_ins.purchase_quantity',
                    DB::raw('(stock_ins.purchase_quantity - COALESCE(stock_ins.sale_quantity, 0)) as stock_quantity'),
                    'stock_ins.status',
                    'store_products.name as product_name'
                )
                ->where('stock_ins.status', 1)
                ->where('stock_ins.school_id', $authSchoolId)
                ->where('stock_ins.product_code', $productCode)
                ->first();

            return returnData(2000, $data, 'Product details successfully retrieved.');
        }
        return returnData(5000, null, 'Product details not retrieved');
    }

    public function getStudent()
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

            return returnData(2000, $data, "Student details successfully retrieved.");
        }
        return returnData(3000, null, "Student details not retrieved.");
    }

    public function getTeacher()
    {
        $empID = request()->input('teacher_id');
        $auth = auth()->user()->school_id;
        $teacher = Teacher::where('employee_id', $empID)->first();

        if ($teacher) {
            $data = DB::table('teachers')
                ->leftJoin('designations', 'teachers.designation_id', '=', 'designations.id')
                ->leftJoin('work_departments', 'teachers.department_id', '=', 'work_departments.id')
                ->leftJoin('users', 'teachers.user_id', '=', 'users.id')
                ->select(
                    'teachers.*',
                    'designations.name as designation',
                    'work_departments.name as department',
                    'users.email'
                )
                ->where('teachers.status', 1)
                ->where('teachers.school_id', $auth)
                ->where('teachers.employee_id', $empID)
                ->first();

            return returnData(2000, $data, "Teacher details successfully retrieved.");
        }
        return returnData(2000, null, "Teacher details not retrieved.");
    }

    public function getStaff()
    {
        $employeeId = request()->input('staff_id');
        $authSchoolId = auth()->user()->school_id;
        $staff = Staff::where('employee_id', $employeeId)->first();

        if ($staff) {
            $data = DB::table('staff')
                ->leftJoin('designations', 'staff.designation_id', '=', 'designations.id')
                ->leftJoin('work_departments', 'staff.department_id', '=', 'work_departments.id')
                ->leftJoin('users', 'staff.user_id', '=', 'users.id')
                ->select(
                    'staff.*',
                    'designations.name as designation',
                    'work_departments.name as department',
                    'users.email'
                )
                ->where('staff.status', 1)
                ->where('staff.school_id', $authSchoolId)
                ->where('staff.employee_id', $employeeId)
                ->first();
            return returnData(2000, $data, 'Staff details successfully retrieved.');
        }
        return returnData(5000, null, 'Staff details not retrieved');
    }
}

<?php

namespace App\Http\Controllers\Dormitory;

use App\Helpers\Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Dormitory\AssignDormitory;

class AssignDormitoryController extends Controller
{
    use Helper;

    public function __construct()
    {
        $this->model = new AssignDormitory();
    }

    public function index()
    {
        if (!can('assign_dormitory_view')) {
            return $this->notPermitted();
        }

        try {

            $dormitoryID = request()->input('dormitory_name');
            $memberType = request()->input('member_type');

            $data = $this->model->with([
                'dormitory:id,dormitory_name,dormitory_no',
                'creator:id,username',
                'updater:id,username'
            ])
                ->when(request('keyword'), function ($query) {
                    $keyword = request('keyword');
                    $query->whereHas('dormitory', function ($q) use ($keyword) {
                        $q->where('dormitory_name', 'LIKE', "%$keyword%");
                    });
                })
                ->when($dormitoryID, function ($query) use ($dormitoryID) {
                    $query->where('dormitory_id', $dormitoryID);
                }) 
                ->when($memberType, function ($query) use ($memberType) {
                    $query->where('dormitory_user_type', $memberType);
                })
                ->orderBy('id', 'DESC')
                ->paginate(request()->input('perPage'));

            foreach ($data as $item) {
                $item->dormitory_user = null;

                if ($item->dormitory_user_type == 1) {
                    $user = DB::table('students')->where('id', $item->dormitory_user_id)->first();
                    if ($user) {
                        $item->dormitory_user = [
                            'student_roll' => $user->student_roll,
                            'student_name_en' => $user->student_name_en,
                            'student_phone' => $user->student_phone,
                            'father_phone' => $user->father_phone,
                        ];
                    }
                } elseif ($item->dormitory_user_type == 2) {
                    $user = DB::table('employees')->where('id', $item->dormitory_user_id)->first();
                    if ($user) {
                        $item->dormitory_user = [
                            'employee_id' => $user->employee_id,
                            'name' => $user->name,
                            'phone' => $user->phone,
                            'employee_type' => $user->employee_type,
                        ];
                    }
                }
            }

            return returnData(2000, $data);
        } catch (\Exception $exception) {
            return returnData(5000, $exception->getMessage(), $exception->getMessage());
        }
    }

    public function store(Request $request)
    {
        if (!can('assign_dormitory_add')) {
            return $this->notPermitted();
        }

        try {
            $userId = auth()->user()->id;
            $authSchoolId = auth()->user()->school_id;
            $input = $request->all();
            if ($request->dormitory_user_type == 1) {
                $input['dormitory_user_id'] = $request->student_id ?? null;
                $input['dormitory_user_type'] = 1;
            } elseif ($request->dormitory_user_type == 2) {
                $input['dormitory_user_id'] = $request->employee_primary_id ?? null;
                $input['dormitory_user_type'] = 2;
            } else {
                return returnData(5000, null, 'Invalid Member Category');
            }

            $netAmount = (float) ($request->net_amount ?? 0);
            $discount = (float) ($request->discount ?? 0);
            $payableAmount = $netAmount - ($netAmount * $discount / 100);

            $input['payable_amount'] = round($payableAmount, 2);

            $validate = $this->model->validate($input);
            if ($validate->fails()) {
                return returnData(5000, $validate->errors(), 'Member Already Assigned or Seat Already Taken');
            }

            DB::beginTransaction();

            $this->model->fill($input);
            $this->model->created_by = $userId;
            $this->model->school_id = $authSchoolId;
            $this->model->save();

            DB::commit();

            return returnData(2000, null, 'Dormitory Assigned Successfully');
        } catch (\Exception $exception) {
            DB::rollBack();
            return returnData(5000, $exception->getMessage(), $exception->getMessage());
        }
    }

    public function edit($id)
    {
        try {
            $resultData = $this->model->with([
                'dormitory:id,dormitory_name,dormitory_no',
            ])
                ->where('id', $id)
                ->first();

            if (!$resultData) {
                return returnData(5000, null, 'Record not found');
            }

            $dormitoryUser = null;
            switch ($resultData->dormitory_user_type) {
                case 1:
                    $student = DB::table('students as s')
                        ->leftJoin('student_classes as c', 's.class_id', '=', 'c.id')
                        ->leftJoin('departments as d', 's.department_id', '=', 'd.id')
                        ->leftJoin('sections as sec', 's.section_id', '=', 'sec.id')
                        ->where('s.id', $resultData->dormitory_user_id)
                        ->select(
                            's.student_roll',
                            's.student_name_en',
                            's.student_phone',
                            'c.name as class_name',
                            'd.department_name',
                            'sec.name as section_name',
                        )
                        ->first();

                    if ($student) {
                        $dormitoryUser = [
                            'student_roll' => $student->student_roll,
                            'student_name_en' => $student->student_name_en,
                            'student_phone' => $student->student_phone,
                            'class_name' => $student->class_name,
                            'department_name' => $student->department_name,
                            'section_name' => $student->section_name,
                        ];
                    }
                    break;

                case 2:
                    $employee = DB::table('employees as e')
                        ->leftJoin('users as u', 'e.user_id', '=', 'u.id')
                        ->leftJoin('work_departments as d', 'e.department_id', '=', 'd.id')
                        ->leftJoin('designations as des', 'e.designation_id', '=', 'des.id')
                        ->where('e.id', $resultData->dormitory_user_id)
                        ->select(
                            'e.employee_id',
                            'e.name',
                            'e.phone',
                            'd.name as department_name',
                            'des.name as designation_name',
                            'u.email as email',
                        )
                        ->first();

                    if ($employee) {
                        $dormitoryUser = [
                            'employee_id' => $employee->employee_id,
                            'name' => $employee->name,
                            'phone' => $employee->phone,
                            'department_name' => $employee->department_name,
                            'designation_name' => $employee->designation_name,
                            'email' => $employee->email,
                        ];
                    }
                    break;

                default:
                    $dormitoryUser = null;
                    break;
            }

            $resultData->dormitory_user = $dormitoryUser;

            return returnData(2000, $resultData);
        } catch (\Exception $exception) {
            return returnData(5000, $exception->getMessage(), $exception->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        if (!can('assign_dormitory_update')) {
            return $this->notPermitted();
        }

        try {
            $userId = auth()->user()->id;
            $data = $this->model->find($id);
            if (!$data) {
                return returnData(5000, null, 'Data Not Found');
            }

            $input = $request->only([
                'dormitory_id',
                'floor_number',
                'room_number',
                'seat_number',
                'arrive_date',
                'discount',
                'net_amount',
                'description',
            ]);

            $input['dormitory_user_id'] = $data->dormitory_user_id;
            $input['dormitory_user_type'] = $data->dormitory_user_type;
            $input['school_id'] = $data->school_id;

            $netAmount = (float) ($request->net_amount ?? 0);
            $discount = (float) ($request->discount ?? 0);
            $input['payable_amount'] = round($netAmount - ($netAmount * $discount / 100), 2);

            $validate = $this->model->validate($input, $id);
            if ($validate->fails()) {
                return returnData(5000, $validate->errors(), 'Validation Failed');
            }

            DB::beginTransaction();

            $data->fill($input);
            $data->updated_by = $userId;
            $data->save();

            DB::commit();

            return returnData(2000, null, 'Dormitory Assignment Updated Successfully');
        } catch (\Exception $exception) {
            DB::rollBack();
            return returnData(5000, $exception->getMessage(), 'Whoops, Something Went Wrong..!!');
        }
    }

    public function destroy($id)
    {
        if (!can('assign_dormitory_delete')) {
            return $this->notPermitted();
        }

        try {
            $data = $this->model->find($id);

            if (!$data) {
                return returnData(5000, null, 'Data not found');
            }

            $hasInvoice = DB::table('invoices')
                ->where('invoice_category', 3)
                ->where('invoice_type', $data->dormitory_user_type)
                ->where('invoice_type_id', $data->dormitory_user_id)
                ->exists();

            if ($hasInvoice) {
                return returnData(5000, null, 'Cannot delete. Dormitory assignment has related monthly fees.');
            }

            $data->delete();
            return returnData(2000, $data, 'Successfully Deleted');

        } catch (\Exception $exception) {
            return returnData(5000, $exception->getMessage(), 'Whoops, Something Went Wrong..!!');
        }
    }
}

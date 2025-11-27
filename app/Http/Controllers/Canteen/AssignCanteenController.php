<?php

namespace App\Http\Controllers\Canteen;

use App\Models\Guest;
use App\Helpers\Helper;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\AssignCanteen;
use App\Http\Controllers\Controller;

class AssignCanteenController extends Controller
{
    use Helper;
    public function __construct()
    {
        $this->model = new AssignCanteen();
    }

    public function index()
    {
        if (!can('assign_canteen_view')) {
            return $this->notPermitted();
        }

        $keyword = input('keyword');
        $memberType = input('member_type');
        try {
            $data = $this->model
                ->when($keyword, function ($query) use ($keyword) {
                    $query->where(function ($q) use ($keyword) {
                        $q->orWhereIn('consumer_id', function ($sub) use ($keyword) {
                            $sub->select('id')
                                ->from('students')
                                ->where('student_roll', 'LIKE', "%{$keyword}%")
                                ->orWhere('student_name_en', 'LIKE', "%{$keyword}%");
                        });
                        $q->orWhereIn('consumer_id', function ($sub) use ($keyword) {
                            $sub->select('id')
                                ->from('employees')
                                ->where('employee_id', 'LIKE', "%{$keyword}%")
                                ->orWhere('name', 'LIKE', "%{$keyword}%");
                        });
                    });
                })
                 ->when($memberType, function ($query) use ($memberType) {
                    $query->where('consumer_type', $memberType);
                })
                ->orderBy('id', 'DESC')
                ->paginate(request()->input('perPage'));

            foreach ($data as $item) {
                switch ($item->consumer_type) {
                    case 1: // student
                        $consumer = DB::table('students')->where('id', $item->consumer_id)->first();
                        $item->consumer = $consumer ? [
                            'student_roll' => $consumer->student_roll,
                            'student_name_en' => $consumer->student_name_en,
                            'student_phone' => $consumer->student_phone,
                            'father_phone' => $consumer->father_phone,
                        ] : null;
                        break;

                    case 2: // Employee
                        $consumer = DB::table('employees')->where('id', $item->consumer_id)->first();
                        $item->consumer = $consumer ? [
                            'employee_id' => $consumer->employee_id,
                            'name' => $consumer->name,
                            'phone' => $consumer->phone,
                            'employee_type' => $consumer->employee_type,
                        ] : null;
                        break;
                    default:
                        $item->consumer = null;
                        break;
                }
            }

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
        if (!can('assign_canteen_add')) {
            return $this->notPermitted();
        }

        try {
            $userId = auth()->user()->id;
            $authSchoolId = auth()->user()->school_id;
            $input = $request->all();

            if ($request->consumer_type == 1) {
                $input['consumer_id'] = $request->student_id ?? null;
                $input['consumer_type'] = 1;
            } elseif ($request->consumer_type == 2) {
                $input['consumer_id'] = $request->employee_primary_id ?? null;
                $input['consumer_type'] = 2;
            } elseif ($request->consumer_type == 3) {
                $input['consumer_type'] = 3;

                do {
                    $newGuestId = mt_rand(100000, 999999);
                    $existingGuest = Guest::where('guest_id', $newGuestId)->first();
                } while ($existingGuest);

                $guest = new Guest();
                $guest->guest_id = $newGuestId;
                $guest->guest_name = $request->guest_name;
                $guest->phone = $request->guest_phone;
                $guest->nid = $request->guest_nid;
                $guest->guest_department = $request->guest_department;
                $guest->guest_designation = $request->guest_designation;
                $guest->save();

                $input['consumer_id'] = $guest->id;
            } else {
                return returnData(5000, null, 'Invalid Member Category');
            }

            $validate = $this->model->validate($input);
            if ($validate->fails()) {
                return returnData(5000, $validate->errors(), 'Member Already Assigned to Canteen');
            }

            DB::beginTransaction();

            $this->model->fill($input);
            $this->model->school_id = $authSchoolId;
            $this->model->save();

            DB::commit();

            return returnData(2000, null, 'Canteen Assigned Successfully');
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
            $resultData = $this->model
                ->where('id', $id)
                ->first();

            if (!$resultData) {
                return returnData(5000, null, 'Record not found');
            }

            switch ($resultData->consumer_type) {
                case 'student':
                    $consumer = DB::table('students as s')
                        ->leftJoin('student_classes as c', 's.class_id', '=', 'c.id')
                        ->leftJoin('departments as d', 's.department_id', '=', 'd.id')
                        ->leftJoin('sections as sec', 's.section_id', '=', 'sec.id')
                        ->where('s.id', $resultData->consumer_id)
                        ->select(
                            's.student_roll',
                            's.student_name_en',
                            's.student_phone',
                            'c.name as class_name',
                            'd.department_name',
                            'sec.name as section_name'
                        )
                        ->first();

                    $resultData->consumer = $consumer ? [
                        'student_roll' => $consumer->student_roll,
                        'student_name_en' => $consumer->student_name_en,
                        'student_phone' => $consumer->student_phone,
                        'class_name' => $consumer->class_name,
                        'department_name' => $consumer->department_name,
                        'section_name' => $consumer->section_name,
                    ] : null;
                    break;

                case 'teacher':
                    $consumer = DB::table('employees as e')
                        ->leftJoin('work_departments as d', 'e.department_id', '=', 'd.id')
                        ->leftJoin('designations as des', 'e.designation_id', '=', 'des.id')
                        ->where('e.id', $resultData->consumer_id)
                        ->select(
                            'e.id',
                            'e.employee_id',
                            'e.name',
                            'e.phone',
                            'des.name as designation',
                            'd.name as employee_department'
                        )
                        ->first();

                    $resultData->consumer = $consumer ? [
                        'employee_id' => $consumer->employee_id,
                        'name' => $consumer->name,
                        'phone' => $consumer->phone,
                        'designation' => $consumer->designation,
                        'employee_department' => $consumer->employee_department,
                    ] : null;
                    break;

                case 'guest':
                    $consumer = DB::table('guests')
                        ->where('id', $resultData->consumer_id)
                        ->select(
                            'guest_name',
                            'phone',
                            'nid',
                            'guest_designation',
                            'guest_department'
                        )
                        ->first();

                    $resultData->consumer = $consumer ? [
                        'guest_name' => $consumer->guest_name,
                        'phone' => $consumer->phone,
                        'nid' => $consumer->nid,
                        'guest_designation' => $consumer->guest_designation,
                        'guest_department' => $consumer->guest_department,
                    ] : null;
                    break;


                default:
                    $resultData->consumer = null;
                    break;
            }

            return returnData(2000, $resultData);
        } catch (\Exception $exception) {
            return returnData(5000, $exception->getMessage(), 'Whoops, Something Went Wrong..!!');
        }
    }

    public function update(Request $request, $id)
    {
        if (!can('assign_canteen_update')) {
            return $this->notPermitted();
        }

        $schoolId = auth()->user()->school_id;

        try {
            $input = $request->all();
            $validate = $this->model->validate($input);
            if ($validate->fails()) {
                return returnData(5000, $validate->errors());
            }

            $existingRecord = $this->model->find($id);
            if (!$existingRecord) {
                return returnData(5000, null, 'Record not found');
            }

            $existingRecord->school_id = $schoolId;
            $existingRecord->consumer_type = $request->consumer_type;
            $existingRecord->assign_date = $request->assign_date;
            $existingRecord->comments = $request->comments;

            if ($request->consumer_type == 'guest') {
                $guestData = Guest::where('id', $existingRecord->consumer_id)->first();

                if ($guestData) {
                    $guestData->guest_name = $request->guest_name;
                    $guestData->email = $request->email;
                    $guestData->guest_institute = $request->guest_institute;
                    $guestData->phone = $request->guest_phone;
                    $guestData->nid = $request->guest_nid;
                    $guestData->guest_department = $request->guest_department;
                    $guestData->guest_designation = $request->guest_designation;
                    $guestData->save();
                } else {
                    return returnData(5000, null, 'Guest record not found');
                }
            } else {
                $existingConsumer = $this->model
                    ->where('consumer_type', $request->consumer_type)
                    ->where('consumer_id', $request->consumer_id)
                    ->where('id', '<>', $id)
                    ->first();

                if ($existingConsumer) {
                    return returnData(5000, null, $request->consumer_type . ' already exists');
                }

                $existingRecord->consumer_id = $request->consumer_id;
            }
            $existingRecord->save();

            return returnData(2000, null, 'Successfully Updated');
        } catch (\Exception $exception) {
            return returnData(5000, $exception->getMessage(), 'Whoops, Something Went Wrong..!!');
        }
    }

    public function destroy($id)
    {
        if (!can('assign_canteen_delete')) {
            return $this->notPermitted();
        }

        try {
            $data = $this->model->find($id);

            if (!$data) {
                return returnData(5000, null, 'Data not found');
            }

            $hasInvoice = DB::table('invoices')
                ->where('invoice_category', 2)
                ->where('invoice_type', $data->consumer_type)
                ->where('invoice_type_id', $data->consumer_id)
                ->exists();

            if ($hasInvoice) {
                return returnData(5000, null, 'Cannot delete. Canteen assignment has related monthly fees.');
            }

            $data->delete();
            return returnData(2000, $data, 'Successfully Deleted');

        } catch (\Exception $exception) {
            return returnData(5000, $exception->getMessage(), 'Whoops, Something Went Wrong..!!');
        }
    }
}

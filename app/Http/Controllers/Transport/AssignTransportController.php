<?php

namespace App\Http\Controllers\Transport;

use App\Helpers\Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Transport\AssignTransport;

class AssignTransportController extends Controller
{
    use Helper;
    public function __construct()
    {
        $this->model = new AssignTransport();
    }

    public function index()
    {
        if (!can('assign_transport_view')) {
            return $this->notPermitted();
        }

        $keyword = request('keyword');
        $transportId = request('transport_id');
        $memberType = request('member_type') ?? null;

        try {
            $data = $this->model
                ->leftJoin('transport_manages', 'assign_transports.transport_id', 'transport_manages.id')
                ->leftJoin('transport_routes', 'transport_manages.route_id', 'transport_routes.id')
                ->select(
                    'assign_transports.*',
                    'transport_manages.transport_no',
                    'transport_manages.transport_name',
                    'transport_manages.route_id',
                    'transport_routes.route_name',
                )
                ->when($transportId, function ($query) use ($transportId) {
                    $query->where('assign_transports.transport_id', $transportId);
                }) 
                ->when($memberType, function ($query) use ($memberType) {
                    $query->where('assign_transports.transport_user_type', $memberType);
                })
                ->when($keyword, function ($query) use ($keyword) {
                    $query->where(function ($subQuery) use ($keyword) {
                        $subQuery->where('transport_manages.transport_name', 'like', "%$keyword%")
                            ->orWhere('transport_routes.route_name', 'like', "%$keyword%")
                            ->orWhereExists(function ($q) use ($keyword) {
                                $q->select(DB::raw(1))
                                    ->from('students')
                                    ->whereColumn('students.id', 'assign_transports.transport_user_id')
                                    ->where('students.student_roll', 'like', "%$keyword%");
                            })
                            ->orWhereExists(function ($q) use ($keyword) {
                                $q->select(DB::raw(1))
                                    ->from('employees')
                                    ->whereColumn('employees.id', 'assign_transports.transport_user_id')
                                    ->where('employees.employee_id', 'like', "%$keyword%");
                            });
                    });
                })
                ->orderBy('assign_transports.id', 'DESC')
                ->paginate(request()->input('perPage'));

            foreach ($data as $item) {
                $item->transport_user = null;

                if ($item->transport_user_type == 1) {
                    $user = DB::table('students')->where('id', $item->transport_user_id)->first();
                    if ($user) {
                        $item->transport_user = [
                            'student_roll' => $user->student_roll,
                            'student_name_en' => $user->student_name_en,
                            'student_phone' => $user->student_phone,
                        ];
                    }
                } elseif ($item->transport_user_type == 2) {
                    $user = DB::table('employees')->where('id', $item->transport_user_id)->first();
                    if ($user) {
                        $item->transport_user = [
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

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        if (!can('assign_transport_add')) {
            return $this->notPermitted();
        }

        try {
            $userId = auth()->user()->id;
            $authSchoolId = auth()->user()->school_id;
            $input = $request->all();

            if ($request->transport_user_type == 1) {
                $input['transport_user_id'] = $request->student_id ?? null;
                $input['transport_user_type'] = 1;
            } elseif ($request->transport_user_type == 2) {
                $input['transport_user_id'] = $request->employee_primary_id ?? null;
                $input['transport_user_type'] = 2;
            } elseif ($request->transport_user_type == 3) {
                $input['transport_user_id'] = $request->guest_id ?? null;
                $input['transport_user_type'] = 3;
            } else {
                return returnData(5000, null, 'Invalid Member Category');
            }

            $input['transport_user_type'] = $request->transport_user_type;

            $exists = $this->model
                ->where('transport_user_type', $input['transport_user_type'])
                ->where('transport_user_id', $input['transport_user_id'])
                ->where('school_id', $authSchoolId)
                ->exists();

            if ($exists) {
                return returnData(5000, null, 'This member is already assigned to transport.');
            }

            $netAmount = (float) ($request->net_amount ?? 0);
            $discount = (float) ($request->discount ?? 0);
            $payableAmount = $netAmount - ($netAmount * $discount / 100);
            $input['payable_amount'] = round($payableAmount, 2);

            $validate = $this->model->validate($input);
            if ($validate->fails()) {
                return returnData(5000, $validate->errors(), $validate->errors()->first());
            }

            DB::beginTransaction();

            $this->model->fill($input);
            $this->model->created_by = $userId;
            $this->model->school_id = $authSchoolId;
            $this->model->save();

            DB::commit();

            return returnData(2000, null, 'Transport Assigned Successfully');

        } catch (\Exception $exception) {
            DB::rollBack();
            return returnData(5000, $exception->getMessage(), $exception->getMessage());
        }
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        try {
            $assignData = $this->model
                ->leftJoin('transport_manages', 'assign_transports.transport_id', 'transport_manages.id')
                ->leftJoin('transport_routes', 'transport_manages.route_id', 'transport_routes.id')
                ->select(
                    'assign_transports.*',
                    'transport_manages.transport_no',
                    'transport_manages.transport_name',
                    'transport_manages.route_id',
                    'transport_routes.route_name'
                )
                ->where('assign_transports.id', $id)
                ->first();

            if (!$assignData) {
                return returnData(5000, null, 'Transport assignment not found');
            }

            $assignData->transport_user = null;

            if ($assignData->transport_user_type == 1) {
                $user = DB::table('students as s')
                    ->leftJoin('student_classes as c', 's.class_id', '=', 'c.id')
                    ->leftJoin('departments as d', 's.department_id', '=', 'd.id')
                    ->leftJoin('sections as sec', 's.section_id', '=', 'sec.id')
                    ->select(
                        's.id',
                        's.student_roll',
                        's.student_name_en',
                        's.student_phone',
                        'c.name as student_class',
                        'd.department_name',
                        'sec.name as section_name'
                    )
                    ->where('s.id', $assignData->transport_user_id)
                    ->first();
                if ($user) {
                    $assignData->transport_user = [
                        'student_id' => $user->id,
                        'student_roll' => $user->student_roll,
                        'student_name_en' => $user->student_name_en,
                        'student_phone' => $user->student_phone,
                        'class_name' => $user->student_class ?? null,
                        'department_name' => $user->department_name ?? null,
                        'section_name' => $user->section_name ?? null,
                    ];
                }
            } elseif ($assignData->transport_user_type == 2 || $assignData->transport_user_type == 3) {
                $user = DB::table('employees as e')
                    ->leftJoin('users as u', 'e.user_id', '=', 'u.id')
                    ->leftJoin('employee_departments as d', 'e.department_id', '=', 'd.id')
                    ->leftJoin('employee_designations as des', 'e.designation_id', '=', 'des.id')
                    ->select(
                        'e.id',
                        'e.employee_id',
                        'e.name',
                        'e.phone',
                        'des.name as designation',
                        'd.name as employee_department',
                        'u.email'
                    )
                    ->where('e.id', $assignData->transport_user_id)
                    ->first();
                if ($user) {
                    $assignData->transport_user = [
                        'employee_id' => $user->employee_id,
                        'name' => $user->name,
                        'phone' => $user->phone,
                        'designation' => $user->designation ?? null,
                        'employee_department' => $user->employee_department ?? null,
                        'email' => $user->email ?? null,
                    ];
                }
            }

            return returnData(2000, $assignData);
        } catch (\Exception $exception) {
            return returnData(5000, $exception->getMessage(), $exception->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        if (!can('assign_transport_update')) {
            return $this->notPermitted();
        }

        try {
            $userId = auth()->user()->id;

            $data = $this->model->find($id);
            if (!$data) {
                return returnData(5000, null, 'Data Not Found');
            }

            $input = $request->only([
                'transport_id',
                'stoppage',
                'assign_date',
                'discount',
                'net_amount',
                'comments'
            ]);

            // Keep fixed fields from DB
            $input['transport_user_id'] = $data->transport_user_id;
            $input['transport_user_type'] = $data->transport_user_type;
            $input['school_id'] = $data->school_id;

            // Recalculate payable amount
            $netAmount = (float) ($request->net_amount ?? 0);
            $discount = (float) ($request->discount ?? 0);
            $input['payable_amount'] = round($netAmount - ($netAmount * $discount / 100), 2);

            // Validate
            $validate = $this->model->validate($input, $id);
            if ($validate->fails()) {
                return returnData(5000, $validate->errors(), 'Validation Failed');
            }

            DB::beginTransaction();

            $data->fill($input);
            $data->updated_by = $userId;
            $data->save();

            DB::commit();

            return returnData(2000, null, 'Transport Assignment Updated Successfully');
        } catch (\Exception $exception) {
            DB::rollBack();
            return returnData(5000, $exception->getMessage(), 'Whoops, Something Went Wrong..!!');
        }
    }

    public function destroy($id)
    {
        if (!can('assign_transport_delete')) {
            return $this->notPermitted();
        }

        try {
            $data = $this->model->find($id);

            if (!$data) {
                return returnData(5000, null, 'Data not found');
            }

            $hasInvoice = DB::table('invoices')
                ->where('invoice_category', 4)
                ->where('invoice_type', $data->transport_user_type)
                ->where('invoice_type_id', $data->transport_user_id)
                ->exists();

            if ($hasInvoice) {
                return returnData(5000, null, 'Cannot delete. Transport fees already exist for this member.');
            }

            $data->delete();
            return returnData(2000, $data, 'Successfully Deleted');

        } catch (\Exception $exception) {
            return returnData(5000, $exception->getMessage(), 'Whoops, Something Went Wrong..!!');
        }
    }
}

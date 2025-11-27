<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Models\ApproveLeave;
use App\Models\Leave;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LeaveController extends Controller
{
    use Helper;

    public function __construct()
    {
        $this->model = new Leave();
    }
    public function index()
    {
        if (!can('apply_leave_view')) {
            return $this->notPermitted();
        }
        $authID = auth()->user();
        $status = request()->input('status');
        try {
            $keyword = request()->input('keyword');
            $data = $this->model->with('category:id,title', 'student:id,student_name_en', 'teacher:id,name', 'staff:id,name')
                ->where('school_id', $authID->school_id)
                ->when($keyword, function ($query) use ($keyword) {
                    $query->where('title', 'Like', "%$keyword%");
                })
                ->when($status, function ($query) use ($status) {
                    $query->where('status', '=', $status);
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
        if (!can('apply_leave_add')) {
            return $this->notPermitted();
        }
        $authID = auth()->user();
        try {
            $validate = $this->model->validate($request->all());

            if ($validate->fails()) {
                return returnData(3000, $validate->errors(), 'Validation Failed');
            }

            DB::beginTransaction();

            $this->model->fill($request->all());
            $this->model->school_id = $authID->school_id;
            $this->model->save();

            DB::commit();

            return returnData(2000, null, 'Successfully Inserted');
        } catch (\Exception $exception) {
            return returnData(5000, $exception->getMessage(), $exception->getMessage());
        }
    }

    public function show($id)
    {
        try {
            $ShowData = Leave::with('category:id,title', 'student:id,student_name_en', 'teacher:id,name', 'staff:id,name')
                ->find($id);

            if (!$ShowData) {
                return returnData(404, null, 'Leave not found');
            }

            $data = ApproveLeave::where('leave_id', $id)->get();
            $ShowData->approveData = $data;

            // Identify user ID
            $user_id = $ShowData->student_id ?? $ShowData->teacher_id ?? $ShowData->staff_id;

            $balance = DB::table('leave_categories')
                ->leftJoin('leaves', 'leave_categories.id', '=', 'leaves.category_id')
                ->leftJoin('approve_leaves', 'leaves.id', '=', 'approve_leaves.leave_id')
                ->selectRaw("
                leave_categories.id,
                leave_categories.title,
                leave_categories.note,
                leave_categories.total_leave,
                COALESCE(ROUND(SUM(leaves.no_of_days)), 0) as applied_leave,
                COALESCE(SUM(approve_leaves.approve_days), 0) as approved,
                leave_categories.total_leave - COALESCE(SUM(approve_leaves.approve_days), 0) as balance
            ")
                ->where(function ($query) use ($user_id) {
                    $query->where('leaves.student_id', $user_id)
                        ->orWhere('leaves.teacher_id', $user_id)
                        ->orWhere('leaves.staff_id', $user_id);
                })
                ->groupBy('leave_categories.id', 'leave_categories.title', 'leave_categories.note', 'leave_categories.total_leave')
                ->get();

            $ShowData->balanceLeave = $balance;

            return returnData(2000, $ShowData);
        } catch (\Exception $exception) {
            return returnData(5000, $exception->getMessage(), $exception->getMessage());
        }
    }


    // public function show($id)
    // {
    //     try {
    //         $ShowData = Leave::with('category:id,title', 'student:id,student_name_en', 'teacher:id,name', 'staff:id,name')
    //             ->find($id);

    //         if (!$ShowData) {
    //             return returnData(404, null, 'Leave not found');
    //         }

    //         $data = ApproveLeave::where('leave_id', $id)->get();
    //         $ShowData->approveData = $data;

    //         $user_id = $ShowData->student_id ?? $ShowData->teacher_id ?? $ShowData->staff_id;

    //         $balance = DB::table('leave_categories')
    //             ->leftJoin('leaves', 'leave_categories.id', '=', 'leaves.category_id')
    //             ->leftJoin('approve_leaves', 'leaves.id', '=', 'approve_leaves.leave_id')
    //             ->selectRaw("
    //             leave_categories.title,
    //             leave_categories.note,
    //             leave_categories.total_leave,
    //             ROUND(SUM(leaves.no_of_days)) as applied_leave,
    //             CASE WHEN SUM(approve_leaves.approve_days) IS NOT NULL THEN SUM(approve_leaves.approve_days) ELSE 0 END as approved,
    //             leave_categories.total_leave - CASE WHEN SUM(approve_leaves.approve_days) IS NOT NULL THEN SUM(approve_leaves.approve_days) ELSE 0 END as balance
    //         ")
    //             ->where(function ($query) use ($user_id) {
    //                 $query->where('leaves.student_id', $user_id)
    //                     ->orWhere('leaves.teacher_id', $user_id)
    //                     ->orWhere('leaves.staff_id', $user_id);
    //             })
    //             ->groupBy('leave_categories.id')
    //             ->get();

    //         $ShowData->balanceLeave = $balance;

    //         return returnData(2000, $ShowData);
    //     } catch (\Exception $exception) {
    //         return returnData(5000, $exception->getMessage(), $exception->getMessage());
    //     }
    // }


    public function edit($id)
    {
    }

    public function update(Request $request, $id)
    {
        if (!can('apply_leave_update')) {
            return $this->notPermitted();
        }
        $authID = auth()->user();
        try {
            $validate = $this->model->validate($request->all());

            if ($validate->fails()) {
                return returnData(3000, $validate->errors(), 'Validation Failed');
            }

            DB::beginTransaction();

            $data = $this->model->where('id', $request->input('id'))->first();

            if ($data) {
                $data->fill($request->all());
                $data->school_id = $authID->school_id;
                $data->save();

                DB::commit();

                return returnData(2000, null, 'Successfully Updated');
            }

            return returnData(5000, null, 'Canteen Menu Not found');
        } catch (\Exception $exception) {
            return returnData(5000, $exception->getMessage(), $exception->getMessage());
        }
    }

    public function destroy($id)
    {
        if (!can('apply_leave_delete')) {
            return $this->notPermitted();
        }
        $data = $this->model->where('id', $id)->first();

        if ($data) {
            $data->delete();

            return returnData(2000, null, 'Successfully Deleted');
        }

        return returnData(2000, [], 'Data not found');
    }

    public function DueLeaveData(Request $request)
    {
        $studentId = $request->input('student_id');
        $teacherId = $request->input('teacher_id');
        $staffId = $request->input('staff_id');

        $query = DB::table('leave_categories')
            ->leftJoin('leaves', function ($join) use ($studentId, $teacherId, $staffId) {
                $join->on('leave_categories.id', '=', 'leaves.category_id');
                if ($studentId) {
                    $join->where('leaves.student_id', '=', $studentId);
                } elseif ($teacherId) {
                    $join->where('leaves.teacher_id', '=', $teacherId);
                } elseif ($staffId) {
                    $join->where('leaves.staff_id', '=', $staffId);
                }
            })
            ->leftJoin('approve_leaves', 'leaves.id', '=', 'approve_leaves.leave_id')
            ->selectRaw("
                leave_categories.id,
                leave_categories.title,
                leave_categories.note,
                leave_categories.total_leave,
                COALESCE(SUM(leaves.no_of_days), 0) as applied_leave,
                COALESCE(SUM(approve_leaves.approve_days), 0) as approved,
                leave_categories.total_leave - COALESCE(SUM(approve_leaves.approve_days), 0) as balance
            ")
            ->groupBy('leave_categories.id');

        $balance = $query->get();

        return returnData(2000, $balance);
    }
}

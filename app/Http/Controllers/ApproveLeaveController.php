<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Models\ApproveLeave;
use App\Models\Leave;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApproveLeaveController extends Controller
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

    public function updateLeaveStatus(Request $request, $id)
    {
        // dd($request->all());
        try {
            $authID = auth()->user();

            DB::beginTransaction();
            $approve_days = $request->input('approve_days');

            if ($request->input('leaveType')) {
                $leaveAccept = $request->input('leaveType');

                foreach ($leaveAccept as $leave) {
                    if ($leave) {
                        $approve_days = $leave['approve_days'] ?? $approve_days;

                        $leave['leave_to'] = $leave['leave_to'] ?? $request->leave_to;
                        $leave['leave_from'] = $leave['leave_from'] ?? $request->leave_from;
                        $leave['approve_days'] = $leave['approve_days'] ?? $request->approve_days;

                        $requestModel = new ApproveLeave();
                        $requestModel->fill($leave);
                        $requestModel->leave_id = $id;
                        $requestModel->school_id = $authID->school_id;
                        $requestModel->status = $request->status;
                        $requestModel->save();
                    }
                }
            } else {
                $studentdata = new ApproveLeave();
                $studentdata->leave_id = $id;
                $studentdata->leave_from = $request->leave_from;
                $studentdata->leave_to = $request->leave_to;
                $studentdata->approve_days = $request->approve_days;
                $studentdata->status = $request->status;
                $studentdata->school_id = $authID->school_id;

                $studentdata->leave_to = $studentdata->leave_to ?? $request->leave_to;
                $studentdata->leave_from = $studentdata->leave_from ?? $request->leave_from;
                $studentdata->approve_days = $studentdata->approve_days ?? $request->approve_days;

                $studentdata->save();
            }
            Leave::where('id', $id)->update([
                'status' => $request->status,
                'school_id' => $authID->school_id,
                'approve_days' => $approve_days,
            ]);

            DB::commit();

            return returnData(2000, null, 'Leave request updated successfully');
        } catch (\Exception $exception) {
            return returnData(5000, null, $exception->getMessage());
        }
    }
}

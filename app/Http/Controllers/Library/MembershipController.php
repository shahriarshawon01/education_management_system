<?php

namespace App\Http\Controllers\Library;

use App\Models\Staff;
use App\Helpers\Helper;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Http\Request;
use App\Models\Library\Membership;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Employee\Employee;

class MembershipController extends Controller
{
    use Helper;
    public function __construct()
    {
        $this->model = new Membership();
    }

    // public function index()
    // {
    //     if (!can('membership_view')) {
    //         return $this->notPermitted();
    //     }
    //     try {
    //         $keyword = request()->input('keyword');
    //         $type = request()->input('type');
    //         $perPage = request()->input('perPage', 15);

    //         $data = $this->model->with(['teacher', 'student'])
    //             ->where(function($query) use ($keyword) {
    //                 if ($keyword) {
    //                     $query->whereHas('teacher', function($q) use ($keyword) {
    //                         $q->where('name', 'like', "%$keyword%");
    //                     })->orWhereHas('student', function($q) use ($keyword) {
    //                         $q->where('student_name_en', 'like', "%$keyword%");
    //                     })->orWhereHas('student', function($q) use ($keyword) {
    //                         $q->where('student_roll', 'like', "%$keyword%");
    //                     })->orWhereHas('teacher', function($q) use ($keyword) {
    //                         $q->where('employee_id', 'like', "%$keyword%");
    //                     });
    //                 }
    //             })
    //             ->when($type, function ($query) use ($type) {
    //                 $query->where('type', 'Like', "%$type%");
    //             })
    //             ->paginate($perPage);

    //         return returnData(2000, $data);
    //     } catch (\Exception $exception) {
    //         return returnData(5000, $exception->getMessage(), 'Whoops, Something Went Wrong..!!');
    //     }
    // }

    public function index()
    {

        if (!can('membership_view')) {
            return $this->notPermitted();
        }

        try {
            $data = $this->model
            // ->with("product:id,name", "stock_in:id,product_id,product_code")
                ->when(input('keyword'), function ($query) {
                    $keyword = input('keyword');
                    $query->where('name', 'LIKE', "%$keyword%");
                })
                ->orderBy('id', 'DESC')
                ->paginate(request()->input('perPage'));

            foreach ($data as $item) {
                switch ($item->type) {
                    case '1':
                        $member = Student::with('user:id,email')->find($item->member_id);
                        $item->member = $member ? $member->only(['student_roll', 'student_name_en', 'student_phone']) + [
                        ] : null;
                        break;
                    case '2':
                        $member = Employee::with('user:id,email')->find($item->member_id);
                        $item->member = $member ? $member->only(['employee_id', 'name', 'phone', 'department']) + [
                        ] : null;
                        break;
                         case '3':
                        $member = Employee::find($item->member_id);
                        $item->member = $member ? $member->only(['employee_id', 'name', 'phone', 'department']) + [
                        ] : null;
                        break;
                    default:
                        $item->member = null;
                        break;
                }
            }
            // ddA($data);
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
        // dd($request->all());
        
        if (!can('membership_add')) {
            return $this->notPermitted();
        }
        try {
            $schoolId = auth()->user()->school_id;
            $authUserId = auth()->user()->id;
            $input = $request->all();
            $validate = $this->model->validate($input);
            if ($validate->fails()) {
                return returnData(3000, $validate->errors());
            }
           
            $memberId = null;
            if ($request->type == '1') {
                $memberId = $request->student_id ?? null;
            } elseif ($request->type == '2') {
                $memberId = $request->employee_primary_id ?? null;
            }
             elseif ($request->type == '3') {
                $memberId = $request->employee_primary_id ?? null;
            }

            $this->model->fill($request->except(['id', 'type']));
            $this->model->school_id = $schoolId;
             $this->model->created_by = $authUserId;
            $this->model->member_id = $memberId;
            $this->model->type = $request->type;
            $this->model->save();
            return returnData(2000, null, 'Successfully Inserted');
        } catch (\Exception $exception) {
            return returnData(5000, $exception->getMessage(), 'Whoops, Something Went Wrong..!!');
        }

    }

   

    public function edit($id)
    {
        try {
            $resultData = $this->model
            // ->with('product:id,name', 'stock_in:id,product_id,product_code,purchase_quantity,sale_quantity')
                ->where('id', $id)
                ->first();

            if (!$resultData) {
                return returnData(5000, null, 'Record not found');
            }
            switch ($resultData->type) {
                case '1':
                    $member = Student::with('user:id,email')->find($resultData->member_id);
                    $resultData->member = $member ? $member->only(['student_roll', 'student_name_en', 'student_phone', 'classes', 'departments', 'sections','sessions']) + [
                        'email' => $member->user->email ?? null,
                    ] : null;
                    break;
                case '2':
                    $member = Employee::with('user:id,email')->find($resultData->member_id);
                    $resultData->member = $member ? $member->only(['employee_id', 'name', 'phone', 'department']) + [
                        'email' => $member->user->email ?? null,
                        'phone' => $member->user->profile->phone ?? null,
                    ] : null;
                    break;
                case '3':
                    $member = Employee::find($resultData->member_id);
                    $resultData->member = $member ? $member->only(['employee_id', 'name', 'phone', 'department']) + [
                        'email' => $member->user->email ?? null,
                        'phone' => $member->user->profile->phone ?? null,
                    ] : null;
                    break;
                default:
                    $resultData->member = null;
                    break;
            }
            // ddA($resultData);
            return returnData(2000, $resultData);
        } catch (\Exception $exception) {
            return returnData(5000, $exception->getMessage(), $exception->getMessage());
        }
    }


    public function show($id)
    {
       
        if (!can('membership_view')) {
            return $this->notPermitted();
        }
        $data = $this->model->where('id', $id)->first();
        if ($data) {
            switch ($data->type) {
                case '1':
                    $data->member = Student::find($data->member_id);
                    break;
                case '2':
                    $data->member = Employee::find($data->member_id);
                    break;
                case '3':
                    $data->member = Employee::find($data->member_id);
                    break;
                default:
                    $data->member = null;
                    break;
            }
        } else {
            return returnData(5000, 'Record not found');
        }
        // ddA($data);
        return returnData(2000, $data);
    }

    // public function update(Request $request, $id)
    // {
    //     // ddA($request->all());
    //     if (!can('membership_update')) {
    //         return $this->notPermitted();
    //     }
    //     try {
    //         $authUserId = auth()->user()->id;
    //         $schoolId = auth()->user()->school_id;
    //         $input = $request->all();
    //         $validation = $this->model->validate($input);

          

    //         if ($validation->fails()) {
    //             return response()->json(['status' => 2000, 'errors' => $validation->errors()], 200);
    //         }

    //         DB::beginTransaction();
    //         $data = $this->model->where('id', $id)->first();
    //         if (!$data) {
    //             DB::rollBack();
    //             return returnData(5000, null, 'Record Not Found');
    //         }
          

    //         $memberId = null;
    //         if ($request->type == '1') {
    //             $memberId = $request->student_id ?? null;
    //         } elseif ($request->type == '2') {
    //             $memberId = $request->employee_primary_id ?? null;
    //         }
    //             elseif ($request->type == '3') {
    //                 $memberId = $request->employee_primary_id ?? null;
    //             }

    //         $data->fill($request->except(['id', 'type']));
    //         $data->school_id = $schoolId;
    //         $data->updated_by = $authUserId;
    //         $data->member_id = $memberId;
    //         $data->type = $request->type;
    //         $data->save();

    //         DB::commit();

    //         return returnData(2000, null, 'Successfully Updated');
            
    //     } catch (\Exception $exception) {
    //         return returnData(5000, $exception->getMessage(), 'Whoops, Something Went Wrong..!!');
    //     }
    // }

    public function update(Request $request, $id)
{
    if (!can('membership_update')) {
        return $this->notPermitted();
    }

    try {
        $authUserId = auth()->user()->id;
        $schoolId = auth()->user()->school_id;
        $input = $request->all();


        $validation = $this->model->validate($input);
        if ($validation->fails()) {
            return response()->json(['status' => 2000, 'errors' => $validation->errors()], 200);
        }

        DB::beginTransaction();

        $data = $this->model->where('id', $id)->first();
        if (!$data) {
            DB::rollBack();
            return returnData(5000, null, 'Record Not Found');
        }

        $memberId = null;
        if ($request->type == '1') {
            $memberId = $request->student_id ?? null;
        } elseif ($request->type == '2') {
            $memberId = $request->employee_primary_id ?? null;
        } elseif ($request->type == '3') {
            $memberId = $request->employee_primary_id ?? null;
        }

        if ($memberId === null) {
            $memberId = $data->member_id;
        }

        $type = $request->type ?? $data->type;


        $data->fill($request->except(['id', 'type']));
        $data->school_id = $schoolId;
        $data->updated_by = $authUserId;
        $data->member_id = $memberId;
        $data->type = $type;
        $data->save();

        DB::commit();

        return returnData(2000, null, 'Successfully Updated');
    } catch (\Exception $exception) {
        DB::rollBack();
        return returnData(5000, $exception->getMessage(), 'Whoops, Something Went Wrong..!!');
    }
}


    public function destroy($id)
    {
        if (!can('membership_delete')) {
            return $this->notPermitted();
        }
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
    public function getStudentDetails(Request $request)
    {
       
//        $data = Student::where('id',$request->student)->select('email','phone','reg_number','class_id','section_id','session_id')->first();
        // $data = Student::where('id',$request->student_id)->select('reg_number','class_id','section_id','session_year_id','student_email','student_phone')->first();
        $data = Student::where('id',$request->student_id)->select('reg_number','class_id','section_id','session_year_id','department_id','student_phone')->first();
        return returnData(2000, $data);
    }
    public function getTeacherDetails(Request $request)
    {
        $data = Teacher::where('id',$request->teacher_id)->select('phone')->first();
        return returnData(2000, $data);
    }
}

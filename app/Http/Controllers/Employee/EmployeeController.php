<?php

namespace App\Http\Controllers\Employee;

use App\Models\User;
use App\Models\School;
use App\Helpers\Helper;
use App\Models\Address;
use Illuminate\Http\Request;
use App\Models\Employee\Employee;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Models\Employee\EmployeeQualification;

class EmployeeController extends Controller
{
    use Helper;

    public function __construct()
    {
        $this->model = new Employee();
    }

    public function index()
    {
        if (!can('employee_view')) {
            return $this->notPermitted();
        }
        try {
            $keyword = request()->input('keyword');
            $designation = request()->input('designation_id');
            $department = request()->input('department_id');
            $employeeType = request()->input('employee_type');
            $status = request()->input('status');

            $data = $this->model
                ->with([
                    'department:id,name',
                    'designation:id,name',
                    'user:id,id,email',
                ])
                ->when($keyword, function ($query) use ($keyword) {
                    $query->where('name', 'like', "%$keyword%")
                        ->orWhere('employee_id', 'like', "%$keyword%");
                })
                ->when($designation, function ($query) use ($designation) {
                    $query->where('designation_id', 'Like', "$designation");
                })
                ->when($department, function ($query) use ($department) {
                    $query->where('department_id', 'Like', "$department");
                })
                ->when($employeeType, function ($query) use ($employeeType) {
                    $query->where('employee_type', 'Like', "$employeeType");
                })
                ->when(isset($status), function ($query) use ($status) {
                    $query->where('status', $status);
                })
                ->orderBy('sort_order', 'asc')
                ->paginate(request()->input('perPage'));

            return returnData(2000, $data);
        } catch (\Exception $exception) {
            return returnData(5000, $exception->getMessage(), 'Whoops, Something Went Wrong..!!');
        }
    }

    public function reorder(Request $request)
    {
        try {
            foreach ($request->orderedIds as $employee) {
                $this->model->where('id', $employee['id'])
                    ->update(['sort_order' => $employee['position']]);
            }

            return returnData(2000, null, 'Order updated successfully');
        } catch (\Exception $exception) {
            return returnData(5000, $exception->getMessage(), 'Something went wrong..!!');
        }
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        if (!can('employee_add')) {
            return $this->notPermitted();
        }

        try {
            DB::beginTransaction();

            $input = $request->all();
            $schoolId = auth()->user()->school_id;
            $createdBy = auth()->user()->id;
            $addresses = $request->input('address', []);
            $qualifications = $request->input('qualification', []);

            $validate = $this->model->validate($input);
            if ($validate->fails()) {
                return returnData(2000, $validate->errors());
            }

            if (User::where('email', $request->email)->exists()) {
                return returnData(5000, null, 'Email Already Exists');
            }
            if (Employee::where('employee_id', $request->employee_id)->exists()) {
                return returnData(5000, null, 'Employee ID Already Exists');
            }

            $employeeTypeMap = [
                1 => 'teacher',
                2 => 'staff',
                3 => 'support_staff'
            ];
            $userId = null;
            $employeeType = $request->employee_type;

            if ($request->as_login) {
                $userData = new User();
                $userData->username = strtolower($request->name);
                $userData->email = $request->input('email');
                $userData->password = Hash::make($request->input('password'));
                $userData->role_id = 2;
                $userData->type = ($employeeType == 1) ? 2 : 3;
                $userData->status = 1;
                $userData->school_id = $schoolId;
                $userData->created_by = $createdBy;
                $userData->save();

                $userId = $userData->id;
            }

            $picturePath = $request->image ?? null;
            $this->model->name = $request->input('name');
            $this->model->user_id = $userId;
            $this->model->father_name = $request->input('father_name');
            $this->model->mother_name = $request->input('mother_name');
            $this->model->joining_date = $request->input('joining_date');
            $this->model->employee_id = $request->input('employee_id');
            $this->model->gender = $request->input('gender');
            $this->model->marital_status = $request->input('marital_status');
            $this->model->religion = $request->input('religion');
            $this->model->designation_id = $request->input('designation_id');
            $this->model->department_id = $request->input('department_id');
            $this->model->school_id = $schoolId;
            $this->model->nid = $request->input('nid');
            $this->model->phone = $request->input('phone');
            $this->model->date_of_birth = $request->input('date_of_birth');
            $this->model->photo = $picturePath;
            $this->model->status = 1;
            $this->model->created_by = $createdBy;
            $this->model->employee_type = $employeeType;

            $this->model->save();
            $employeeId = $this->model->id;

            // Save addresses
            // $addressableType = ($employeeType == 1) ? 'teacher' : 'staff';

            $addressableType = $employeeTypeMap[$employeeType] ?? 'staff';
            foreach ($addresses as $address) {
                $addressData = new Address();
                $addressData->fill($address);
                $addressData->addressable_id = $employeeId;
                $addressData->addressable_type = 2;
                $addressData->school_id = $schoolId;
                $addressData->save();
            }

            // Save qualifications
            foreach ($qualifications as $qualification) {
                $qualificationData = new EmployeeQualification();
                $qualificationData->fill($qualification);
                $qualificationData->employee_id = $employeeId;
                $qualificationData->school_id = $schoolId;
                $qualificationData->created_by = $createdBy;
                $qualificationData->save();
            }

            DB::commit();
            return returnData(2000, null, 'Successfully Inserted');

        } catch (\Exception $exception) {
            DB::rollBack();
            return returnData(5000, $exception->getMessage(), 'Whoops, Something Went Wrong..!!');
        }
    }

    public function show($id)
    {
        $data = $this->model->where('id', $id)
            ->with('employee_qualifications:id,employee_id,degree_name,board_name,passing_year,dept_name,status', 'department:id,name', 'designation:id,name', 'user')->first();

        $address = Address::selectRaw("*, divisions.name as division, districts.name as district, upazilas.name as upazilla,unions.name as union_name")
            ->join('divisions', 'addresses.division', '=', 'divisions.id')
            ->join('districts', 'addresses.district', '=', 'districts.id')
            ->join('upazilas', 'addresses.upazila', '=', 'upazilas.id')
            ->join('unions', 'addresses.union', '=', 'unions.id')
            ->where('addressable_id', $id)->get();

        $school = School::first();
        if ($school) {
            $data['school'] = collect($school->toArray());
        } else {
            $data['school'] = null;
        }

        $data->{'address'} = $address;

        return returnData(2000, $data);
    }

    public function edit($id)
    {
        try {
            $data = $this->model->where('id', $id)
                ->with(['user'])
                ->first();

            if (!$data) {
                return returnData(5000, null, 'Employee not found');
            }

            $qualification = EmployeeQualification::where('employee_id', $id)->get();
            $address = Address::where('addressable_id', $id)->where('addressable_type',2)->get();
            $resultData = $data->toArray();

            $resultData['qualification'] = $qualification->toArray();
            $resultData['address'] = $address->toArray();

            return returnData(2000, $resultData);
        } catch (\Exception $exception) {
            return returnData(5000, $exception->getMessage(), $exception->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        if (!can('employee_update')) {
            return $this->notPermitted();
        }
        try {
            DB::beginTransaction();

            $input = $request->all();
            $schoolId = auth()->user()->school_id;
            $createdBy = auth()->user()->id;
            $addresses = $request->input('address', []);
            $qualifications = $request->input('qualification', []);

            // Validation
            $validate = $this->model->validate($input);
            if ($validate->fails()) {
                return returnData(2000, $validate->errors());
            }

            // Find employee
            $employee = $this->model->findOrFail($id);
            $userId = $employee->user_id;
            $employeeType = $request->employee_type;

            // Handle User (login info)
            if ($request->as_login) {
                $user = User::find($userId) ?: new User();

                $exist = User::where('email', $request->email)
                    ->where('id', '!=', $userId)
                    ->first();
                if ($exist) {
                    return returnData(5000, null, 'Email already exists..');
                }

                $user->username = strtolower($request->name);
                $user->email = $request->input('email');
                if ($request->filled('password')) {
                    $user->password = Hash::make($request->input('password'));
                }
                $user->role_id = 2;
                $user->type = ($employeeType == 1) ? 2 : 3;
                $user->status = 1;
                $user->school_id = $schoolId;
                $user->created_by = $createdBy;
                $user->save();

                $userId = $user->id;
            }

            // Check duplicate employee_id if changed
            if ($employee->employee_id !== $request->employee_id) {
                if (Employee::where('employee_id', $request->employee_id)->exists()) {
                    return returnData(5000, null, 'Employee ID already exists.');
                }
            }

            $picturePath = $request->image ?? $employee->photo;

            // Update Employee (explicit assignment style)
            $employee->user_id = $userId;
            $employee->name = $request->input('name');
            $employee->father_name = $request->input('father_name');
            $employee->mother_name = $request->input('mother_name');
            $employee->joining_date = $request->input('joining_date');
            $employee->employee_id = $request->input('employee_id');
            $employee->gender = $request->input('gender');
            $employee->marital_status = $request->input('marital_status');
            $employee->religion = $request->input('religion');
            $employee->designation_id = $request->input('designation_id');
            $employee->department_id = $request->input('department_id');
            $employee->school_id = $schoolId;
            $employee->nid = $request->input('nid');
            $employee->phone = $request->input('phone');
            $employee->date_of_birth = $request->input('date_of_birth');
            $employee->photo = $picturePath;
            $employee->status = 1;
            $employee->created_by = $createdBy;
            $employee->employee_type = $employeeType;
            $employee->save();

            $employeeId = $employee->id;

            // 🔹 Update Address records
            foreach ($addresses as $address) {
                if (isset($address['id'])) {
                    $existingAddress = Address::find($address['id']);
                    if ($existingAddress) {
                        $existingAddress->division = $address['division'];
                        $existingAddress->district = $address['district'];
                        $existingAddress->upazila = $address['upazila'];
                        $existingAddress->union = $address['union'];
                        $existingAddress->post_office = $address['post_office'];
                        $existingAddress->village = $address['village'];
                        $existingAddress->type = $address['type'];
                        $existingAddress->school_id = $schoolId;
                        $existingAddress->save();
                    }
                } else {
                    $newAddress = new Address();
                    $newAddress->division = $address['division'];
                    $newAddress->district = $address['district'];
                    $newAddress->upazila = $address['upazila'];
                    $newAddress->union = $address['union'];
                    $newAddress->post_office = $address['post_office'];
                    $newAddress->village = $address['village'];
                    $newAddress->type = $address['type'];
                    $newAddress->addressable_id = $employeeId;
                    $newAddress->school_id = $schoolId;
                    $newAddress->save();
                }
            }

            foreach ($qualifications as $qualification) {
                if (isset($qualification['id'])) {
                    $existingQualification = EmployeeQualification::find($qualification['id']);
                    if ($existingQualification) {
                        $existingQualification->degree_name = $qualification['degree_name'];
                        $existingQualification->board_name = $qualification['board_name'];
                        $existingQualification->passing_year = $qualification['passing_year'];
                        $existingQualification->dept_name = $qualification['dept_name'];
                        $existingQualification->school_id = $schoolId;
                        $existingQualification->created_by = $createdBy;
                        $existingQualification->save();
                    }
                } else {
                    $newQualification = new EmployeeQualification();
                    $newQualification->degree_name = $qualification['degree_name'];
                    $newQualification->board_name = $qualification['board_name'];
                    $newQualification->passing_year = $qualification['passing_year'];
                    $newQualification->dept_name = $qualification['dept_name'];
                    $newQualification->employee_id = $employeeId;
                    $newQualification->school_id = $schoolId;
                    $newQualification->created_by = $createdBy;
                    $newQualification->save();
                }
            }

            DB::commit();
            return returnData(2000, null, 'Successfully Updated');

        } catch (\Exception $exception) {
            DB::rollBack();
            return returnData(5000, $exception->getMessage(), 'Whoops, Something Went Wrong..!!');
        }
    }

    public function destroy($id)
    {
        if (!can('employee_delete')) {
            return $this->notPermitted();
        }
        $data = $this->model->where('id', $id)->first();

        if ($data) {
            $employeeId = $data->id;

            Address::where('addressable_id', $employeeId)->delete();
            EmployeeQualification::where('employee_id', $employeeId)->delete();

            $data->delete();
            User::where('id', $data->user_id)->delete();

            return returnData(2000, null, 'Successfully Deleted');
        }

        return returnData(2000, [], 'Record not found');
    }
    public function employeeJobStatus(Request $request, $id)
    {
        try {
            $resignDate = $request->resign_date;
            $terminateDate = $request->terminate_date;
            $jobStatus = $request->status;

            $userId = Employee::where('id', $id)->value('user_id');

            if ($jobStatus == 1 || $jobStatus == 0) {
                $terminateDate = null;
                $resignDate = null;
            } else {
                if ($resignDate) {
                    $terminateDate = null;
                } elseif ($terminateDate) {
                    $resignDate = null;
                }
            }

            Employee::where('id', $id)->update([
                'status' => $jobStatus,
                'resign_date' => $resignDate,
                'terminate_date' => $terminateDate,
                'job_comments' => $request->job_comments,
            ]);

            if ($userId) {
                $userStatus = ($jobStatus == 2 || $jobStatus == 3) ? 0 : $jobStatus;
                User::where('id', $userId)->update([
                    'status' => $userStatus,
                ]);
            }

            // Dynamic success message
            if ($jobStatus == 2) {
                $message = 'Employee resigned successfully';
            } elseif ($jobStatus == 3) {
                $message = 'Employee terminated successfully';
            } elseif ($jobStatus == 1) {
                $message = 'Employee activated successfully';
            } elseif ($jobStatus == 0) {
                $message = 'Employee inactivated successfully';
            } else {
                $message = 'Job status updated successfully';
            }

            return returnData(2000, null, $message);

        } catch (\Exception $exception) {
            return returnData(5000, null, $exception->getMessage());
        }
    }
}

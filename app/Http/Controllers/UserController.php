<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Helpers\Helper;
use App\Models\Student;
use Illuminate\Http\Request;
use App\Models\Employee\Employee;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    use Helper;

    public function __construct()
    {
        $this->model = new User();
    }

    public function index()
    {
        if (!can('user_view')) {
            return $this->notPermitted();
        }
        try {
            $userName = input('keyword');
            $role_id = input('role_id');
            $status = request()->input('status');

            $users = $this->model
                ->with(['roles:id,display_name'])
                ->when($userName, function ($query) use ($userName) {
                    $query->where('username', 'Like', "%$userName%");
                })
                ->when($role_id, function ($query) use ($role_id) {
                    $query->where('role_id', $role_id);
                })
                ->when($status, function ($query) use ($status) {
                    $query->where('status', '=', (int) $status);
                })
                ->orderby('id', 'desc')
                ->paginate(input('perPage'));

            return returnData(2000, $users);
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
        if (!can('user_add')) {
            return $this->notPermitted();
        }
        try {
            $data = $request->all();
            $schoolId = auth()->user()->school_id;
            $userId = auth()->user()->id;

            $emailExists = $this->model->where('email', $request->input('email'))->exists();
            if ($emailExists) {
                return returnData(5000, null, 'User already exists.');
            }

            if (isset($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            }
            $user = $this->model;
            $picturePath = $data['image'] ?? null;
            $userType = $request->input('type');
            $user->username = $request->input('username');
            $user->email = $request->input('email');
            $user->password = Hash::make($request->input('password'));
            $user->role_id = $request->input('role_id');
            $user->type = $userType;
            $user->school_id = $schoolId;
            $user->image = $picturePath;
            $user->created_by = $userId;
            $user->status = 1;
            $user->save();

            $userId = $user->id;

            return returnData(2000, null, 'Successfully Inserted');
        } catch (\Exception $exception) {
            return returnData(5000, $exception->getMessage(), 'Whoops, Something Went Wrong..!!');
        }
    }

    public function show($id)
    {
        if (!can('user_view')) {
            return $this->notPermitted();
        }
        try {
            $perPage = request()->input('perPage');
            $data = $this->model->where('id', $id)
                ->orderBy('id', 'DESC')
                ->paginate($perPage);

            return returnData(2000, $data);
        } catch (\Exception $exception) {
            return returnData(5000, $exception->getMessage(), 'Whoops, Something Went Wrong..!!');
        }
    }

    public function edit($id)
    {
        try {
            $resultData = $this->model->with([
                'roles:id,display_name',
            ])
                ->where('id', $id)
                ->first();
            if (!$resultData) {
                return returnData(5000, null, 'No record found.');
            }
            return returnData(2000, $resultData);
        } catch (\Exception $exception) {
            return returnData(5000, $exception->getMessage(), $exception->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        if (!can('user_update')) {
            return $this->notPermitted();
        }
        try {
            $user = $this->model->findOrFail($id);
            $data = $request->all();
            $schoolId = auth()->user()->school_id;
            $userId = auth()->user()->id;
            $userType = $request->input('type');

            $emailExists = $this->model->where('email', $request->input('email'))
                ->where('id', '<>', $id)
                ->exists();
            if ($emailExists) {
                return returnData(5000, null, 'Email already exists.');
            }

            // Update user fields
            $user->username = $request->input('username');
            $user->email = $request->input('email');
            if (isset($data['password'])) {
                $user->password = Hash::make($request->input('password'));
            }
            $user->role_id = $request->input('role_id');
            $user->type = $userType;
            $user->school_id = $schoolId;
            $user->image = $data['image'] ?? $user->image;
            $user->created_by = $userId;
            $user->nid = $request->input('nid');
            $user->date_of_birth = $request->input('date_of_birth');
            $user->phone = $request->input('phone');
            $user->status = $request->input('status', 1);
            $user->save();

            return returnData(2000, null, 'Successfully Updated');
        } catch (\Exception $exception) {
            return returnData(5000, $exception->getMessage(), 'Whoops, Something Went Wrong..!!');
        }
    }

    public function destroy($id)
    {
        if (!can('user_delete')) {
            return $this->notPermitted();
        }
        try {
            $data = $this->model->where('id', $id)->first();
            if (!$data) {
                return returnData(5000, null, 'No data found.');
            }
            $data->delete();

            return returnData(2000, $data, 'Successfully Deleted');
        } catch (\Exception $exception) {
            return returnData(5000, $exception->getMessage(), 'Whoops, Something Went Wrong..!!');
        }
    }
    public function statusChanges($id)
    {
        $user = User::findOrFail($id);

        if ($user->status == '1') {

            $user->update([
                'status' => 0
            ]);
            if ($user->type == '5') {
                Student::where('user_id', $user->id)->update(['status' => 0]);
            } elseif ($user->type == '2') {
                Employee::where('user_id', $user->id)->update(['status' => 0]);
            }
        } else {
            $user->update([
                'status' => 1
            ]);
            if ($user->type == '5') {
                Student::where('user_id', $user->id)->update(['status' => 1]);
            } elseif ($user->type == '2') {
                Employee::where('user_id', $user->id)->update(['status' => 1]);
            }
        }
    }


}

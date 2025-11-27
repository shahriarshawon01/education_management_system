<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Staff;
use App\Helpers\Helper;
use App\Models\Employee\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserProfileController extends Controller
{
    use Helper;
    public function __construct()
    {
        $this->model = new User();
    }
    public function index()
    {
        $user = auth()->user();
        $data = $this->model
            ->with('school:id,title', 'teacher:user_id,name,phone,nid,photo', 'staff:user_id,name,phone,nid,photo')
            ->where('id', $user->id)
            ->orderBy('id', 'desc')
            ->paginate(15);

        return returnData(2000, $data);
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        try {
            $user = Auth::user();
            $teacher = Employee::where('user_id', $user->id)->first();
            $staff = Employee::where('user_id', $user->id)->first();
            if ($user->type == 1) {
                $user->username = $request->input('username');
                $user->nid = $request->input('nid');
                $user->phone = $request->input('phone');
                $user->email = $request->input('email');
                $user->layout = $request->input('layout');
                $user->image = $request->input('image');
                $user->save();
                if ($request->input('password')) {
                    if (!Hash::check($request->old_password, $user->password)) {
                        return returnData(3000, null, 'Old password is incorrect');
                    }
                    $user->password = Hash::make($request->password);
                }
                $user->save();
                return returnData(2000, null, 'Successfully Updated');
            }
            if ($user->type == 2) {
                $teacher->nid = $request['teacher']['nid'];
                $teacher->phone = $request['teacher']['phone'];
                $teacher->photo = $request['teacher']['photo'];
                $teacher->save();

                $user->nid = $teacher->nid;
                $user->phone = $teacher->phone;
                $user->email = $request->input('email');
                $user->layout = $request->input('layout');
                $user->image = $request->input('image');
                $user->save();
                if ($request->input('password')) {
                    if (!Hash::check($request->old_password, $user->password)) {
                        return returnData(3000, null, 'Old password is incorrect');
                    }
                    $user->password = Hash::make($request->password);
                }
                $user->save();
                return returnData(2000, null, 'Successfully Updated');
            }
            if ($user->type == 3) {
                $staff->nid = $request['staff']['nid'];
                $staff->phone = $request['staff']['phone'];
                $staff->photo = $request['staff']['photo'];
                $staff->save();

                $user->nid = $staff->nid;
                $user->phone = $staff->phone;
                $user->email = $request->input('email');
                $user->layout = $request->input('layout');
                $user->image = $request->input('image');
                $user->save();
                if ($request->input('password')) {
                    if (!Hash::check($request->old_password, $user->password)) {
                        return returnData(3000, null, 'Old password is incorrect');
                    }
                    $user->password = Hash::make($request->password);
                }
                $user->save();
                return returnData(2000, null, 'Successfully Updated');
            }
        } catch (\Exception $exception) {
            return returnData(5000, $exception->getMessage(), $exception->getMessage());
        }


    }

    public function show($id)
    {
        //
    }


    public function edit($id)
    {
        //
    }


    public function update(Request $request, $id)
    {

    }


    public function destroy($id)
    {
        //
    }
}
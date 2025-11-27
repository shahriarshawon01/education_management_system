<?php

namespace App\Http\Controllers\Website;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ApplicantController extends Controller
{
    public function loginForm(){
        return view("frontend.auth.applicant_login");
    }
    public function applicant_login_store(Request $request){
        $this->validate($request, [
            'username' => 'required',
            'password' => 'required',
        ]);

        $loginData = [
             'username' => $request->input('username'),
             'password' => $request->input('password'),
        ];
        if (Auth::attempt($loginData)) {
            if(Auth::user()->type == 7){
                return redirect('/admission_request');
            }else{
                Toastr::error('Error!','Username Or Password does not match');
                return redirect()->back()->with('warning', 'Username Or Password does not match in our record.');
            }
        } else {
            Toastr::error('Error!','Username Or Password does not match');
            return redirect()->back()->with('warning', 'Username Or Password does not match in our record.');
        }
    }

    public function registerForm(){
        return view("frontend.auth.applicant_register");
    }

    public function applicant_register_store(Request $request){
        $this->validate($request, [
            'username' => 'required|string|max:255',
            'email' => 'email|unique:users',
            'password' => 'required|confirmed',
        ]);
        $input = $request->except(['password_confirmation']);
        $user = new User;
        $user->fill($input);
        $user->password = Hash::make($request->password);
        $user->role_id = 7;
        $user->type = 7;
        $user->save();
        Toastr::success('Success!','Registration Successfully');
        return redirect('/applicant_login')->with('success', 'Successfully Registered');
    }
    public function applicant_logout()
    {
        Auth::logout();
        return redirect('/')->with('success', 'Successfully Logged out');
    }

}

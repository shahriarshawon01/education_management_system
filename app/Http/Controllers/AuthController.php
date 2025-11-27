<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function loginForm()
    {
        return view('backend.auth.login');
    }
    public function studentLoginForm()
    {
        return view('backend.auth.studentLogin');
    }

    public function doLogin(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $isStudentRoute = $request->is('student/login');
            $isAdminRoute = $request->is('login');
            if ($isStudentRoute) {
                if ($user->type == 5) {
                    return redirect('student/dashboard');
                }
                Auth::logout();
                return back()->withErrors(['message' => 'Only students can login here.']);
            }

            if ($isAdminRoute) {
                if (in_array($user->type, [1, 2, 3, 4, 6])) {
                    return redirect('admin/dashboard');
                }
                Auth::logout();
                return back()->withErrors(['message' => 'Only staff users can login here.']);
            }

            Auth::logout();
            return back()->withErrors(['message' => 'Unauthorized login attempt.']);
        }

        return back()->withErrors(['message' => 'Invalid credentials.']);
    }

    public function logout(Request $request)
    {
        $user = Auth::user();
        Auth::logout();

        if ($user && $user->type == 5) {
            // Student
            return redirect()->route('student_login');
        }

        // Admin or staff
        return redirect()->route('login');
    }
}

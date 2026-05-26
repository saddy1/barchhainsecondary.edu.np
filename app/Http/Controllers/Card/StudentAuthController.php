<?php

namespace App\Http\Controllers\Card;

use App\Http\Controllers\Controller;

use App\Models\Card\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class StudentAuthController extends Controller
{
    public function showLogin()
    {
        if (session()->has('student_id')) {
            return redirect()->route('student.dashboard');
        }

        return view('card.student-portal.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'login' => ['required', 'string', 'max:150'],
            'password' => ['required', 'string'],
        ]);

        $login = trim($request->login);

        $user = User::query()
            ->where('email', $login)
            ->orWhere('student_code', $login)
            ->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return back()
                ->withInput()
                ->withErrors(['credentials' => 'The provided User ID/email or password is incorrect.']);
        }

        $student = $user->student ?: Student::where('user_id', $user->id)->first();

        if (! $student && $user->student_code) {
            $student = Student::where('roll_number', $user->student_code)->first();
            if ($student) {
                $student->forceFill(['user_id' => $user->id])->save();
            }
        }

        if (! $student || $student->member_type !== 'student') {
            return back()
                ->withInput()
                ->withErrors(['credentials' => 'This portal is only for linked student accounts.']);
        }

        session(['student_id' => $student->id]);
        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('student.dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->forget('student_id');
        $request->session()->regenerateToken();

        return redirect()->route('student.login')->with('success', 'You have been logged out.');
    }
}

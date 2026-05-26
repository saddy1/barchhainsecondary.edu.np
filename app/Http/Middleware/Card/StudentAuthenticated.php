<?php

namespace App\Http\Middleware\Card;

use App\Models\Card\Student;
use Closure;
use Illuminate\Http\Request;

class StudentAuthenticated
{
    public function handle(Request $request, Closure $next)
    {
        if (!session()->has('student_id')) {
            return redirect()->route('student.login')
                ->with('error', 'Please login to continue.');
        }

        $student = Student::find(session('student_id'));

        if (!$student) {
            $request->session()->forget('student_id');

            return redirect()->route('student.login')
                ->with('error', 'Please login to continue.');
        }

        if ($student->profile_completed_at === null && !$request->routeIs('student.profile.*') && !$request->routeIs('student.logout')) {
            return redirect()->route('student.profile.edit')
                ->with('info', 'Please complete your profile first.');
        }

        return $next($request);
    }
}

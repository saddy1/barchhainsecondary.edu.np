<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Admission; 
use App\Models\Setting;

class AdmissionsController extends Controller
{
    public function index()
    {
        $settings = [
            'academic_year' => Setting::get('admission_year', '2082 – 2083 B.S.'),
            'phone' => Setting::get('school_phone', '+977-123456789'),
            'email' => Setting::get('school_email', 'barchhainmavi2017@gmail.com'),
            'office_hours' => Setting::get('office_hours', 'Mon-Fri 9:00 AM - 5:00 PM'),
        ];

        return view('pages.admissions', compact('settings'));
    }

   public function storeAdmission(Request $request)
    {
        // 1. Validate the user's input
        $request->validate([
            'student_name' => 'required|string|max:255',
            'dob' => 'required|date',
            'gender' => 'required|string|in:Male,Female,Other',
            'applied_grade' => 'required|string|max:255',
            'guardian_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'required|string',
            'previous_school' => 'nullable|string|max:255',
        ]);

        // 2. Save exactly what was submitted to the database
        Admission::create($request->all());

        // 3. Send them back to the form with a success message
        return back()->with('success', 'Your admission inquiry has been submitted successfully! Our team will contact you shortly.');
    }
}

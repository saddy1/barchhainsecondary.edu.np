<?php

namespace App\Http\Controllers;

use App\Models\Vacancy;
use App\Models\VacancyApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VacancyController extends Controller
{
    public function index()
    {
        $vacancies = Vacancy::where('is_active', true)->latest()->get();

        $user = Auth::user();

        $appliedVacancyIds = [];
        if ($user) {
            $appliedVacancyIds = VacancyApplication::where('user_id', $user->id)
                ->pluck('vacancy_id')->toArray();
        }

        return view('pages.vacancy', compact('vacancies', 'user', 'appliedVacancyIds'));
    }

    public function createApplication(Vacancy $vacancy)
    {
        if ($vacancy->isExpired()) {
            return redirect()->route('vacancies')->with('error', 'This vacancy deadline has passed.');
        }

        if (VacancyApplication::where('user_id', Auth::id())->where('vacancy_id', $vacancy->id)->exists()) {
            return redirect()->route('account.applications.index')->with('status', 'You have already applied for this position.');
        }

        return view('pages.vacancy-apply', [
            'vacancy' => $vacancy,
            'user' => Auth::user(),
        ]);
    }

    public function apply(Request $request, Vacancy $vacancy)
    {
        $user = Auth::user();

        if (VacancyApplication::where('user_id', $user->id)->where('vacancy_id', $vacancy->id)->exists()) {
            return back()->with('error', 'You have already applied for this position.');
        }

        if ($vacancy->isExpired()) {
            return back()->with('error', 'This vacancy deadline has passed.');
        }

        $request->validate([
            'full_name'         => 'required|string|max:255',
            'email'             => 'required|email|max:255',
            'phone'             => 'required|string|max:20',
            'address'           => 'nullable|string|max:255',
            'qualification'     => 'required|string|max:255',
            'experience'        => 'nullable|string|max:100',
            'motivation'        => 'required|string|min:50|max:2000',
            'cv'                => 'required|file|mimes:pdf,doc,docx|max:5120',
            'date_of_birth'     => 'required|date',
            'gender'            => 'required|in:Male,Female,Other',
            'father_name'       => 'required|string|max:255',
            'mother_name'       => 'required|string|max:255',
            'permanent_address' => 'required|string|max:255',
            'temporary_address' => 'nullable|string|max:255',
            'citizenship_no'    => 'required|string|max:50',
            'citizen_front'     => 'required|file|mimes:jpg,jpeg,png,pdf|max:4096',
            'citizen_back'      => 'required|file|mimes:jpg,jpeg,png,pdf|max:4096',
            'profile_photo'     => 'required|file|mimes:jpg,jpeg,png|max:2048',
            'signature'         => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $application = VacancyApplication::create([
            'vacancy_id'         => $vacancy->id,
            'user_id'            => $user->id,
            'full_name'          => $request->full_name,
            'email'              => $request->email,
            'phone'              => $request->phone,
            'address'            => $request->address,
            'qualification'      => $request->qualification,
            'experience'         => $request->experience,
            'motivation'         => $request->motivation,
            'cv_path'            => $this->saveFile($request->file('cv'), 'vacancy-cvs'),
            'profile_photo'      => $this->saveFile($request->file('profile_photo'), 'vacancy-photos'),
            'citizen_front_path' => $this->saveFile($request->file('citizen_front'), 'vacancy-citizenship'),
            'citizen_back_path'  => $this->saveFile($request->file('citizen_back'), 'vacancy-citizenship'),
            'signature_path'     => $this->saveFile($request->file('signature'), 'vacancy-signatures'),
            'date_of_birth'      => $request->date_of_birth,
            'gender'             => $request->gender,
            'father_name'        => $request->father_name,
            'mother_name'        => $request->mother_name,
            'permanent_address'  => $request->permanent_address,
            'temporary_address'  => $request->temporary_address,
            'citizenship_no'     => $request->citizenship_no,
        ]);

        return redirect()
            ->route('account.applications.show', $application)
            ->with('success', 'Your application has been submitted successfully! We will contact you soon.');
    }

    public function myApplications()
    {
        $applications = VacancyApplication::with('vacancy')
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        return view('account.applications.index', compact('applications'));
    }

    public function showApplication(VacancyApplication $application)
    {
        abort_unless($application->user_id === Auth::id(), 403);

        $application->load('vacancy');

        return view('account.applications.show', compact('application'));
    }

    private function saveFile($file, string $folder): string
    {
        $dir = public_path("uploads/{$folder}");
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $file->move($dir, $filename);
        return "uploads/{$folder}/{$filename}";
    }
}

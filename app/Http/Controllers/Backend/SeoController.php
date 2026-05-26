<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SeoSetting;
use App\Models\Faculty;
use App\Models\Media;
use App\Models\Announcement;
use App\Services\SchoolSeoService;

class SeoController extends Controller
{
    public function index()
    {
        $seoSettings = SeoSetting::all()->keyBy('page_name');
        return view('backend.seo.index', compact('seoSettings'));
    }

    public function generate(Request $request, SchoolSeoService $seoService)
    {
        $page = $request->page_name;
        $context = '';

        // Feed hyper-specific data to the AI based on the page
        switch ($page) {
            case 'home':
                $context = "This is the main landing page for Barchhain Secondary School, a government school in Barchhain, Doti, Sudurpashchim Province. Emphasize quality school education, discipline, values, and community service.";
                break;
            case 'about':
                $context = "This is the About Us page. Focus on the school's history, mission, vision, and reputation as a trusted government educational institution in Sudurpashchim Province.";
                break;
            case 'admissions':
                $context = "This is the Admissions page. Keywords should target parents looking for admission open, school enrollment, and how to apply in Barchhain, Doti.";
                break;
            case 'elementary':
                $context = "This is the Kids School (Nursery to Grade 3) page. Focus on child care, Montessori methods, early childhood development, and safe learning environment.";
                break;
            case 'middle_school':
                $context = "This is the Middle School (Grade 4 to 8) page. Focus on foundational learning, interactive classrooms, and student growth.";
                break;
            case 'secondary':
                $context = "This is the High School page. Focus on secondary education, SEE preparation, NEB-aligned learning, practical skills, and student guidance in Barchhain, Doti.";
                break;
            case 'faculty':
                $count = Faculty::where('is_active', true)->count();
                $departments = Faculty::select('category')->distinct()->pluck('category')->implode(', ');
                $context = "This is the Faculty directory. We have {$count} expert teachers and staff across departments: {$departments}. Focus on highly qualified teachers.";
                break;
            case 'gallery':
                $count = Media::count();
                $context = "This is the Photo Gallery featuring {$count} images of campus life, sports events, academics, and cultural programs.";
                break;
            case 'notices':
                $latest = Announcement::latest()->take(3)->pluck('title')->implode(' | ');
                $context = "This is the Notice Board. Latest updates include: {$latest}. Focus on exam routines, school events, and official updates.";
                break;
            default:
                $context = "General school information page for Barchhain Secondary School.";
                break;
        }

        try {
            $seoData = $seoService->generateSeoData($page, $context);
            return response()->json($seoData);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'page_name' => 'required|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
        ]);

        SeoSetting::updateOrCreate(
            ['page_name' => $request->page_name],
            [
                'meta_title' => $request->meta_title,
                'meta_description' => $request->meta_description,
                'meta_keywords' => $request->meta_keywords,
            ]
        );

        return back()->with('success', 'SEO Settings saved successfully for ' . str_replace('_', ' ', $request->page_name));
    }
}

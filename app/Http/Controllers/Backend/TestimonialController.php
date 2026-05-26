<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use Illuminate\Http\Request;

class TestimonialController extends Controller
{
    public function index()
    {
        $testimonials = Testimonial::latest()->get();
        return view('backend.testimonials.index', compact('testimonials'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'role' => 'required|string|max:255',
            'info' => 'required|string|max:1000',
            'category' => 'required|in:home,elementary,primary,secondary'
        ]);

        Testimonial::create([
            'name' => $request->name,
            'role' => $request->role,
            'content' => $request->info,
            'category' => $request->category,
            'is_active' => $request->has('is_active')
        ]);

        return back()->with('success', 'Testimonial added successfully.');
    }

    public function destroy(Testimonial $testimonial)
    {
        $testimonial->delete();
        return back()->with('success', 'Testimonial deleted.');
    }

    public function toggle(Testimonial $testimonial)
    {
        $testimonial->update(['is_active' => !$testimonial->is_active]);
        return back()->with('success', 'Testimonial status updated.');
    }
}
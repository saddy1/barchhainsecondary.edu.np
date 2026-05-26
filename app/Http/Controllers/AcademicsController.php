<?php

namespace App\Http\Controllers;

class AcademicsController extends Controller
{
    public function elementary()
    {
        $testimonials = \App\Models\Testimonial::where('is_active', true)->where('category', 'elementary')->latest()->get();
        return view('pages.academics-elementary', compact('testimonials'));
    }
    public function primary()
    {
        $testimonials = \App\Models\Testimonial::where('is_active', true)->where('category', 'primary')->latest()->get();
        return view('pages.academics-primary', compact('testimonials'));
    }
    public function secondary()
    {
        $testimonials = \App\Models\Testimonial::where('is_active', true)->where('category', 'secondary')->latest()->get();
        return view('pages.academics-secondary', compact('testimonials'));
    }
}

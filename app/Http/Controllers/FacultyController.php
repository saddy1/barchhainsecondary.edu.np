<?php

namespace App\Http\Controllers;

use App\Models\Faculty;
use Illuminate\Http\Request;

class FacultyController extends Controller
{
   public function index()
    {
        // Fetch categories dynamically so your filter buttons always match your data
        $faculties = Faculty::where('is_active', true)
                            ->orderBy('order', 'asc')
                            ->get();

        $categories = $faculties->pluck('category')->unique();

        return view('pages.faculty', compact('faculties', 'categories'));
    }
}
<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Faculty;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class FacultyController extends Controller
{
    public function index()
    {
        $faculties = Faculty::orderBy('order', 'asc')->paginate(15);
        return view('backend.faculty.index', compact('faculties'));
    }

    public function create()
    {
        return view('backend.faculty.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'role' => 'required|string|max:255',
            'category' => 'required|string',
            'education' => 'required|string',
            'image_file' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'order' => 'nullable|integer',
        ]);

        $data = $request->except('image_file');

        if ($request->hasFile('image_file')) {
            $file = $request->file('image_file');
            $filename = time() . '_' . \Str::slug($request->name) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('faculty'), $filename);
            $data['image'] = 'faculty/' . $filename;
        }

        Faculty::create($data);

        return redirect()->route('admin.faculty.index')->with('success', 'Faculty member added successfully!');
    }

    public function edit(Faculty $faculty)
    {
        return view('backend.faculty.edit', compact('faculty'));
    }

    public function update(Request $request, Faculty $faculty)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'role' => 'required|string|max:255',
            'category' => 'required|string',
            'education' => 'required|string',
            'image_file' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'order' => 'nullable|integer',
        ]);

        $data = $request->except('image_file');

        if ($request->hasFile('image_file')) {
            // Delete old image
            if ($faculty->image) {
                $oldPath = public_path($faculty->image);
                if (File::exists($oldPath)) File::delete($oldPath);
            }

            $file = $request->file('image_file');
            $filename = time() . '_' . \Str::slug($request->name) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('faculty'), $filename);
            $data['image'] = 'faculty/' . $filename;
        }

        $faculty->update($data);

        return redirect()->route('admin.faculty.index')->with('success', 'Faculty profile updated!');
    }

    public function destroy(Faculty $faculty)
    {
        if ($faculty->image) {
            $path = public_path($faculty->image);
            if (File::exists($path)) File::delete($path);
        }

        $faculty->delete();

        return redirect()->route('admin.faculty.index')->with('success', 'Faculty member removed.');
    }
}
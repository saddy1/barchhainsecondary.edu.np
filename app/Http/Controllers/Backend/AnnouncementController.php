<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File; // Added for file deletion

class AnnouncementController extends Controller
{
    public function index()
    {
        $announcements = Announcement::latest()->paginate(10);
        return view('backend.announcements.index', compact('announcements'));
    }

    public function create()
    {
        return view('backend.announcements.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:notice,event,news',
            'category' => 'required|string|max:50',
            'content' => 'required',
            'image_type' => 'required|in:upload,link',
            'image_file' => 'nullable|file|mimes:jpeg,png,jpg,webp,pdf|max:5120',
            'image_link' => 'nullable|url',
        ]);

        $data = $request->except(['image_file', 'image_link']);

        // Generate unique slug
        $data['slug'] = Str::slug($request->title) . '-' . uniqid();

        // Handle Image
        if ($request->image_type === 'upload' && $request->hasFile('image_file')) {
            $file = $request->file('image_file');
            // Create a unique filename
            $filename = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();

            // Move directly to public/announcements
            $file->move(public_path('announcements'), $filename);

            // Save the relative path in the database
            $data['featured_image'] = 'announcements/' . $filename;
        } elseif ($request->image_type === 'link' && $request->filled('image_link')) {
            // Automatically convert Google Drive sharing links to direct viewing links
            $link = $request->image_link;
            if (preg_match('/drive\.google\.com\/file\/d\/(.*?)\//', $link, $matches)) {
                $link = 'https://drive.google.com/uc?export=view&id=' . $matches[1];
            }
            $data['featured_image'] = $link;
        }

        Announcement::create($data);

        return redirect()->route('admin.announcements.index')->with('success', 'Post created successfully!');
    }

    public function edit(Announcement $announcement)
    {
        return view('backend.announcements.edit', compact('announcement'));
    }

    public function update(Request $request, Announcement $announcement)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:notice,event,news',
            'category' => 'required|string|max:50',
            'content' => 'required',
            'image_type' => 'required|in:upload,link',
            'image_file' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'image_link' => 'nullable|url',
        ]);

        $data = $request->except(['image_file', 'image_link', '_token', '_method']);

        // Update Slug only if the title changed
        if ($request->title !== $announcement->title) {
            $data['slug'] = Str::slug($request->title) . '-' . uniqid();
        }

        // Handle Image Update
        if ($request->image_type === 'upload' && $request->hasFile('image_file')) {

            // 1. Delete old image from public directory if it exists
            if ($announcement->image_type === 'upload' && $announcement->featured_image) {
                $oldImagePath = public_path($announcement->featured_image);
                if (File::exists($oldImagePath)) {
                    File::delete($oldImagePath);
                }
            }

            // 2. Store new image directly in public/announcements
            $file = $request->file('image_file');
            $filename = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('announcements'), $filename);
            $data['featured_image'] = 'announcements/' . $filename;
        } elseif ($request->image_type === 'link' && $request->filled('image_link')) {
            $link = $request->image_link;
            if (preg_match('/drive\.google\.com\/file\/d\/(.*?)\//', $link, $matches)) {
                $link = 'https://drive.google.com/uc?export=view&id=' . $matches[1];
            }
            $data['featured_image'] = $link;
        }

        // If post type changed away from 'event', clear event fields
        if ($request->type !== 'event') {
            $data['event_date'] = null;
            $data['event_time'] = null;
            $data['event_location'] = null;
        }

        $announcement->update($data);

        return redirect()->route('admin.announcements.index')->with('success', 'Post updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Announcement $announcement)
    {
        // 1. Check if the image was an uploaded file (not a Drive link) and exists in DB
        if ($announcement->image_type === 'upload' && $announcement->featured_image) {

            // 2. Get the full absolute path to the image in the public folder
            // Because we saved it as 'announcements/filename.jpg', public_path() finds it perfectly.
            $imagePath = public_path($announcement->featured_image);

            // 3. If the file actually exists on the server, delete it
            if (File::exists($imagePath)) {
                File::delete($imagePath);
            }
        }

        // 4. Delete the database record
        $announcement->delete();

        return redirect()->route('admin.announcements.index')
            ->with('success', 'Post and its associated image were deleted successfully!');
    }
    /**
     * Force PDFs to open inline in the browser instead of downloading.
     */
    public function viewFile(Announcement $announcement)
    {
        // Check if it's a locally uploaded PDF
        if ($announcement->image_type === 'upload' && Str::endsWith(strtolower($announcement->featured_image), '.pdf')) {
            $path = public_path($announcement->featured_image);
            
            if (File::exists($path)) {
                // Return the file with headers forcing it to display in the browser
                return response()->file($path, [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'inline; filename="' . basename($path) . '"'
                ]);
            }
        }

        // If it's a regular image or a Drive link, just redirect to its normal URL
        return redirect($announcement->image_url);
    }
}

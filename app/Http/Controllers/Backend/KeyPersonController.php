<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\KeyPerson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class KeyPersonController extends Controller
{
    public function index()
    {
        $keyPersons = KeyPerson::orderBy('sort_order')->orderBy('name')->paginate(20);

        return view('backend.key-persons.index', compact('keyPersons'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'designation' => 'required|string|max:255',
            'phone'       => 'nullable|string|max:50',
            'email'       => 'nullable|email|max:255',
            'sort_order'  => 'nullable|integer|min:0|max:9999',
            'is_active'   => 'nullable|boolean',
            'photo'       => 'nullable|file|mimes:jpg,jpeg,png,webp|max:4096',
        ]);

        $data['is_active']  = $request->boolean('is_active');
        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);

        if ($request->hasFile('photo')) {
            $data['photo'] = $this->storePhoto($request);
        }

        KeyPerson::create($data);

        return redirect()->route('admin.key-persons.index')->with('success', 'Key person added successfully.');
    }

    public function update(Request $request, KeyPerson $keyPerson)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'designation' => 'required|string|max:255',
            'phone'       => 'nullable|string|max:50',
            'email'       => 'nullable|email|max:255',
            'sort_order'  => 'nullable|integer|min:0|max:9999',
            'is_active'   => 'nullable|boolean',
            'photo'       => 'nullable|file|mimes:jpg,jpeg,png,webp|max:4096',
        ]);

        $data['is_active']  = $request->boolean('is_active');
        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);

        if ($request->hasFile('photo')) {
            $this->deletePhoto($keyPerson->photo);
            $data['photo'] = $this->storePhoto($request);
        }

        $keyPerson->update($data);

        return redirect()->route('admin.key-persons.index')->with('success', 'Key person updated successfully.');
    }

    public function destroy(KeyPerson $keyPerson)
    {
        $this->deletePhoto($keyPerson->photo);
        $keyPerson->delete();

        return response()->json(['success' => true]);
    }

    public function toggleActive(KeyPerson $keyPerson)
    {
        $keyPerson->update(['is_active' => ! $keyPerson->is_active]);

        return response()->json(['success' => true, 'is_active' => $keyPerson->is_active]);
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function storePhoto(Request $request): string
    {
        $file     = $request->file('photo');
        $filename = 'person-' . time() . '-' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
        File::ensureDirectoryExists(public_path('uploads/key-persons'));
        $file->move(public_path('uploads/key-persons'), $filename);

        return 'uploads/key-persons/' . $filename;
    }

    private function deletePhoto(?string $path): void
    {
        if (! $path || str_starts_with($path, 'assets/')) {
            return;
        }

        $fullPath = public_path($path);
        if (File::exists($fullPath)) {
            File::delete($fullPath);
        }
    }
}

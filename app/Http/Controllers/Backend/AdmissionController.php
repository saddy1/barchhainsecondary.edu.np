<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Admission;
use Illuminate\Http\Request;

class AdmissionController extends Controller
{
    // List all admissions
    public function index(Request $request)
    {
        $query = Admission::latest();

        // Optional: Filter by status if requested
        if ($request->filled('status') && $request->status !== 'All') {
            $query->where('status', $request->status);
        }

        $admissions = $query->paginate(15)->withQueryString();
        
        return view('backend.admissions.index', compact('admissions'));
    }

    // View specific admission details
    public function show(Admission $admission)
    {
        return view('backend.admissions.show', compact('admission'));
    }

    // Update application status and remarks
    public function update(Request $request, Admission $admission)
    {
        $request->validate([
            'status' => 'required|string|in:Pending,Reviewed,Accepted,Rejected',
            'admin_remarks' => 'nullable|string'
        ]);

        $admission->update([
            'status' => $request->status,
            'admin_remarks' => $request->admin_remarks
        ]);

        return back()->with('success', 'Admission application updated successfully.');
    }

    // Delete an application
    public function destroy(Admission $admission)
    {
        $admission->delete();
        return redirect()->route('admin.admissions.index')->with('success', 'Application deleted successfully.');
    }
}
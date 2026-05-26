<?php

namespace App\Http\Controllers\Hajiri;

use App\Http\Controllers\Controller;
use App\Models\Hajiri\StaffCardRequest;
use Illuminate\Http\Request;

class StaffCardRequestController extends Controller
{
    public function index()
    {
        $requests = StaffCardRequest::where('user_id', auth()->id())
            ->latest()
            ->get();

        $hasPending = $requests->where('status', 'pending')->isNotEmpty();

        return view('hajiri.staff-card-request.index', compact('requests', 'hasPending'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'reason' => ['nullable', 'string', 'max:255'],
        ]);

        // One active (pending/approved/printed) request at a time
        $active = StaffCardRequest::where('user_id', auth()->id())
            ->whereIn('status', ['pending', 'approved', 'printed'])
            ->exists();

        if ($active) {
            return back()->with('error', 'You already have an active card request in progress.');
        }

        StaffCardRequest::create([
            'user_id' => auth()->id(),
            'reason'  => $request->reason,
            'status'  => 'pending',
        ]);

        return back()->with('message', 'ID card request submitted successfully.');
    }

    // Admin: list all staff card requests
    public function adminIndex()
    {
        $requests = StaffCardRequest::with('user')
            ->latest()
            ->get()
            ->groupBy('status');

        $all = StaffCardRequest::with('user')->latest()->paginate(20);

        return view('hajiri.staff-card-request.admin', compact('requests', 'all'));
    }

    public function updateStatus(Request $request, StaffCardRequest $staffCardRequest)
    {
        $request->validate([
            'status'     => ['required', 'in:approved,printed,collected,rejected'],
            'admin_note' => ['nullable', 'string'],
        ]);

        $staffCardRequest->update([
            'status'     => $request->status,
            'admin_note' => $request->admin_note,
        ]);

        return back()->with('message', 'Request status updated.');
    }
}

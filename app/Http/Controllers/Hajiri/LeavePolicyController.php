<?php

namespace App\Http\Controllers\Hajiri;

use App\Http\Controllers\Controller;
use App\Models\Hajiri\LeavePolicy;
use Illuminate\Http\Request;

class LeavePolicyController extends Controller
{
    public function index()
    {
        $policies = LeavePolicy::orderBy('applicable_to')->orderBy('name')->get();
        return view('hajiri.leave-policies.index', compact('policies'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'          => 'required|max:100',
            'short_code'    => 'required|max:10',
            'days_allowed'  => 'required|integer|min:1|max:365',
            'period_type'   => 'required|in:annual,tenure',
            'applicable_to' => 'required|in:all,teaching,non_teaching',
        ]);

        LeavePolicy::create($request->only(['name', 'short_code', 'days_allowed', 'period_type', 'applicable_to']));
        return redirect()->back()->with('message', 'Leave policy added successfully.');
    }

    public function update(Request $request, LeavePolicy $leavePolicy)
    {
        $request->validate([
            'name'          => 'required|max:100',
            'short_code'    => 'required|max:10',
            'days_allowed'  => 'required|integer|min:1|max:365',
            'period_type'   => 'required|in:annual,tenure',
            'applicable_to' => 'required|in:all,teaching,non_teaching',
        ]);

        $leavePolicy->update($request->only(['name', 'short_code', 'days_allowed', 'period_type', 'applicable_to']));
        return redirect()->back()->with('message', 'Leave policy updated.');
    }

    public function destroy(LeavePolicy $leavePolicy)
    {
        $leavePolicy->delete();
        return redirect()->back()->with('message', 'Leave policy deleted.');
    }

    public function toggle(LeavePolicy $leavePolicy)
    {
        $leavePolicy->update(['is_active' => ! $leavePolicy->is_active]);
        return redirect()->back()->with('message', 'Status updated.');
    }
}

<?php

namespace App\Http\Controllers\Hajiri;

use App\Http\Controllers\Controller;
use App\Models\Hajiri\LeavePolicy;
use App\Models\Hajiri\LeaveRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LeaveRequestController extends Controller
{
    private NepaliCalendarController $npCal;

    public function __construct()
    {
        $this->npCal = new NepaliCalendarController();
    }

    /**
     * Admin: all leave requests, filterable by status.
     */
    public function index(Request $request)
    {
        $status = $request->query('status', 'pending');
        $query  = LeaveRequest::with(['user', 'policy', 'approvedBy'])->latest();

        if (in_array($status, ['pending', 'approved', 'rejected'])) {
            $query->where('status', $status);
        }

        $requests = $query->get();
        $counts = [
            'all'      => LeaveRequest::count(),
            'pending'  => LeaveRequest::where('status', 'pending')->count(),
            'approved' => LeaveRequest::where('status', 'approved')->count(),
            'rejected' => LeaveRequest::where('status', 'rejected')->count(),
        ];

        return view('hajiri.leave-requests.index', compact('requests', 'status', 'counts'));
    }

    /**
     * Employee: their own leave balance + history + apply form.
     */
    public function myLeaves()
    {
        $user     = auth()->user();
        $policies = LeavePolicy::where('is_active', true)
            ->where(function ($q) use ($user) {
                $q->where('applicable_to', 'all')
                  ->orWhere('applicable_to', $this->userCategory($user));
            })
            ->orderBy('name')
            ->get();

        $balances = $policies->map(fn ($p) => array_merge(
            ['policy' => $p],
            $this->calculateBalance($user->id, $p)
        ));

        $myRequests = LeaveRequest::with('policy')
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        return view('hajiri.leave-requests.my', compact('balances', 'myRequests', 'policies'));
    }

    /**
     * Employee submits a leave request.
     */
    public function store(Request $request)
    {
        $request->validate([
            'leave_policy_id' => 'required|exists:leave_policies,id',
            'start_date_bs'   => 'required',
            'end_date_bs'     => 'required',
            'reason'          => 'nullable|max:500',
        ]);

        $rawStart = $request->input('start_date_bs');
        $rawEnd   = $request->input('end_date_bs');
        $startBS  = is_array($rawStart) ? ($rawStart[0] ?? '') : ($rawStart ?? '');
        $endBS    = is_array($rawEnd)   ? ($rawEnd[0]   ?? '') : ($rawEnd   ?? '');

        $startAD = $this->bsToAD($startBS);
        $endAD   = $this->bsToAD($endBS);

        if ($endAD < $startAD) {
            return redirect()->back()->withErrors(['end_date_bs' => 'End date must be on or after start date.'])->withInput();
        }

        $days = Carbon::parse($startAD)->diffInDays(Carbon::parse($endAD)) + 1;

        // Check balance for annual leaves
        $policy = LeavePolicy::findOrFail($request->leave_policy_id);
        $balance = $this->calculateBalance(auth()->id(), $policy);
        if ($days > $balance['remaining']) {
            return redirect()->back()
                ->withErrors(['leave_policy_id' => "Insufficient leave balance. You have {$balance['remaining']} day(s) remaining."])
                ->withInput();
        }

        LeaveRequest::create([
            'user_id'         => auth()->id(),
            'leave_policy_id' => $request->leave_policy_id,
            'start_date'      => $startAD,
            'end_date'        => $endAD,
            'days_count'      => $days,
            'reason'          => $request->reason,
            'status'          => 'pending',
        ]);

        return redirect()->back()->with('message', 'Leave request submitted successfully.');
    }

    /**
     * Admin approves a request.
     */
    public function approve(Request $request, $id)
    {
        $leave = LeaveRequest::findOrFail($id);
        $leave->update([
            'status'       => 'approved',
            'approved_by'  => auth()->id(),
            'admin_remarks' => $request->admin_remarks,
        ]);
        return redirect()->back()->with('message', 'Leave request approved.');
    }

    /**
     * Admin rejects a request.
     */
    public function reject(Request $request, $id)
    {
        $request->validate(['admin_remarks' => 'required|max:300']);
        $leave = LeaveRequest::findOrFail($id);
        $leave->update([
            'status'        => 'rejected',
            'approved_by'   => auth()->id(),
            'admin_remarks' => $request->admin_remarks,
        ]);
        return redirect()->back()->with('message', 'Leave request rejected.');
    }

    /**
     * Employee cancels their own pending request.
     */
    public function destroy($id)
    {
        $leave = LeaveRequest::where('id', $id)->where('user_id', auth()->id())->firstOrFail();
        if ($leave->status !== 'pending') {
            return redirect()->back()->withErrors(['error' => 'Only pending requests can be cancelled.']);
        }
        $leave->delete();
        return redirect()->back()->with('message', 'Leave request cancelled.');
    }

    // ── Helpers ─────────────────────────────────────────────────────────────

    private function calculateBalance(int $userId, LeavePolicy $policy): array
    {
        $query = LeaveRequest::where('user_id', $userId)
            ->where('leave_policy_id', $policy->id)
            ->where('status', 'approved');

        if ($policy->period_type === 'annual') {
            [$fyStart, $fyEnd] = $this->currentFiscalYear();
            $query->where('start_date', '>=', $fyStart)->where('start_date', '<=', $fyEnd);
        }

        $used = (int) $query->sum('days_count');
        return [
            'used'      => $used,
            'remaining' => max(0, $policy->days_allowed - $used),
            'total'     => $policy->days_allowed,
        ];
    }

    private function currentFiscalYear(): array
    {
        $today   = Carbon::now();
        $bsToday = $this->npCal->ad_2_bs($today->year, $today->month, $today->day);
        $bsMonth = (int) $bsToday['month'];
        $bsYear  = (int) $bsToday['year'];

        // Fiscal year starts Shrawan (month 4). If before month 4, FY started last year.
        $fyYear = $bsMonth >= 4 ? $bsYear : $bsYear - 1;

        $start   = $this->npCal->bs_2_ad($fyYear, 4, 1);
        $fyStart = sprintf('%04d-%02d-%02d', $start['year'], $start['month'], $start['date']);

        $endYear    = $fyYear + 1;
        $ashadhDays = $this->npCal->bs[intval(substr((string) $endYear, -2))][3];
        $end        = $this->npCal->bs_2_ad($endYear, 3, $ashadhDays);
        $fyEnd      = sprintf('%04d-%02d-%02d', $end['year'], $end['month'], $end['date']);

        return [$fyStart, $fyEnd];
    }

    private function bsToAD(string $bsDate): string
    {
        [$y, $m, $d] = explode('-', $bsDate);
        $ad = $this->npCal->bs_2_ad((int) $y, (int) $m, (int) $d);
        return sprintf('%04d-%02d-%02d', $ad['year'], $ad['month'], $ad['date']);
    }

    private function userCategory(User $user): string
    {
        $label = strtolower($user->working_at->label ?? '');
        return str_contains($label, 'academic') || str_contains($label, 'teacher')
            ? 'teaching'
            : 'non_teaching';
    }
}

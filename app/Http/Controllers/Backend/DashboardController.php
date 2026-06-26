<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\ContactMessage;
use App\Models\Admission; // The new model we just made
use App\Models\LibraryLoan;
use App\Models\Work\WorkTask;
use App\Models\Work\WorkTaskSubmission;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $today = Carbon::today();
        $user = auth()->user();
        $workTaskCount = 0;

        if (Schema::hasTable('work_tasks') && Schema::hasTable('work_task_submissions') && $user?->canAccess(['work-tasks.view', 'work-tasks.submit', 'work-tasks.review', 'work-tasks.create'])) {
            $workTaskCount = $user->canAccess(['work-tasks.review', 'work-tasks.create'])
                ? WorkTaskSubmission::where('status', 'submitted')->count()
                : WorkTask::pendingForUser($user)->count();
        }

        // 1. TOP STATS CARDS
        $stats = [
            'new_admissions'  => Admission::where('status', 'Pending')->count(),
            'unread_messages' => ContactMessage::where('is_read', false)->count(),
            'upcoming_events' => Announcement::where('type', 'event')->where('event_date', '>=', $today)->count(),
            'active_notices'  => Announcement::whereIn('type', ['notice', 'news'])->where('is_published', true)->count(),
            'work_reviews'    => $workTaskCount,
        ];

        // 2. DASHBOARD PANELS (Fetch latest 5 of everything)
        $recentAdmissions = Admission::latest()->take(5)->get();
        
        $recentMessages = ContactMessage::where('is_read', false)->latest()->take(5)->get();
        
        $nextEvents = Announcement::where('type', 'event')
            ->where('event_date', '>=', $today)
            ->orderBy('event_date', 'asc')
            ->take(5)
            ->get();
            
        $recentPosts = Announcement::whereIn('type', ['notice', 'news'])
            ->latest()
            ->take(5)
            ->get();

        // Library stats (only if module table exists)
        $libraryStats = null;
        if (Schema::hasTable('library_loans') && \App\Services\ModuleService::enabled('library')) {
            $libraryStats = [
                'overdue'   => LibraryLoan::where('status', 'issued')->whereDate('due_date', '<', now()->toDateString())->count(),
                'issued'    => LibraryLoan::where('status', 'issued')->count(),
                'fine_due'  => LibraryLoan::sum(\Illuminate\Support\Facades\DB::raw('GREATEST(fine_amount - fine_paid, 0)')),
            ];
        }

        return view('backend.dashboard', compact('stats', 'recentAdmissions', 'recentMessages', 'nextEvents', 'recentPosts', 'libraryStats'));
    }
}

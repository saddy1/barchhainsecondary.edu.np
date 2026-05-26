<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\ContactMessage;
use App\Models\Admission; // The new model we just made
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $today = Carbon::today();

        // 1. TOP STATS CARDS
        $stats = [
            'new_admissions'  => Admission::where('status', 'Pending')->count(),
            'unread_messages' => ContactMessage::where('is_read', false)->count(),
            'upcoming_events' => Announcement::where('type', 'event')->where('event_date', '>=', $today)->count(),
            'active_notices'  => Announcement::whereIn('type', ['notice', 'news'])->where('is_published', true)->count(),
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

        return view('backend.dashboard', compact('stats', 'recentAdmissions', 'recentMessages', 'nextEvents', 'recentPosts'));
    }
}
<?php


namespace App\Http\Controllers;
use App\Models\Announcement;
use Carbon\Carbon;
use Illuminate\Http\Request;

class EventsController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

    // 1. Fetch Upcoming Events (Future or Today)
    $upcomingEvents = Announcement::where('type', 'event')
        ->where('is_published', true)
        ->where('event_date', '>=', $today)
        ->orderBy('event_date', 'asc') // Closest event first
        ->get();

    // 2. Fetch Past Events (Dates before today)
    $pastEvents = Announcement::where('type', 'event')
        ->where('is_published', true)
        ->where('event_date', '<', $today)
        ->latest('event_date') // Most recently finished first
        ->take(6) // Limit archive display
        ->get();
        return view('pages.events', compact('upcomingEvents', 'pastEvents'));
    }
    public function show($event)
    {
        return view('pages.event-detail', compact('event'));
    }

    //news and events
    public function news()
    {
        $today = \Carbon\Carbon::today();

        // 1. Fetch Upcoming Events
        $upcomingEvents = Announcement::where('type', 'event')
            ->where('is_published', true)
            ->where('event_date', '>=', $today)
            ->orderBy('event_date', 'asc')
            ->take(3) // 1 Featured + 2 List items looks best
            ->get();

        // 2. Fetch PAST Events (Newly Added!)
        $pastEvents = Announcement::where('type', 'event')
            ->where('is_published', true)
            ->where('event_date', '<', $today)
            ->orderBy('event_date', 'desc') // Show most recently finished first
            ->take(3)
            ->get();

        // 3. Fetch Notices
        $notices = Announcement::whereIn('type', ['notice', 'news'])
            ->where('is_published', true)
            ->latest()
            ->paginate(10);

        $noticeCategories = Announcement::whereIn('type', ['notice', 'news'])
            ->where('is_published', true)
            ->whereNotNull('category')
            ->distinct()
            ->pluck('category');

        return view('pages.news-event', compact('upcomingEvents', 'pastEvents', 'notices', 'noticeCategories'));
    }
  
}

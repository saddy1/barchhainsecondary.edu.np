<?php

namespace App\Http\Controllers;
use App\Models\Announcement;
use App\Models\PopupNotice;
use App\Models\Testimonial;
use App\Models\Vacancy;
use App\Models\Media;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
{
    // Fetch latest 3 notices
    $homeNotices = Announcement::where('type', 'notice')
        ->where('is_published', true)
        ->latest()
        ->take(3)
        ->get();

    // Fetch latest 3 upcoming events
    $homeEvents = Announcement::where('type', 'event')
        ->where('is_published', true)
        ->latest()
        ->take(3)
        ->get();

        $testimonials = Testimonial::where('is_active', true)->latest()->get();

        // Fetch campus design images uploaded via admin (category = 'Campus')
        $campusImages = Media::where('category', 'Campus')
            ->latest()
            ->take(8)
            ->get();

        $popups = PopupNotice::where('is_active', true)->latest()->take(3)->get();
    return view('pages.home', compact('homeNotices', 'homeEvents', 'popups', 'testimonials', 'campusImages'));
}

    public function sitemap()
    {
        $now = now()->toAtomString();

        // Static pages
        $urls = [
            ['loc' => route('home'),                 'priority' => '1.0', 'freq' => 'daily',   'lastmod' => $now],
            ['loc' => route('about'),                'priority' => '0.9', 'freq' => 'monthly', 'lastmod' => $now],
            ['loc' => route('admissions'),           'priority' => '0.9', 'freq' => 'weekly',  'lastmod' => $now],
            ['loc' => route('academics.elementary'), 'priority' => '0.8', 'freq' => 'monthly', 'lastmod' => $now],
            ['loc' => route('academics.primary'),    'priority' => '0.8', 'freq' => 'monthly', 'lastmod' => $now],
            ['loc' => route('academics.secondary'),  'priority' => '0.8', 'freq' => 'monthly', 'lastmod' => $now],
            ['loc' => route('gallery'),              'priority' => '0.7', 'freq' => 'weekly',  'lastmod' => $now],
            ['loc' => route('news'),                 'priority' => '0.8', 'freq' => 'daily',   'lastmod' => $now],
            ['loc' => route('events'),               'priority' => '0.8', 'freq' => 'weekly',  'lastmod' => $now],
            ['loc' => route('notices'),              'priority' => '0.8', 'freq' => 'daily',   'lastmod' => $now],
            ['loc' => route('frontend.faculty'),     'priority' => '0.7', 'freq' => 'monthly', 'lastmod' => $now],
            ['loc' => route('vacancies'),            'priority' => '0.8', 'freq' => 'weekly',  'lastmod' => $now],
            ['loc' => route('contact'),              'priority' => '0.7', 'freq' => 'yearly',  'lastmod' => $now],
            ['loc' => route('privacy'),              'priority' => '0.3', 'freq' => 'yearly',  'lastmod' => $now],
            ['loc' => route('terms'),                'priority' => '0.3', 'freq' => 'yearly',  'lastmod' => $now],
        ];

        // Dynamic announcement pages (news & events)
        $announcements = Announcement::where('is_published', true)
            ->whereNotNull('slug')
            ->latest()
            ->get(['slug', 'type', 'updated_at']);

        foreach ($announcements as $item) {
            $urls[] = [
                'loc'      => route('news.show', $item->slug),
                'priority' => '0.6',
                'freq'     => 'monthly',
                'lastmod'  => $item->updated_at->toAtomString(),
            ];
        }

        return response()->view('pages.sitemap', compact('urls'))
                         ->header('Content-Type', 'application/xml');
    }

  
}

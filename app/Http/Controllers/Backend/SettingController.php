<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Support\SiteSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Throwable;

class SettingController extends Controller
{
    public function index()
    {
        abort_unless(request()->user()?->isSuperAdmin(), 403);

        $settings = Setting::pluck('value', 'key')->toArray();

        $websiteImages = [
            'home_hero_image'            => ['label' => 'Home Hero School Image',      'fallback' => 'assets/image/school_building.jpg'],
            'academics_elementary_image' => ['label' => 'Basic / Early Level Image',   'fallback' => 'assets/image/kids.jpg'],
            'academics_primary_image'    => ['label' => 'Primary / Basic Level Image', 'fallback' => 'assets/image/school_building.jpg'],
            'academics_secondary_image'  => ['label' => 'Secondary Level Image',       'fallback' => 'assets/image/school_building.jpg'],
        ];

        return view('backend.settings.index', compact('settings', 'websiteImages'));
    }

    public function update(Request $request)
    {
        abort_unless($request->user()?->isSuperAdmin(), 403);

        $rules = [
            'admission_year' => 'required|string|max:255',
            'school_phone'   => 'required|string|max:255',
            'school_email'   => 'required|email|max:255',
            'office_hours'   => 'required|string|max:255',
            'site_name_en'   => 'required|string|max:255',
            'site_name_ne'   => 'nullable|string|max:255',
            'app_name'       => 'required|string|max:255',
            'site_tagline_en' => 'nullable|string|max:255',
            'site_tagline_ne' => 'nullable|string|max:255',
            'site_address_en' => 'required|string|max:255',
            'site_address_ne' => 'nullable|string|max:255',
            'website_url'    => 'nullable|url|max:255',
            'primary_color'         => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'secondary_color'       => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'dark_color'            => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'primary_light_color'   => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'body_bg_color'         => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'body_bg_gradient_end'  => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'header_gradient_end'   => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'footer_gradient_end'   => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'notice_bg_color'       => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'notice_accent_color'   => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'sidebar_gradient_end'  => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'body_font'      => 'required|in:DM Sans,Inter,Noto Sans Devanagari,Poppins',
            'heading_font'   => 'required|in:Playfair Display,Lora,Merriweather,Noto Sans Devanagari',
            'social_facebook'  => 'nullable|url|max:255',
            'social_instagram' => 'nullable|url|max:255',
            'social_tiktok'    => 'nullable|url|max:255',
            'social_twitter'   => 'nullable|url|max:255',
            'social_whatsapp'  => 'nullable|url|max:255',
            'social_youtube'   => 'nullable|url|max:255',
            'site_logo'      => 'nullable|file|mimes:png,jpg,jpeg,webp,svg|max:2048',
            'site_favicon'   => 'nullable|file|mimes:png,jpg,jpeg,webp,ico|max:512',
            'home_hero_image' => 'nullable|file|mimes:png,jpg,jpeg,webp|max:4096',
            'home_principal_image' => 'nullable|file|mimes:png,jpg,jpeg,webp|max:4096',
            'academics_elementary_image' => 'nullable|file|mimes:png,jpg,jpeg,webp|max:4096',
            'academics_primary_image' => 'nullable|file|mimes:png,jpg,jpeg,webp|max:4096',
            'academics_secondary_image' => 'nullable|file|mimes:png,jpg,jpeg,webp|max:4096',
        ];

        if ($request->user()?->isSuperAdmin()) {
            $rules += [
                'mail_mailer'       => 'required|in:smtp,log,array',
                'mail_host'         => 'nullable|required_if:mail_mailer,smtp|string|max:255',
                'mail_port'         => 'nullable|required_if:mail_mailer,smtp|integer|min:1|max:65535',
                'mail_username'     => 'nullable|string|max:255',
                'mail_password'     => 'nullable|string|max:255',
                'mail_encryption'   => 'nullable|in:tls,ssl',
                'mail_from_address' => 'required|email|max:255',
                'mail_from_name'    => 'required|string|max:255',
            ];
        }

        $request->validate($rules);

        $keys = [
            'admission_year',
            'school_phone',
            'school_email',
            'office_hours',
            'site_name_en',
            'site_name_ne',
            'app_name',
            'site_tagline_en',
            'site_tagline_ne',
            'site_address_en',
            'site_address_ne',
            'website_url',
            'social_facebook',
            'social_instagram',
            'social_tiktok',
            'social_twitter',
            'social_whatsapp',
            'social_youtube',
            'primary_color',
            'secondary_color',
            'dark_color',
            'primary_light_color',
            'body_bg_color',
            'body_bg_gradient_end',
            'header_gradient_end',
            'footer_gradient_end',
            'notice_bg_color',
            'notice_accent_color',
            'sidebar_gradient_end',
            'body_font',
            'heading_font',
        ];

        if ($request->user()?->isSuperAdmin()) {
            $keys = array_merge($keys, [
                'mail_mailer',
                'mail_host',
                'mail_port',
                'mail_username',
                'mail_encryption',
                'mail_from_address',
                'mail_from_name',
            ]);
        }

        foreach ($request->only($keys) as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        if ($request->user()?->isSuperAdmin() && $request->filled('mail_password')) {
            Setting::updateOrCreate(
                ['key' => 'mail_password'],
                ['value' => Crypt::encryptString($request->mail_password)]
            );
        }

        if ($request->hasFile('site_logo')) {
            $logo = $request->file('site_logo');
            $filename = 'logo-'.Str::slug(pathinfo($logo->getClientOriginalName(), PATHINFO_FILENAME)).'-'.time().'.'.$logo->getClientOriginalExtension();
            File::ensureDirectoryExists(public_path('uploads/site'));
            $logo->move(public_path('uploads/site'), $filename);

            Setting::updateOrCreate(
                ['key' => 'site_logo'],
                ['value' => 'uploads/site/'.$filename]
            );
        }

        if ($request->hasFile('site_favicon')) {
            $favicon = $request->file('site_favicon');
            $filename = 'favicon-'.time().'.'.$favicon->getClientOriginalExtension();
            File::ensureDirectoryExists(public_path('uploads/site'));
            $favicon->move(public_path('uploads/site'), $filename);

            Setting::updateOrCreate(
                ['key' => 'site_favicon'],
                ['value' => 'uploads/site/'.$filename]
            );
        }

        foreach ([
            'home_hero_image',
            'home_principal_image',
            'academics_elementary_image',
            'academics_primary_image',
            'academics_secondary_image',
        ] as $imageKey) {
            if (! $request->hasFile($imageKey)) {
                continue;
            }

            $image = $request->file($imageKey);
            $filename = Str::slug($imageKey).'-'.time().'.'.$image->getClientOriginalExtension();
            File::ensureDirectoryExists(public_path('uploads/site'));
            $image->move(public_path('uploads/site'), $filename);

            Setting::updateOrCreate(
                ['key' => $imageKey],
                ['value' => 'uploads/site/'.$filename]
            );
        }

        app(SiteSettings::class)->clearCache();

        return back()->with('success', 'Global settings updated successfully.');
    }

    public function testMail(Request $request)
    {
        abort_unless($request->user()?->isSuperAdmin(), 403);

        $request->validate([
            'test_email' => 'required|email|max:255',
        ]);

        try {
            Mail::raw(
                'This is a test email from Barchhain Secondary School mail settings.',
                function ($message) use ($request) {
                    $message->to($request->test_email)
                        ->subject('Barchhain Mail Settings Test');
                }
            );
        } catch (Throwable $exception) {
            return back()->with('mail_error', 'Test email failed: ' . $exception->getMessage());
        }

        $mailer = config('mail.default');
        $message = $mailer === 'smtp'
            ? 'Test email sent through SMTP. If it is not in the inbox, check spam and confirm the SMTP account is allowed to send as the From address.'
            : "Test email handled by the {$mailer} mailer. This does not deliver to an inbox.";

        return back()->with('mail_success', $message);
    }
}

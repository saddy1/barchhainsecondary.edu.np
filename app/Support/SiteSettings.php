<?php

namespace App\Support;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Throwable;

class SiteSettings
{
    private array $defaults = [
        'site_name_en' => 'Barchhain Secondary School',
        'site_name_ne' => 'बर्छैन माध्यमिक विद्यालय',
        'app_name' => 'Barchhain ERP',
        'site_tagline_en' => 'Fostering Excellence, Inspiring Futures',
        'site_tagline_ne' => 'उत्कृष्टता प्रवर्द्धन, भविष्य निर्माण',
        'site_address_en' => 'Barchhain, Doti, Sudurpashchim Province, Nepal',
        'site_address_ne' => 'बर्छैन, डोटी, सुदूरपश्चिम प्रदेश, नेपाल',
        'school_code' => '',
        'school_phone' => 'School Office',
        'school_email' => 'info@barchhainsecondary.edu.np',
        'office_hours' => 'Sun-Fri 9:00 AM - 5:00 PM',
        'office_hours_days' => 'Sunday – Friday',
        'office_hours_time' => '9:00 AM – 5:00 PM',
        'office_hours_closed' => 'Saturday & Public Holidays',
        'map_latitude'  => '29.2844',
        'map_longitude' => '81.0897',
        'map_zoom'      => '16',
        'website_url' => 'https://barchhainsecondary.edu.np',
        'site_logo'     => 'assets/image/logo.png',
        'site_favicon'  => 'assets/image/favicon.png',
        'home_hero_image' => 'assets/image/default-placeholder.jpg',
        'home_principal_image' => 'assets/image/default-placeholder.jpg',
        'academics_elementary_image' => 'assets/image/default-placeholder.jpg',
        'academics_primary_image' => 'assets/image/default-placeholder.jpg',
        'academics_secondary_image' => 'assets/image/default-placeholder.jpg',
        'social_facebook'  => '',
        'social_instagram' => '',
        'social_tiktok'    => '',
        'social_twitter'   => '',
        'social_whatsapp'  => '',
        'social_youtube'   => '',
        'school_alternate_name'       => 'Barchhain Secondary School Doti',
        'school_street'               => 'Barchhain',
        'school_locality'             => 'Barchhain',
        'school_region'               => 'Doti',
        'school_area_served'          => 'Doti',
        'school_founding_date_ad'     => '2005',
        'school_hours_schema'         => 'Su-Fr 09:00-17:00',
        'seo_default_title_en'        => 'Barchhain Secondary School | Government School in Doti, Nepal',
        'seo_default_title_ne'        => 'बर्छैन माध्यमिक विद्यालय | डोटीको सरकारी विद्यालय',
        'seo_default_description_en'  => 'Barchhain Secondary School is a government school in Doti, Nepal, focused on quality education, discipline, values, and community service.',
        'seo_default_description_ne'  => 'बर्छैन माध्यमिक विद्यालय डोटी, नेपालमा रहेको सरकारी विद्यालय हो। विद्यालय गुणस्तरीय शिक्षा, अनुशासन, संस्कार र समुदाय सेवामा केन्द्रित छ।',
        'seo_default_keywords_en'     => 'Barchhain Secondary School, school in Doti, government school Nepal, Doti education',
        'seo_default_keywords_ne'     => 'बर्छैन माध्यमिक विद्यालय, डोटी विद्यालय, सरकारी विद्यालय नेपाल, डोटी शिक्षा',
        'school_estd'          => '2017 B.S.',
        'principal_name'       => 'Indra Bahadur Bam',
        'principal_initials'   => 'IB',
        'principal_role_en'    => 'Principal, Barchhain Secondary School',
        'principal_role_ne'    => 'प्रधानाध्यापक, बर्छैन माध्यमिक विद्यालय',
        'principal_message_en' => 'Message From the Principal',
        'principal_message_ne' => 'प्रधानाध्यापकको सन्देश',
        'principal_quote_en'   => 'Education should help every child become capable, ethical, creative, and responsible. Our school is working to strengthen quality learning, practical skills, technology-friendly teaching, inclusive support, and community partnership.',
        'principal_quote_ne'   => 'शिक्षाले हरेक बालबालिकालाई सक्षम, नैतिक, सिर्जनशील र उत्तरदायी बनाउनुपर्छ। हाम्रो विद्यालय गुणस्तरीय सिकाइ, व्यवहारिक सीप, प्रविधिमैत्री शिक्षण, समावेशी सहयोग र समुदाय सहकार्यलाई बलियो बनाउन निरन्तर कार्यरत छ।',
        'primary_color' => '#1a5632',
        'secondary_color' => '#e2a024',
        'dark_color' => '#0b2415',
        'primary_light_color' => '#237042',
        'body_bg_color' => '#fdfbf7',
        'body_bg_gradient_end' => '#f4f5f0',
        'surface_color' => '#ffffff',
        'muted_surface_color' => '#F8FAFC',
        'border_color' => '#E5E7EB',
        'text_color' => '#111827',
        'muted_text_color' => '#64748B',
        'header_gradient_end' => '#0f3d22',
        'hero_gradient_end' => '#0f3d22',
        'cta_gradient_end' => '#0b2415',
        'footer_gradient_end' => '#0b2415',
        'notice_bg_color' => '',
        'notice_accent_color' => '',
        'sidebar_gradient_end' => '#050f09',
        'body_font' => 'DM Sans',
        'heading_font' => 'Playfair Display',
        'default_locale' => 'en',
    ];

    public function all(): array
    {
        return array_merge($this->defaults, $this->stored());
    }

    public function get(string $key, ?string $default = null): ?string
    {
        $settings = $this->all();

        return $settings[$key] ?? $default;
    }

    public function localized(string $baseKey, ?string $default = null): ?string
    {
        $locale = app()->getLocale() === 'ne' ? 'ne' : 'en';

        return $this->get($baseKey.'_'.$locale, $default);
    }

    public function logoUrl(): string
    {
        $path = $this->get('site_logo', 'assets/image/logo.png');

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        return asset($path);
    }

    public function faviconUrl(): string
    {
        $path = $this->get('site_favicon', 'assets/image/favicon.png');

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        return asset($path);
    }

    public function imageUrl(string $key, string $fallback): string
    {
        $path = $this->get($key, $fallback);

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        return asset($path);
    }

    public function fontFamily(string $key, string $fallback): string
    {
        return match ($this->get($key)) {
            'Inter' => "'Inter', sans-serif",
            'Noto Sans Devanagari' => "'Noto Sans Devanagari', sans-serif",
            'Poppins' => "'Poppins', sans-serif",
            'Lora' => "'Lora', Georgia, serif",
            'Merriweather' => "'Merriweather', Georgia, serif",
            'Playfair Display' => "'Playfair Display', Georgia, serif",
            default => $fallback,
        };
    }

    public function clearCache(): void
    {
        Cache::forget('site_settings');
    }

    private function stored(): array
    {
        return Cache::remember('site_settings', 300, function () {
            try {
                if (! Schema::hasTable('settings')) {
                    return [];
                }

                return Setting::pluck('value', 'key')
                    ->filter(fn ($value) => $value !== null && $value !== '')
                    ->toArray();
            } catch (Throwable) {
                return [];
            }
        });
    }
}

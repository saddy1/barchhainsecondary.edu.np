<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AboutController;
use App\Http\Controllers\AdmissionsController;
use App\Http\Controllers\AcademicsController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\EventsController;
use App\Http\Controllers\NoticesController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\Backend\DashboardController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Backend\AnnouncementController;
use App\Http\Controllers\Backend\MediaController;
use App\Http\Controllers\Backend\SeoController;
use App\Http\Controllers\Backend\AdmissionController;
use App\Http\Controllers\Backend\PopupNoticeController;
use App\Http\Controllers\Backend\TestimonialController;
use App\Http\Controllers\Backend\ContactMessageController;
use App\Http\Controllers\Backend\FacultyController;
use App\Http\Controllers\Backend\AdminUserController;
use App\Http\Controllers\VacancyController;
use App\Http\Controllers\Backend\VacancyController as BackendVacancyController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ApplicantLoginController;
use App\Http\Controllers\Auth\SocialiteController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Auth\AccountController;
use App\Http\Controllers\Backend\ModuleController;
use App\Http\Controllers\Work\WorkGroupController;
use App\Http\Controllers\Work\WorkTaskController;
/*

|--------------------------------------------------------------------------
| Web Routes — Barchhain Secondary School
|--------------------------------------------------------------------------
*/

Route::get('/language/{locale}', function (string $locale) {
    abort_unless(in_array($locale, ['en', 'ne'], true), 404);

    session(['locale' => $locale]);

    return back();
})->name('language.switch');

// Home
Route::get('/', [HomeController::class, 'index'])->name('home');

// About
Route::get('/about', [AboutController::class, 'index'])->name('about');

// Admissions
Route::get('/admissions', [App\Http\Controllers\AdmissionsController::class, 'index'])->name('admissions');
Route::post('/admissions', [App\Http\Controllers\AdmissionsController::class, 'storeAdmission'])->name('admissions.store');

// Academics
Route::get('/academics/elementary', [AcademicsController::class, 'elementary'])->name('academics.elementary');
Route::get('/academics/primary', [AcademicsController::class, 'primary'])->name('academics.primary');
Route::get('/academics/secondary', [AcademicsController::class, 'secondary'])->name('academics.secondary');

// Gallery
Route::get('/gallery', [GalleryController::class, 'index'])->name('gallery');

// Events
Route::get('/events', [EventsController::class, 'index'])->name('events');
Route::get('/events/{event:slug}', [EventsController::class, 'show'])->name('events.show');

// Notices
Route::get('/notices', [NoticesController::class, 'index'])->name('notices');
Route::get('/notices/{notice:slug}', [NoticesController::class, 'show'])->name('notices.show');

//news and events
Route::get('/news', [EventsController::class, 'news'])->name('news');
Route::get('/news/{news:slug}', [NoticesController::class, 'show'])->name('news.show');
// Contact
Route::get('/contact', [ContactController::class, 'index'])->name('contact');
Route::post('/contact', [ContactController::class, 'storeContact'])->name('contact.submit');

// Vacancies (public listing, apply requires verified auth)
Route::get('/vacancies', [VacancyController::class, 'index'])->name('vacancies');
Route::get('/vacancies/{vacancy}/apply', [VacancyController::class, 'createApplication'])
    ->middleware(['auth', 'verified'])
    ->name('vacancy.apply.create');
Route::post('/vacancies/{vacancy}/apply', [VacancyController::class, 'apply'])
    ->middleware(['auth', 'verified'])
    ->name('vacancy.apply');

// Legal pages
Route::view('/privacy-policy', 'pages.privacy')->name('privacy');
Route::view('/terms-of-service', 'pages.terms')->name('terms');
Route::get('/sitemap.xml', [HomeController::class, 'sitemap'])->name('sitemap');

//faculty and staff

Route::get('/Frontend/faculty', [App\Http\Controllers\FacultyController::class, 'index'])->name('frontend.faculty');

Route::get('/staff', [HomeController::class, 'staff'])->name('staff');

// Admin — routes to be added later when admin dashboard is built
// Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {
//     Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
//     // ... more admin routes
// });




// ── Admin login (admin-only users) ───────────────────────────────────────────
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->middleware('guest');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// ── Applicant registration & login ───────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
    Route::get('/applicant/login', [ApplicantLoginController::class, 'showLoginForm'])->name('applicant.login');
    Route::post('/applicant/login', [ApplicantLoginController::class, 'login'])->name('applicant.login.submit');
    Route::get('/forgot-password', [PasswordResetController::class, 'request'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetController::class, 'email'])->name('password.email');
    Route::get('/reset-password/{token}', [PasswordResetController::class, 'reset'])->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'update'])->name('password.update');
});
Route::post('/applicant/logout', [ApplicantLoginController::class, 'logout'])
    ->name('applicant.logout')
    ->middleware('auth');
Route::middleware('auth')->group(function () {
    Route::get('/account/password', [AccountController::class, 'editPassword'])->name('account.password.edit');
    Route::put('/account/password', [AccountController::class, 'updatePassword'])->name('account.password.update');
    Route::get('/account/applications', [VacancyController::class, 'myApplications'])->name('account.applications.index');
    Route::get('/account/applications/{application}', [VacancyController::class, 'showApplication'])->name('account.applications.show');
});

Route::prefix('admin/work-tasks')
    ->name('admin.work-tasks.')
    ->middleware(['auth'])
    ->group(function () {
        Route::get('/', [WorkTaskController::class, 'index'])->middleware('permission:work-tasks.view')->name('index');
        Route::post('/', [WorkTaskController::class, 'store'])->middleware('permission:work-tasks.create')->name('store');
        Route::get('/{task}', [WorkTaskController::class, 'show'])->middleware('permission:work-tasks.view')->name('show');
        Route::post('/{task}/submit', [WorkTaskController::class, 'submit'])->middleware('permission:work-tasks.submit')->name('submit');
        Route::post('/{task}/submissions/{submission}/review', [WorkTaskController::class, 'review'])->middleware('permission:work-tasks.review')->name('review');

        Route::post('/groups', [WorkGroupController::class, 'store'])->middleware('permission:work-groups.manage')->name('groups.store');
        Route::patch('/groups/{group}', [WorkGroupController::class, 'update'])->middleware('permission:work-groups.manage')->name('groups.update');
        Route::delete('/groups/{group}', [WorkGroupController::class, 'destroy'])->middleware('permission:work-groups.manage')->name('groups.destroy');
    });

// ── Email verification ────────────────────────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::get('/email/verify', function () {
        return view('auth.verify-email');
    })->name('verification.notice');

    Route::get('/email/verify/{id}/{hash}', function (\Illuminate\Foundation\Auth\EmailVerificationRequest $request) {
        $request->fulfill();
        return redirect()->route('vacancies')->with('status', 'Email verified! You can now apply for vacancies.');
    })->middleware('signed')->name('verification.verify');

    Route::post('/email/verification-notification', function (\Illuminate\Http\Request $request) {
        try {
            $request->user()->sendEmailVerificationNotification();
        } catch (\Throwable $exception) {
            return back()->with('mail_error', 'Verification email could not be sent: ' . $exception->getMessage());
        }

        return back()->with('status', 'verification-link-sent');
    })->middleware('throttle:6,1')->name('verification.send');
});

// ── Google OAuth ──────────────────────────────────────────────────────────────
Route::get('/auth/google', [SocialiteController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [SocialiteController::class, 'handleGoogleCallback'])->name('auth.google.callback');
// backend dashboard route
Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {

    // Dashboard Route: matches /admin/dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->middleware('permission:dashboard.admin,dashboard.view,dashboard.financial')->name('admin.dashboard');

    // User management — controlled by permissions
    Route::get('/users', [AdminUserController::class, 'index'])->middleware('permission:users.view')->name('admin.users.index');
    Route::post('/users', [AdminUserController::class, 'store'])->middleware('permission:users.create')->name('admin.users.store');
    Route::patch('/users/{user}/role', [AdminUserController::class, 'updateRole'])->middleware('permission:users.edit')->name('admin.users.update-role');
    Route::patch('/users/{user}/password', [AdminUserController::class, 'resetPassword'])->middleware('permission:users.edit')->name('admin.users.reset-password');
    Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->middleware('permission:users.delete')->name('admin.users.destroy');

    Route::middleware('super_admin')->group(function () {
        Route::get('/users/{user}/permissions', [AdminUserController::class, 'permissions'])->name('admin.users.permissions');
        Route::patch('/users/{user}/permissions', [AdminUserController::class, 'updatePermissions'])->name('admin.users.permissions.update');
    });

    // Module access — super-admin only
    Route::middleware('super_admin')->group(function () {
        Route::get('/modules', [ModuleController::class, 'index'])->name('admin.modules.index');
        Route::post('/modules/{key}/toggle', [ModuleController::class, 'toggle'])->name('admin.modules.toggle');
    });

    Route::resource('announcements', AnnouncementController::class, ['as' => 'admin'])
        ->only(['index', 'show'])
        ->middleware('permission:announcements.view');
    Route::resource('announcements', AnnouncementController::class, ['as' => 'admin'])
        ->only(['create', 'store'])
        ->middleware('permission:announcements.create');
    Route::resource('announcements', AnnouncementController::class, ['as' => 'admin'])
        ->only(['edit', 'update'])
        ->middleware('permission:announcements.edit');
    Route::resource('announcements', AnnouncementController::class, ['as' => 'admin'])
        ->only(['destroy'])
        ->middleware('permission:announcements.delete');
    // Route to force inline PDF viewing
    Route::get('/announcements/{announcement}/view-file', [App\Http\Controllers\Backend\AnnouncementController::class, 'viewFile'])->middleware('permission:announcements.view')->name('admin.announcements.view_file');;

    Route::prefix('admin')->name('admin.')->group(function () {
        // ... your other admin routes ...

        Route::resource('faculty', FacultyController::class)
            ->only(['index', 'show'])
            ->middleware('permission:faculty.view');
        Route::resource('faculty', FacultyController::class)
            ->only(['create', 'store'])
            ->middleware('permission:faculty.create');
        Route::resource('faculty', FacultyController::class)
            ->only(['edit', 'update'])
            ->middleware('permission:faculty.edit');
        Route::resource('faculty', FacultyController::class)
            ->only(['destroy'])
            ->middleware('permission:faculty.delete');

        Route::get('/media', [MediaController::class, 'index'])->middleware('permission:media.view')->name('media.index');
        Route::post('/media', [MediaController::class, 'store'])->middleware('permission:media.create')->name('media.store');
        Route::get('/seo', [SeoController::class, 'index'])->middleware('permission:settings.view')->name('seo.index');
        Route::post('/seo/generate', [SeoController::class, 'generate'])->middleware('permission:settings.edit')->name('seo.generate');
        Route::post('/seo/store', [SeoController::class, 'store'])->middleware('permission:settings.edit')->name('seo.store');



        Route::middleware('module.enabled:admissions')->group(function () {
            Route::get('/admissions', [AdmissionController::class, 'index'])->middleware('permission:students.admission')->name('admissions.index');
            Route::get('/admissions/{admission}', [AdmissionController::class, 'show'])->middleware('permission:students.admission')->name('admissions.show');
            Route::put('/admissions/{admission}', [AdmissionController::class, 'update'])->middleware('permission:students.edit')->name('admissions.update');
            Route::delete('/admissions/{admission}', [AdmissionController::class, 'destroy'])->middleware('permission:students.delete')->name('admissions.destroy');
        });


        // Popup Management
    Route::get('/popups', [PopupNoticeController::class, 'index'])->middleware('permission:popups.view')->name('popups.index');
    Route::post('/popups', [PopupNoticeController::class, 'store'])->middleware('permission:popups.create')->name('popups.store');
    Route::get('/popups/{popup}/edit', [PopupNoticeController::class, 'edit'])->middleware('permission:popups.edit')->name('popups.edit'); // NEW
    Route::put('/popups/{popup}', [PopupNoticeController::class, 'update'])->middleware('permission:popups.edit')->name('popups.update'); // NEW
    Route::delete('/popups/{popup}', [PopupNoticeController::class, 'destroy'])->middleware('permission:popups.delete')->name('popups.destroy');
    Route::patch('/popups/{popup}/toggle', [PopupNoticeController::class, 'toggle'])->middleware('permission:popups.edit')->name('popups.toggle');



// Testimonial Management
    Route::get('/testimonials', [TestimonialController::class, 'index'])->middleware('permission:testimonials.view')->name('testimonials.index');
    Route::post('/testimonials', [TestimonialController::class, 'store'])->middleware('permission:testimonials.create')->name('testimonials.store');
    Route::delete('/testimonials/{testimonial}', [TestimonialController::class, 'destroy'])->middleware('permission:testimonials.delete')->name('testimonials.destroy');
    Route::patch('/testimonials/{testimonial}/toggle', [TestimonialController::class, 'toggle'])->middleware('permission:testimonials.edit')->name('testimonials.toggle');



    // Global Settings — controlled by permissions
    Route::get('/settings', [\App\Http\Controllers\Backend\SettingController::class, 'index'])->middleware('permission:settings.view')->name('settings.index');
    Route::put('/settings', [\App\Http\Controllers\Backend\SettingController::class, 'update'])->middleware('permission:settings.edit')->name('settings.update');
    Route::post('/settings/test-mail', [\App\Http\Controllers\Backend\SettingController::class, 'testMail'])->middleware('permission:settings.edit')->name('settings.test-mail');

    Route::get('/principal', [\App\Http\Controllers\Backend\PrincipalController::class, 'index'])->middleware('permission:settings.view')->name('principal.index');
    Route::put('/principal', [\App\Http\Controllers\Backend\PrincipalController::class, 'update'])->middleware('permission:settings.edit')->name('principal.update');


Route::get('/contacts', [ContactMessageController::class, 'index'])->middleware('permission:contacts.view')->name('contacts.index');
Route::patch('/contacts/{message}/read', [ContactMessageController::class, 'markAsRead'])->middleware('permission:contacts.edit')->name('contacts.read');
Route::delete('/contacts/{message}', [ContactMessageController::class, 'destroy'])->middleware('permission:contacts.delete')->name('contacts.destroy');

        // Vacancy Management
        Route::middleware('module.enabled:vacancy')->group(function () {
            Route::get('/vacancies', [BackendVacancyController::class, 'index'])->middleware('permission:vacancies.view')->name('vacancies.index');
            Route::get('/vacancies/create', [BackendVacancyController::class, 'create'])->middleware('permission:vacancies.create')->name('vacancies.create');
            Route::post('/vacancies', [BackendVacancyController::class, 'store'])->middleware('permission:vacancies.create')->name('vacancies.store');
            Route::get('/vacancies/{vacancy}/edit', [BackendVacancyController::class, 'edit'])->middleware('permission:vacancies.edit')->name('vacancies.edit');
            Route::put('/vacancies/{vacancy}', [BackendVacancyController::class, 'update'])->middleware('permission:vacancies.edit')->name('vacancies.update');
            Route::delete('/vacancies/{vacancy}', [BackendVacancyController::class, 'destroy'])->middleware('permission:vacancies.delete')->name('vacancies.destroy');
            Route::patch('/vacancies/{vacancy}/toggle', [BackendVacancyController::class, 'toggle'])->middleware('permission:vacancies.edit')->name('vacancies.toggle');
            Route::get('/vacancies/{vacancy}/applications', [BackendVacancyController::class, 'applications'])->middleware('permission:vacancies.applications')->name('vacancies.applications');
            Route::get('/vacancy-applications/{application}', [BackendVacancyController::class, 'showApplication'])->middleware('permission:vacancies.applications')->name('vacancy-applications.show');
            Route::put('/vacancy-applications/{application}', [BackendVacancyController::class, 'updateApplication'])->middleware('permission:vacancies.applications')->name('vacancy-applications.update');
            Route::delete('/vacancy-applications/{application}', [BackendVacancyController::class, 'destroyApplication'])->middleware('permission:vacancies.applications')->name('vacancy-applications.destroy');
        });



    });
    Route::get('/gallery', [MediaController::class, 'gallery'])->middleware('permission:media.view')->name('admin.gallery.index');
    Route::post('/gallery/upload', [MediaController::class, 'uploadMultiple'])->middleware('permission:media.create')->name('admin.gallery.upload');
    Route::patch('/media/{media}', [MediaController::class, 'update'])->middleware('permission:media.edit')->name('admin.media.update');
    Route::delete('/media/{media}', [MediaController::class, 'destroy'])->middleware('permission:media.delete')->name('admin.media.destroy');



    // Future Routes will go here:
    // Route::resource('seo', SeoController::class);
    // Route::resource('notices', NoticeController::class);

});

require __DIR__.'/modules/card.php';
require __DIR__.'/modules/hr.php';
require __DIR__.'/modules/hajiri.php';
require __DIR__.'/modules/learning.php';

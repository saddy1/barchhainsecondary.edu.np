<?php

use App\Http\Controllers\Hr\MemberController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin/hr')
    ->name('admin.hr.')
    ->middleware(['auth', 'admin', 'module.enabled:hr'])
    ->group(function () {
        Route::get('/', fn () => redirect()->route('admin.hr.members.index'))->name('dashboard');

        Route::get('/members', [MemberController::class, 'index'])->middleware('permission:hr.members.view')->name('members.index');
        Route::get('/members/import', [MemberController::class, 'importForm'])->middleware('permission:hr.members.create')->name('members.import');
        Route::post('/members/import', [MemberController::class, 'importCsv'])->middleware('permission:hr.members.create')->name('members.import.store');
        Route::get('/members/template', [MemberController::class, 'template'])->middleware('permission:hr.members.create')->name('members.template');
        Route::get('/members/create', [MemberController::class, 'create'])->middleware('permission:hr.members.create')->name('members.create');
        Route::post('/members', [MemberController::class, 'store'])->middleware('permission:hr.members.create')->name('members.store');
        Route::get('/members/{member}/edit', [MemberController::class, 'edit'])->middleware('permission:hr.members.edit')->name('members.edit');
        Route::put('/members/{member}', [MemberController::class, 'update'])->middleware('permission:hr.members.edit')->name('members.update');
        Route::delete('/members/{member}', [MemberController::class, 'destroy'])->middleware('permission:hr.members.delete')->name('members.destroy');
    });

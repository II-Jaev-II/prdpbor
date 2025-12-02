<?php

use App\Livewire\ApprovedReports;
use App\Livewire\BackToOfficeReport;
use App\Livewire\PendingReports;
use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use App\Livewire\Settings\TwoFactor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('dashboard', function (Request $request) {
    $user = $request->user();
    
    return match($user->role) {
        \App\Enum\UserRole::Admin => redirect()->route('admin.dashboard'),
        \App\Enum\UserRole::Superior => redirect()->route('superior.dashboard'),
        \App\Enum\UserRole::User => redirect()->route('user.dashboard'),
        default => redirect()->route('home'),
    };
})->middleware(['auth'])->name('dashboard');

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::view('/dashboard', 'dashboards.admin')->name('dashboard');
});

Route::middleware(['auth', 'role:superior'])->prefix('superior')->name('superior.')->group(function () {
    Route::view('/dashboard', 'dashboards.superior')->name('dashboard');
});

Route::middleware(['auth', 'role:user'])->prefix('user')->name('user.')->group(function () {
    Route::view('/dashboard', 'dashboards.user')->name('dashboard');
});

// Routes accessible by both users and superiors
Route::middleware(['auth', 'role:user,superior'])->group(function () {
    Route::get('/back-to-office-report', BackToOfficeReport::class)->name('back-to-office-report');
    Route::get('/pending-reports', PendingReports::class)->name('pending-reports');
    Route::get('/approved-reports', ApprovedReports::class)->name('approved-reports');
});

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Route::get('settings/profile', Profile::class)->name('profile.edit');
    Route::get('settings/password', Password::class)->name('user-password.edit');
    Route::get('settings/appearance', Appearance::class)->name('appearance.edit');
    Route::get('settings/two-factor', TwoFactor::class)->middleware(['password.confirm'])->name('two-factor.show');
});

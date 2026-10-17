<?php

use App\Http\Controllers\LandingPageController;
use App\Http\Middleware\ApplyLandingContent;
use App\Livewire\RegistrationForm;
use Illuminate\Support\Facades\Route;

Route::middleware(ApplyLandingContent::class)->group(function (): void {
    Route::get('/', LandingPageController::class)->name('home');

    Route::livewire('/daftar', RegistrationForm::class)->name('registration');
});

Route::get('/sitemap.xml', function () {
    return response()
        ->view('sitemap', ['urls' => [route('home'), route('registration')]])
        ->header('Content-Type', 'application/xml');
})->name('sitemap');

// Redirect /login to Filament login to avoid RouteNotFoundException
Route::get('/login', function () {
    return redirect()->route('filament.admin.auth.login');
})->name('login');

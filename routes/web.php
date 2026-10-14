<?php

use App\Http\Controllers\LandingPageController;
use App\Livewire\RegistrationForm;
use Illuminate\Support\Facades\Route;

Route::get('/', LandingPageController::class)->name('home');

Route::livewire('/daftar', RegistrationForm::class)->name('registration');

// Redirect /login to Filament login to avoid RouteNotFoundException
Route::get('/login', function () {
    return redirect()->route('filament.admin.auth.login');
})->name('login');

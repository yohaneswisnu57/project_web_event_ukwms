<?php

use App\Livewire\RegistrationForm;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/join/{code}', RegistrationForm::class)->name('registration.form');

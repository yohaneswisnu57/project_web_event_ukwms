<?php

use App\Livewire\RegistrationForm;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/join/{code}', RegistrationForm::class)->name('registration.form');

Route::middleware(['auth'])->group(function () {
    Route::get('/admin/invitations/template', function () {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="template_undangan.csv"',
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['name']);
            fputcsv($file, ['Contoh Nama Perusahaan']);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    })->name('invitations.template');

    // QR Check-in Route
    Route::get('/admin/check-in/{registration}', function (\App\Models\Registration $registration) {
        if ($registration->checked_in_at) {
            return "Tamu ini sudah check-in pada: " . $registration->checked_in_at->format('d M Y H:i');
        }

        $registration->update(['checked_in_at' => now()]);

        return "Check-in Berhasil! Selamat datang, " . $registration->invitation->name . " (Total: " . $registration->attendance_count . " orang)";
    })->name('admin.check-in');
});

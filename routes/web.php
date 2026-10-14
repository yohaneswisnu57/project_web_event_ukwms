<?php

use App\Livewire\RegistrationForm;
use App\Mail\RegistrationConfirmedMail;
use App\Models\Invitation;
use App\Models\Registration;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/join/{code}', RegistrationForm::class)->name('registration.form');

// Redirect /login to Filament login to avoid RouteNotFoundException
Route::get('/login', function () {
    return redirect()->route('filament.admin.auth.login');
})->name('login');

// Email Preview Route (For Testing)
Route::get('/preview-email', function () {
    abort_unless(app()->environment('local'), 404);

    // Gunakan transaksi agar data dummy tidak benar-benar tersimpan ke database permanen
    DB::beginTransaction();

    try {
        $invitation = Invitation::create([
            'name' => 'Bpk. Yohanes (Preview)',
            'unique_code' => 'PREVIEW-'.rand(1000, 9999),
            'email' => 'yohanes-wp@ukwms.ac.id',
            'is_used' => true,
        ]);

        $registration = Registration::create([
            'invitation_id' => $invitation->id,
            'uuid' => (string) Str::uuid(),
            'attendance_count' => 1,
            'email' => 'yohanes-wp@ukwms.ac.id',
            'phone' => '081234567890',
            'position' => 'Preview Position',
        ]);

        $mailable = new RegistrationConfirmedMail($registration);
        $html = $mailable->render();

        DB::rollBack(); // Kembalikan database ke kondisi semula

        return $html;
    } catch (Exception $e) {
        DB::rollBack();

        return 'Error: '.$e->getMessage();
    }
})->name('preview-email');

Route::middleware(['auth'])->group(function () {
    // Documentation & Progress Report
    Route::get('/user-manual', function () {
        return view('user-manual');
    })->name('user-manual');

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
});

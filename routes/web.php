<?php

use App\Livewire\RegistrationForm;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/join/{code}', RegistrationForm::class)->name('registration.form');

// Redirect /login to Filament login to avoid RouteNotFoundException
Route::get('/login', function () {
    return redirect()->route('filament.admin.auth.login');
})->name('login');

// Documentation & Progress Report
Route::get('/user-manual', function () {
    return view('user-manual');
})->name('user-manual');

// Email Preview Route (For Testing)
Route::get('/preview-email', function () {
    // Gunakan transaksi agar data dummy tidak benar-benar tersimpan ke database permanen
    DB::beginTransaction();
    
    try {
        $invitation = \App\Models\Invitation::create([
            'name' => 'Bpk. Yohanes (Preview)',
            'unique_code' => 'PREVIEW-' . rand(1000, 9999),
            'email' => 'yohanes-wp@ukwms.ac.id',
            'is_used' => true,
        ]);

        $registration = \App\Models\Registration::create([
            'invitation_id' => $invitation->id,
            'uuid' => (string) \Illuminate\Support\Str::uuid(),
            'attendance_count' => 1,
            'email' => 'yohanes-wp@ukwms.ac.id',
            'phone' => '081234567890',
            'position' => 'Preview Position'
        ]);

        $mailable = new \App\Mail\RegistrationConfirmedMail($registration);
        $html = $mailable->render();
        
        DB::rollBack(); // Kembalikan database ke kondisi semula
        
        return $html;
    } catch (\Exception $e) {
        DB::rollBack();
        return 'Error: ' . $e->getMessage();
    }
})->name('preview-email');

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
        $name = $registration->attendees->first()->name ?? $registration->invitation->name;
        
        if ($registration->checked_in_at) {
            return "
                <div style='font-family: sans-serif; text-align: center; padding: 50px;'>
                    <div style='font-size: 80px; color: #f59e0b;'>⚠️</div>
                    <h1 style='color: #1e293b;'>Sudah Check-in</h1>
                    <p style='color: #64748b; font-size: 1.2rem;'>Tamu <b>{$name}</b> sudah melakukan check-in pada:<br>" . $registration->checked_in_at->format('d M Y H:i') . " WIB</p>
                    <button onclick='window.close()' style='margin-top: 20px; padding: 12px 24px; background: #FAC43A; border: none; border-radius: 12px; font-weight: bold; cursor: pointer;'>Tutup Halaman</button>
                </div>
            ";
        }

        $registration->update(['checked_in_at' => now()]);

        return "
            <div style='font-family: sans-serif; text-align: center; padding: 50px;'>
                <div style='font-size: 80px; color: #22c55e;'>✅</div>
                <h1 style='color: #1e293b;'>Check-in Berhasil!</h1>
                <p style='color: #64748b; font-size: 1.2rem;'>Selamat datang, <b>{$name}</b>.<br>Silakan masuk ke lokasi acara.</p>
                <button onclick='window.close()' style='margin-top: 20px; padding: 12px 24px; background: #FAC43A; border: none; border-radius: 12px; font-weight: bold; cursor: pointer;'>Tutup Halaman</button>
            </div>
        ";
    })->name('admin.check-in');
});

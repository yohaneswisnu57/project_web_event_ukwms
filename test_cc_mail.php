<?php

use App\Models\Registration;
use App\Models\Invitation;
use App\Mail\RegistrationConfirmedMail;
use Illuminate\Support\Facades\Mail;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// 1. Create dummy invitation if not exists
$invitation = Invitation::firstOrCreate(
    ['name' => 'DUMMY TEST CC'],
    [
        'uuid' => (string) str()->uuid(),
        'link_token' => (string) str()->random(32),
        'max_attendees' => 1
    ]
);

// 2. Create dummy registration
$registration = Registration::create([
    'uuid' => (string) str()->uuid(),
    'invitation_id' => $invitation->id,
    'email' => 'yohanes-wp@ukwms.ac.id', 
    'phone' => '08123456789',
    'position' => 'Tester CC',
    'attendance_count' => 1,
]);

$registration->attendees()->create([
    'name' => 'Tester CC User',
]);

echo "Created Dummy Registration ID: {$registration->id}\n";

// 3. Trigger validation & Mail (Simulate RegistrationsTable action)
echo "Triggering Mail with CC...\n";
$record = $registration;
$record->update(['validated_at' => now()]);

Mail::to($record->email)
    ->cc('adminlpks@ukwms.ac.id')
    ->send(new RegistrationConfirmedMail($record));

echo "Mail dispatched to queue.\n";
echo "Check Horizon or failed_jobs table to verify.\n";

<?php
require dirname(__DIR__) . '/vendor/autoload.php';
$app = require dirname(__DIR__) . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Registration;
use App\Mail\RegistrationConfirmedMail;
use Illuminate\Support\Facades\Mail;

$emails = ["michael-c@ukwms.ac.id", "yohanes-wp@ukwms.ac.id"];
foreach ($emails as $email) {
    $registration = Registration::whereHas("invitation", function($q) use ($email) {
        $q->where("email", $email);
    })->first();

    if ($registration) {
        Mail::to($email)->send(new RegistrationConfirmedMail($registration));
        echo "Sent to $email\n";
    } else {
        echo "Registration not found for $email\n";
    }
}

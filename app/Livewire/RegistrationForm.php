<?php

namespace App\Livewire;

use App\Models\Invitation;
use App\Models\Registration;
use App\Models\Setting;
use Filament\Notifications\Notification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Locked;
use Livewire\Component;

class RegistrationForm extends Component
{
    #[Locked]
    public Invitation $invitation;

    // Form fields
    public int $attendance_count = 1;

    public array $attendees = [
        [
            'name' => '',
            'email' => '',
            'phone' => '',
            'position' => '',
        ],
    ];

    // State
    public bool $submitted = false;

    public bool $isClosed = false;

    public ?Registration $latestRegistration = null;

    public function mount(string $code)
    {
        $this->checkIfClosed();

        $this->invitation = Invitation::where('unique_code', $code)->firstOrFail();

        // If already registered, show the success state and fetch registration
        if ($this->invitation->is_used) {
            $this->submitted = true;
            $this->latestRegistration = Registration::where('invitation_id', $this->invitation->id)->first();
        }
    }

    protected function checkIfClosed(): void
    {
        $setting = Setting::where('key', 'registration_closing_at')->value('value');
        $closingDate = $setting ? Carbon::parse($setting) : Carbon::create(2026, 5, 13, 16, 0, 0, 'Asia/Jakarta');
        $this->isClosed = now()->greaterThan($closingDate);
    }

    protected function initializeAttendees()
    {
        // No longer needed as we only support 1 attendee
    }

    protected function rules(): array
    {
        return [
            'attendance_count' => 'required|integer|min:1|max:10',
            'attendees.*.name' => 'required|string|min:3|max:255',
            'attendees.*.email' => 'required|email|max:255',
            'attendees.*.phone' => 'required|numeric|digits_between:10,15',
            'attendees.*.position' => 'required|string|max:255',
        ];
    }

    protected function messages(): array
    {
        return [
            'attendance_count.required' => 'Jumlah hadir wajib diisi.',
            'attendees.*.name.required' => 'Nama tamu wajib diisi.',
            'attendees.*.name.min' => 'Nama minimal 3 karakter.',
            'attendees.*.email.required' => 'Email wajib diisi.',
            'attendees.*.email.email' => 'Format email tidak valid.',
            'attendees.*.phone.required' => 'Nomor HP wajib diisi.',
            'attendees.*.phone.numeric' => 'Nomor HP harus berupa angka.',
            'attendees.*.phone.digits_between' => 'Nomor HP harus antara 10 - 15 digit.',
            'attendees.*.position.required' => 'Jabatan wajib diisi.',
            'attendees.*.position.regex' => 'Jabatan hanya boleh berisi huruf dan spasi.',
        ];
    }

    public function submit()
    {
        $this->checkIfClosed();

        if ($this->isClosed) {
            Notification::make()
                ->title('Registrasi Ditutup')
                ->body('Mohon maaf, waktu registrasi telah berakhir.')
                ->danger()
                ->send();

            return;
        }

        // Security: Rate Limiting by IP (Max 3 submissions per minute)
        $throttleKey = 'register-attempt:'.request()->ip();
        if (RateLimiter::tooManyAttempts($throttleKey, 3)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            throw ValidationException::withMessages([
                'registration_limit' => "Keamanan: Silakan coba lagi dalam {$seconds} detik.",
            ]);
        }

        $this->validate();

        // Record the attempt
        RateLimiter::hit($throttleKey, 60);

        // Create the registration
        $this->latestRegistration = Registration::create([
            'invitation_id' => $this->invitation->id,
            'attendance_count' => $this->attendance_count,
            'email' => $this->attendees[0]['email'] ?? '',
            'phone' => $this->attendees[0]['phone'] ?? '',
            'position' => $this->attendees[0]['position'] ?? '',
        ]);

        // Create attendees
        foreach ($this->attendees as $attendeeData) {
            $this->latestRegistration->attendees()->create($attendeeData);
        }

        // Mark invitation as used
        $this->invitation->update(['is_used' => true]);

        $this->submitted = true;
    }

    public function render()
    {
        return view('livewire.registration-form')
            ->layout('layouts.guest');
    }
}

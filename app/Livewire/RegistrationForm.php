<?php

namespace App\Livewire;

use App\Models\Invitation;
use App\Models\Registration;
use App\Models\RegistrationAttendee;
use Livewire\Attributes\Locked;
use Livewire\Component;

class RegistrationForm extends Component
{
    #[Locked]
    public Invitation $invitation;

    // Form fields
    public int $attendance_count = 1;
    public array $attendees = [];

    // State
    public bool $submitted = false;
    public ?Registration $latestRegistration = null;

    public function mount(string $code)
    {
        $this->invitation = Invitation::where('unique_code', $code)->firstOrFail();

        // If already registered, show the success state and fetch registration
        if ($this->invitation->is_used) {
            $this->submitted = true;
            $this->latestRegistration = Registration::where('invitation_id', $this->invitation->id)->first();
        }

        $this->initializeAttendees();
    }

    public function updatedAttendanceCount($value)
    {
        $this->initializeAttendees();
    }

    protected function initializeAttendees()
    {
        $count = max(1, min(10, $this->attendance_count)); // Limit to 10 for UI
        
        // If decreasing, keep the existing data
        if (count($this->attendees) > $count) {
            $this->attendees = array_slice($this->attendees, 0, $count);
        } 
        // If increasing, add new empty entries
        elseif (count($this->attendees) < $count) {
            for ($i = count($this->attendees); $i < $count; $i++) {
                $this->attendees[] = [
                    'name' => '',
                    'email' => '',
                    'phone' => '',
                    'position' => '',
                ];
            }
        }
    }

    protected function rules(): array
    {
        return [
            'attendance_count' => 'required|integer|min:1|max:10',
            'attendees.*.name' => 'required|string|min:3|max:255',
            'attendees.*.email' => 'required|email|max:255',
            'attendees.*.phone' => 'required|numeric|digits_between:10,15',
            'attendees.*.position' => 'required|regex:/^[a-zA-Z\s]+$/u|max:255',
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
        // Security: Rate Limiting by IP (Max 3 submissions per minute)
        $throttleKey = 'register-attempt:' . request()->ip();
        if (\Illuminate\Support\Facades\RateLimiter::tooManyAttempts($throttleKey, 3)) {
            $seconds = \Illuminate\Support\Facades\RateLimiter::availableIn($throttleKey);
            
            \Filament\Notifications\Notification::make()
                ->title('Keamanan: Terlalu Banyak Upaya')
                ->body("Silakan coba lagi dalam {$seconds} detik untuk alasan keamanan.")
                ->danger()
                ->send();
                
            return;
        }

        $this->validate();

        // Record the attempt
        \Illuminate\Support\Facades\RateLimiter::hit($throttleKey, 60);

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

        // Send confirmation email to the primary attendee
        if (!empty($this->attendees[0]['email'])) {
            \Illuminate\Support\Facades\Mail::to($this->attendees[0]['email'])
                ->send(new \App\Mail\RegistrationConfirmedMail($this->latestRegistration));
        }

        $this->submitted = true;
    }

    public function render()
    {
        return view('livewire.registration-form')
            ->layout('layouts.guest');
    }
}

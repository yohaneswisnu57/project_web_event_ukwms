<?php

namespace App\Livewire;

use App\Models\Invitation;
use App\Models\Registration;
use App\Models\RegistrationAttendee;
use Livewire\Component;

class RegistrationForm extends Component
{
    public Invitation $invitation;

    // Form fields
    public int $attendance_count = 1;
    public array $attendees = [];

    // State
    public bool $submitted = false;

    public function mount(string $code)
    {
        $this->invitation = Invitation::where('unique_code', $code)->firstOrFail();

        // If already registered, show the success state
        if ($this->invitation->is_used) {
            $this->submitted = true;
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
            'attendees.*.name' => 'required|string|max:255',
            'attendees.*.email' => 'nullable|email|max:255',
            'attendees.*.phone' => 'nullable|string|max:20',
            'attendees.*.position' => 'required|string|max:255',
        ];
    }

    protected function messages(): array
    {
        return [
            'attendance_count.required' => 'Jumlah hadir wajib diisi.',
            'attendees.*.name.required' => 'Nama tamu wajib diisi.',
            'attendees.*.position.required' => 'Jabatan wajib diisi.',
            'attendees.*.email.email' => 'Format email tidak valid.',
        ];
    }

    public function submit()
    {
        $this->validate();

        // Create the registration
        $registration = Registration::create([
            'invitation_id' => $this->invitation->id,
            'attendance_count' => $this->attendance_count,
            'email' => $this->attendees[0]['email'] ?? '',
            'phone' => $this->attendees[0]['phone'] ?? '',
            'position' => $this->attendees[0]['position'] ?? '',
        ]);

        // Create attendees
        foreach ($this->attendees as $attendeeData) {
            $registration->attendees()->create($attendeeData);
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

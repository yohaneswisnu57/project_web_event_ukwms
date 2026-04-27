<?php

namespace App\Livewire;

use App\Models\Invitation;
use App\Models\Registration;
use Livewire\Component;

class RegistrationForm extends Component
{
    public Invitation $invitation;

    // Form fields
    public int $attendance_count = 1;
    public string $email = '';
    public string $phone = '';
    public string $position = '';

    // State
    public bool $submitted = false;

    protected function rules(): array
    {
        return [
            'attendance_count' => 'required|integer|min:1|max:50',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'position' => 'required|string|max:255',
        ];
    }

    protected function messages(): array
    {
        return [
            'attendance_count.required' => 'Jumlah hadir wajib diisi.',
            'attendance_count.min' => 'Jumlah hadir minimal 1 orang.',
            'attendance_count.max' => 'Jumlah hadir maksimal 50 orang.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'phone.required' => 'Nomor handphone wajib diisi.',
            'position.required' => 'Jabatan wajib diisi.',
        ];
    }

    public function mount(string $code)
    {
        $this->invitation = Invitation::where('unique_code', $code)->firstOrFail();

        // If already registered, show the success state
        if ($this->invitation->is_used) {
            $this->submitted = true;
        }
    }

    public function submit()
    {
        $this->validate();

        // Create the registration
        Registration::create([
            'invitation_id' => $this->invitation->id,
            'attendance_count' => $this->attendance_count,
            'email' => $this->email,
            'phone' => $this->phone,
            'position' => $this->position,
        ]);

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

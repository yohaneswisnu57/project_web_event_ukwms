<?php

namespace App\Livewire;

use App\Enums\ParticipantOrigin;
use App\Models\Participant;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Contracts\View\View;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

class RegistrationForm extends Component
{
    use WithFileUploads;

    public const MAX_ATTEMPTS_PER_MINUTE = 10;

    public string $full_name = '';

    public string $email = '';

    public string $whatsapp = '';

    public string $origin = 'internal';

    public string $institution = '';

    public string $faculty = '';

    public string $student_number = '';

    public string $cohort_year = '';

    public string $food_allergy = '';

    public ?TemporaryUploadedFile $payment_proof = null;

    #[Locked]
    public ?string $registeredCode = null;

    #[Locked]
    public ?string $registeredEmail = null;

    /**
     * @return array<string, array<int, mixed>>
     */
    protected function rules(): array
    {
        return [
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique(Participant::class, 'email')],
            'whatsapp' => ['required', 'string', 'max:20', 'regex:/^(\+62|62|0)8[0-9]{7,12}$/'],
            'origin' => ['required', Rule::enum(ParticipantOrigin::class)],
            'institution' => [Rule::requiredIf($this->isExternal()), 'nullable', 'string', 'max:255'],
            'faculty' => ['required', 'string', 'max:255'],
            'student_number' => ['required', 'string', 'max:50'],
            'cohort_year' => ['required', 'integer', 'min:2000', 'max:'.now()->year],
            'food_allergy' => ['nullable', 'string', 'max:255'],
            'payment_proof' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function messages(): array
    {
        return [
            'required' => ':Attribute wajib diisi.',
            'institution.required' => 'Nama universitas wajib diisi untuk peserta eksternal.',
            'payment_proof.required' => 'Unggah foto bukti transfer.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email ini sudah dipakai untuk mendaftar. Gunakan alamat email lain.',
            'whatsapp.regex' => 'Gunakan nomor WhatsApp Indonesia, contoh 081234567890.',
            'cohort_year.integer' => 'Angkatan harus berupa tahun, contoh 2024.',
            'cohort_year.min' => 'Angkatan minimal tahun :min.',
            'cohort_year.max' => 'Angkatan maksimal tahun :max.',
            'payment_proof.image' => 'Bukti transfer harus berupa gambar.',
            'payment_proof.mimes' => 'Gunakan file JPG, PNG, atau WEBP.',
            'payment_proof.max' => 'Ukuran file maksimal 4 MB.',
            'max' => ':Attribute maksimal :max karakter.',
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function validationAttributes(): array
    {
        return [
            'full_name' => 'nama lengkap',
            'email' => 'email',
            'whatsapp' => 'nomor WhatsApp',
            'origin' => 'asal',
            'institution' => 'universitas',
            'faculty' => 'fakultas',
            'student_number' => $this->isExternal() ? 'NIM/NRP' : 'NRP',
            'cohort_year' => 'angkatan',
            'food_allergy' => 'alergi makanan',
            'payment_proof' => 'bukti transfer',
        ];
    }

    /**
     * Setelah submit gagal, kolom yang sudah ditandai salah divalidasi ulang
     * saat diubah supaya pesan galatnya hilang begitu diperbaiki.
     */
    public function updated(string $property): void
    {
        if ($this->getErrorBag()->has($property)) {
            $this->validateOnly($property);
        }
    }

    public function updatedPaymentProof(): void
    {
        $this->validateOnly('payment_proof');
    }

    public function updatedOrigin(): void
    {
        $this->resetValidation(['institution', 'student_number']);
    }

    public function register(): void
    {
        if (! Participant::isRegistrationOpen()) {
            return;
        }

        $this->email = strtolower(trim($this->email));
        $this->whatsapp = preg_replace('/[\s\-().]/', '', $this->whatsapp) ?? '';

        $throttleKey = 'retina-register:'.request()->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, self::MAX_ATTEMPTS_PER_MINUTE)) {
            throw ValidationException::withMessages([
                'form' => 'Terlalu banyak percobaan. Coba lagi dalam '.RateLimiter::availableIn($throttleKey).' detik.',
            ]);
        }

        $validated = $this->validate();

        RateLimiter::hit($throttleKey, 60);

        $proofPath = $this->payment_proof->store(Participant::PAYMENT_PROOF_DIRECTORY, Participant::PAYMENT_PROOF_DISK);

        try {
            $participant = Cache::lock('retina-registration', 10)->block(5, function () use ($validated, $proofPath): ?Participant {
                if (! Participant::isRegistrationOpen()) {
                    return null;
                }

                return Participant::create([
                    'full_name' => trim($validated['full_name']),
                    'email' => $validated['email'],
                    'whatsapp' => $validated['whatsapp'],
                    'origin' => $validated['origin'],
                    'institution' => filled($validated['institution']) ? trim($validated['institution']) : null,
                    'faculty' => trim($validated['faculty']),
                    'student_number' => trim($validated['student_number']),
                    'cohort_year' => (int) $validated['cohort_year'],
                    'food_allergy' => filled($validated['food_allergy']) ? trim($validated['food_allergy']) : null,
                    'payment_proof_path' => $proofPath,
                ]);
            });
        } catch (UniqueConstraintViolationException) {
            Storage::disk(Participant::PAYMENT_PROOF_DISK)->delete($proofPath);

            throw ValidationException::withMessages([
                'email' => 'Email ini sudah dipakai untuk mendaftar. Gunakan alamat email lain.',
            ]);
        } catch (LockTimeoutException) {
            Storage::disk(Participant::PAYMENT_PROOF_DISK)->delete($proofPath);

            throw ValidationException::withMessages([
                'form' => 'Server sedang sibuk menerima pendaftaran. Coba kirim ulang sebentar lagi.',
            ]);
        }

        if (! $participant) {
            Storage::disk(Participant::PAYMENT_PROOF_DISK)->delete($proofPath);

            return;
        }

        $this->registeredCode = $participant->code;
        $this->registeredEmail = $participant->email;

        $this->reset(['full_name', 'email', 'whatsapp', 'origin', 'institution', 'faculty', 'student_number', 'cohort_year', 'food_allergy', 'payment_proof']);
    }

    public function isExternal(): bool
    {
        return $this->origin === ParticipantOrigin::External->value;
    }

    public function render(): View
    {
        return view('livewire.registration-form', [
            'startsAt' => Carbon::parse(config('retina.starts_at')),
            'isRegistrationOpen' => Participant::isRegistrationOpen(),
            'registrationClosesAt' => Participant::registrationClosesAt(),
            'remainingQuota' => Participant::remainingQuota(),
        ])
            ->layout('layouts.retina')
            ->title('Formulir Pendaftaran — '.config('retina.name'));
    }
}

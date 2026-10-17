<?php

namespace App\Models;

use App\Enums\ParticipantOrigin;
use App\Enums\ParticipantStatus;
use Database\Factories\ParticipantFactory;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Participant extends Model
{
    /** @use HasFactory<ParticipantFactory> */
    use HasFactory;

    public const REGISTRATION_FEE = 30000;

    public const PAYMENT_PROOF_DISK = 'local';

    public const PAYMENT_PROOF_DIRECTORY = 'payment-proofs';

    public const SETTING_CLOSING_AT = 'retina_registration_closing_at';

    public const SETTING_QUOTA = 'retina_registration_quota';

    protected $fillable = [
        'full_name',
        'email',
        'whatsapp',
        'origin',
        'institution',
        'faculty',
        'student_number',
        'cohort_year',
        'food_allergy',
        'payment_proof_path',
        'status',
        'rejection_reason',
        'reviewed_at',
        'reviewed_by',
        'checked_in_at',
    ];

    protected $attributes = [
        'status' => 'pending',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'origin' => ParticipantOrigin::class,
            'status' => ParticipantStatus::class,
            'cohort_year' => 'integer',
            'reviewed_at' => 'datetime',
            'checked_in_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Participant $participant): void {
            $participant->uuid ??= (string) Str::uuid();
        });

        static::saving(function (Participant $participant): void {
            if ($participant->origin === ParticipantOrigin::Internal) {
                $participant->institution = null;
            }
        });

        static::updated(function (Participant $participant): void {
            $previousProofPath = $participant->getOriginal('payment_proof_path');

            if ($participant->wasChanged('payment_proof_path') && filled($previousProofPath)) {
                Storage::disk(self::PAYMENT_PROOF_DISK)->delete($previousProofPath);
            }
        });

        static::deleted(function (Participant $participant): void {
            if (filled($participant->payment_proof_path)) {
                Storage::disk(self::PAYMENT_PROOF_DISK)->delete($participant->payment_proof_path);
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    /**
     * Kode peserta yang tercetak di tiket, contoh: RETINA-0007.
     *
     * @return Attribute<string, never>
     */
    protected function code(): Attribute
    {
        return Attribute::get(fn (): string => 'RETINA-'.str_pad((string) $this->getKey(), 4, '0', STR_PAD_LEFT));
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Pendaftar yang menempati kuota (belum ditolak).
     */
    #[Scope]
    protected function occupyingQuota(Builder $query): void
    {
        $query->where('status', '!=', ParticipantStatus::Rejected);
    }

    #[Scope]
    protected function verified(Builder $query): void
    {
        $query->where('status', ParticipantStatus::Verified);
    }

    #[Scope]
    protected function pending(Builder $query): void
    {
        $query->where('status', ParticipantStatus::Pending);
    }

    public function isPending(): bool
    {
        return $this->status === ParticipantStatus::Pending;
    }

    public function isVerified(): bool
    {
        return $this->status === ParticipantStatus::Verified;
    }

    /**
     * Pendaftar lain (belum ditolak) dengan NRP/NIM atau nomor WhatsApp yang sama.
     *
     * @return Collection<int, Participant>
     */
    public function possibleDuplicates(): Collection
    {
        return static::query()
            ->occupyingQuota()
            ->whereKeyNot($this->getKey())
            ->where(fn (Builder $query) => $query
                ->where('student_number', $this->student_number)
                ->orWhere('whatsapp', $this->whatsapp))
            ->get();
    }

    public function markAsVerified(User $reviewer): void
    {
        $this->update([
            'status' => ParticipantStatus::Verified,
            'rejection_reason' => null,
            'reviewed_at' => now(),
            'reviewed_by' => $reviewer->getKey(),
        ]);
    }

    public function markAsRejected(User $reviewer, string $reason): void
    {
        $this->update([
            'status' => ParticipantStatus::Rejected,
            'rejection_reason' => $reason,
            'reviewed_at' => now(),
            'reviewed_by' => $reviewer->getKey(),
        ]);
    }

    public static function registrationClosesAt(): ?Carbon
    {
        $value = Setting::getValue(self::SETTING_CLOSING_AT);

        return $value ? Carbon::parse($value) : null;
    }

    public static function quota(): ?int
    {
        $value = Setting::getValue(self::SETTING_QUOTA);

        return filled($value) ? (int) $value : null;
    }

    /**
     * Sisa kuota, atau null jika kuota tidak dibatasi.
     */
    public static function remainingQuota(): ?int
    {
        $quota = self::quota();

        if ($quota === null) {
            return null;
        }

        return max(0, $quota - static::query()->occupyingQuota()->count());
    }

    public static function isRegistrationOpen(): bool
    {
        $closesAt = self::registrationClosesAt();

        if ($closesAt && now()->greaterThan($closesAt)) {
            return false;
        }

        return self::remainingQuota() !== 0;
    }
}

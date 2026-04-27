<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class Invitation extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'unique_code',
        'is_used',
    ];

    protected $casts = [
        'is_used' => 'boolean',
    ];

    /**
     * Boot method to auto-generate unique code on creation.
     */
    protected static function booted(): void
    {
        static::creating(function (Invitation $invitation) {
            if (empty($invitation->unique_code)) {
                $invitation->unique_code = self::generateUniqueCode();
            }
        });
    }

    /**
     * Generate a unique random code of 7 characters.
     */
    public static function generateUniqueCode(): string
    {
        $code = strtoupper(Str::random(7));

        // Ensure uniqueness
        while (self::where('unique_code', $code)->exists()) {
            $code = strtoupper(Str::random(7));
        }

        return $code;
    }

    /**
     * Get the registration for this invitation.
     */
    public function registration(): HasOne
    {
        return $this->hasOne(Registration::class);
    }

    /**
     * Get the shareable registration link.
     */
    public function getRegistrationLinkAttribute(): string
    {
        return url('/join/' . $this->unique_code);
    }
}

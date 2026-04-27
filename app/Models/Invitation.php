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
                $invitation->unique_code = self::generateUniqueCode($invitation->name);
            }
        });
    }

    /**
     * Generate a unique code based on the name.
     */
    public static function generateUniqueCode(string $name): string
    {
        $slug = Str::slug(Str::limit($name, 20, ''));
        $random = strtoupper(Str::random(6));

        $code = $slug . '-' . $random;

        // Ensure uniqueness
        while (self::where('unique_code', $code)->exists()) {
            $random = strtoupper(Str::random(6));
            $code = $slug . '-' . $random;
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

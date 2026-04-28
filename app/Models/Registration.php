<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Registration extends Model
{
    use HasFactory;
    protected $fillable = [
        'uuid',
        'invitation_id',
        'attendance_count',
        'email',
        'phone',
        'position',
        'validated_at',
        'checked_in_at',
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::creating(function ($registration) {
            $registration->uuid = (string) \Illuminate\Support\Str::uuid();
        });
    }

    protected $casts = [
        'validated_at' => 'datetime',
        'checked_in_at' => 'datetime',
    ];

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    /**
     * Get the invitation that owns this registration.
     */
    public function invitation(): BelongsTo
    {
        return $this->belongsTo(Invitation::class);
    }

    /**
     * Get the attendees for this registration.
     */
    public function attendees(): HasMany
    {
        return $this->hasMany(RegistrationAttendee::class);
    }
}

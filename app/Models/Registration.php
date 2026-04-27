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
        'invitation_id',
        'attendance_count',
        'email',
        'phone',
        'position',
    ];

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

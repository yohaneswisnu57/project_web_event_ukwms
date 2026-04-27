<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Registration extends Model
{
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
}

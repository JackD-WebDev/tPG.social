<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Invite extends Model
{
    use HasFactory;

    /**
     * @var string[]
     */
    protected $fillable = [
        'recipient_email',
        'sender_id',
        'crew_id',
        'token'
    ];

    /**
     * @return BelongsTo
     */
    public function crew(): BelongsTo
    {
        return $this->belongsTo(Crew::class);
    }

    /**
     * @return HasOne
     */
    public function recipient(): HasOne
    {
        return $this->hasOne(User::class, 'email', 'recipient_email');
    }

    /**
     * @return HasOne
     */
    public function sender(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'sender_id');
    }
}

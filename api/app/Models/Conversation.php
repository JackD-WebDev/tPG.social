<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Conversation extends Model
{
    use HasFactory;

    /**
     * @return BelongsToMany
     */
    public function participants(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'participants');
    }

    /**
     * @return HasMany
     */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    /**
     * @return mixed
     */
    public function getLatestMessageAttribute(): mixed
    {
        return $this->messages()->latest()->first();
    }

    /**
     * @param $userId
     * @return bool
     */
    public function isUnreadForUser($userId): bool
    {
        return (bool)$this->messages()
            ->whereNull('last_read')
            ->where('user_id', '<>', $userId)
            ->count();
    }

    /**
     * @param $userId
     * @return int
     */
    public function markAsReadForUser($userId): int
    {
        return $this->messages()
            ->whereNull('last_read')
            ->where('user_id', '<>', $userId)
            ->update([
                'last_read' => Carbon::now()
            ]);
    }
}

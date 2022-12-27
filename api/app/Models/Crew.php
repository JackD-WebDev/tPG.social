<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Crew extends Model
{
    use HasFactory;

    /**
     * @var string[]
     */
    protected $fillable = [
        'name',
        'organizer_id',
        'slug'
    ];

    /**
     * @return void
     */
    protected static function boot(): void
    {
        parent::boot();
        static::created(function ($crew) {
            $crew->members()->attach(auth()->id());
        });

        static::deleting(function ($crew) {
            $crew->members()->sync([]);
        });
    }

    /**
     * @return BelongsTo
     */
    public function organizer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'organizer_id');
    }

    /**
     * @return BelongsToMany
     */
    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withTimestamps();
    }

    /**
     * @return HasMany
     */
    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    /**
     * @param User $user
     * @return bool
     */
    public function hasUser(User $user): bool
    {
        return (bool)$this->members()
            ->where('user_id', $user->id)
            ->first();
    }

    /**
     * @return HasMany
     */
    public function invites(): HasMany
    {
        return $this->hasMany(Invite::class);
    }

    /**
     * @param $email
     * @return bool
     */
    public function hasPendingInvite($email): bool
    {
        return (bool)$this->invites()
            ->where('recipient_email', $email)
            ->count();
    }
}

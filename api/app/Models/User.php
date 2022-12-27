<?php

namespace App\Models;

use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Builder;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use MatanYadaev\EloquentSpatial\Objects\Point;
use MatanYadaev\EloquentSpatial\SpatialBuilder;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @method static create(array $array)
 */
class User extends Authenticatable //implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, TwoFactorAuthenticatable, Notifiable;

    /**
     * @var bool
     */
    public $incrementing = false;

    /**
     * @return void
     */
    protected static function boot(): void
    {
        parent::boot();
        static::creating(function ($model) {
            $model->{$model->getKeyName()} = (string) Str::uuid();
        });
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username',
        'email',
        'password',
        'tagline',
        'about',
        'formatted_address',
        'location',
        'upload_successful'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'location' => Point::class
    ];

    /**
     * @param $query
     * @return SpatialBuilder
     */
    public function newEloquentBuilder($query): SpatialBuilder
    {
        return new SpatialBuilder($query);
    }

    /**
     * @return HasOne
     */
    public function channel(): HasOne
    {
        return $this->hasOne(Channel::class);
    }

    /**
     * @return HasMany
     */
    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    /**
     * @return HasMany
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * @return BelongsToMany
     */
    public function crews(): BelongsToMany
    {
        return $this->belongsToMany(Crew::class)
            ->withTimestamps();
    }

    /**
     * @return BelongsToMany
     */
    public function createdCrews(): BelongsToMany
    {
        return $this->crews()
            ->where('organizer_id', $this->id);
    }

    /**
     * @param $crew
     * @return bool
     */
    public function isCrewOrganizer($crew): bool
    {
        return (bool)$this->crews()
            ->where('id', $crew->id)
            ->where('organizer_id', $this->id)
            ->count();
    }

    /**
     * @return HasMany
     */
    public function invites(): HasMany
    {
        return $this->hasMany(Invite::class, 'recipient_email', 'email');
    }

    /**
     * @return BelongsToMany
     */
    public function conversations(): BelongsToMany
    {
        return $this->belongsToMany(Conversation::class, 'participants');
    }

    /**
     * @return HasMany
     */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    /**
     * @param $user_id
     * @return Builder|\Illuminate\Database\Eloquent\Model|BelongsToMany|mixed|object|null
     */
    public function getConversationWithUser($user_id): mixed
    {
        return $this->conversations()
            ->whereHas('participants', function ($query) use ($user_id) {
                $query->where('user_id', $user_id);
            })
            ->first();
    }

    /**
     * @return HasMany
     */
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    /**
     * @return HasMany
     */
    public function images(): HasMany
    {
        return $this->hasMany(UserImage::class);
    }

    /**
     * @return HasOne
     */
    public function coverImage(): HasOne
    {
        return $this->hasOne(UserImage::class)
            ->orderByDesc('id')
            ->where('location', 'cover')
            ->withDefault(function ($userImage) {
                $userImage->path = 'uploads/user-images/cover-default-image.jpg';
            });
    }

    /**
     * @return HasOne
     */
    public function profileImage(): HasOne
    {
        return $this->hasOne(UserImage::class)
            ->orderByDesc('id')
            ->where('location', 'profile')
            ->withDefault(function ($userImage) {
                $userImage->path = 'uploads/user-images/profile-default-image.jpg';
            });
    }

    /**
     * @return BelongsToMany
     */
    public function supportedPosts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class, 'supports', 'user_id', 'post_id');
    }

    /**
     * @return HasMany
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }
}

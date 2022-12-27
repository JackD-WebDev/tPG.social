<?php

namespace App\Models;

use App\Models\Traits\Supportable;
use Cviebrock\EloquentTaggable\Taggable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Post extends Model
{
    use HasFactory, SoftDeletes, Supportable, Taggable;

    /**
     * @var string[]
     */
    protected $fillable = [
        'body',
        'image',
        'width',
        'height'
    ];

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return MorphMany
     */
    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable')
            ->orderBy('created_at', 'desc');
    }

    /**
     * @param $value
     * @return string|null
     */
    public function getBodyAttribute($value): ?string
    {
        if ($this->trashed()) {
            if (!auth()->check()) return null;
            return auth()->id() == $this->sender->id ?
                'YOU DELETED THIS MESSAGE.' : "{$this->sender->username} DELETED THIS MESSAGE.";
        }
        return $value;
    }
}

<?php

namespace App\Models;

use App\Models\Traits\Supportable;
use JetBrains\PhpStorm\ArrayShape;
use Illuminate\Support\Facades\Storage;
use Cviebrock\EloquentTaggable\Taggable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Project extends Model
{
    use HasFactory, Taggable, Supportable;

    /**
     * @var string[]
     */
    protected $fillable = [
        'user_id',
        'crew_id',
        'image',
        'title',
        'category',
        'description',
        'slug',
        'closed_to_comments',
        'is_live',
        'upload_successful',
        'disk'
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'is_live' => 'boolean',
        'upload_successful' => 'boolean',
        'closed_to_comments' => 'boolean'
    ];

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo
     */
    public function crew(): BelongsTo
    {
        return $this->belongsTo(Crew::class);
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
     * @return array
     */
    #[ArrayShape(['thumbnail' => "string", 'large' => "string", 'original' => "string"])]
    public function getImagesAttribute(): array
    {
        return [
            'thumbnail' => $this->getImagePath('thumbnail'),
            'large' => $this->getImagePath('large'),
            'original' => $this->getImagePath('original'),
        ];
    }

    /**
     * @param $size
     * @return string
     */
    protected function getImagePath($size): string
    {
        return Storage::disk($this->disk)
            ->url("uploads/projects/$size/" . $this->image);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserImage extends Model
{
    use HasFactory;

    /**
     * @var string[]
     */
    protected $fillable = [
        'image',
        'width',
        'height',
        'location'
    ];

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(UserImage::class);
    }
}

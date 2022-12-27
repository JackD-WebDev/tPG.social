<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Support extends Model
{
    use HasFactory;

    /**
     * @var string[]
     */
    protected $fillable = [
        'user_id'
    ];

    /**
     * @return MorphTo
     */
    public function supportable(): MorphTo
    {
        return $this->morphTo();
    }
}

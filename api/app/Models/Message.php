<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Message extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * @var string[]
     */
    protected $touches = ['conversation'];

    /**
     * @var string[]
     */
    protected $fillable = [
        'user_id',
        'conversation_id',
        'body',
        'last_read'
    ];

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

    /**
     * @return BelongsTo
     */
    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    /**
     * @return BelongsTo
     */
    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

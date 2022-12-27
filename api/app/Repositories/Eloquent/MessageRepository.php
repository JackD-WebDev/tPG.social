<?php
namespace App\Repositories\Eloquent;

use App\Models\Message;
use App\Repositories\Contracts\MessageInterface;

class MessageRepository extends BaseRepository implements MessageInterface
{
    /**
     * @return string
     */
    public function model(): string
    {
        return Message::class;
    }
}

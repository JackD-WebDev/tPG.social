<?php
namespace App\Repositories\Eloquent;

use App\Models\Channel;
use App\Repositories\Contracts\ChannelInterface;

class ChannelRepository extends BaseRepository implements ChannelInterface
{
    /**
     * @return string
     */
    public function model(): string
    {
        return Channel::class;
    }
}

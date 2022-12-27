<?php
namespace App\Repositories\Eloquent;

use App\Models\Invite;
use App\Repositories\Contracts\InviteInterface;

class InviteRepository extends BaseRepository implements InviteInterface
{
    /**
     * @return string
     */
    public function model(): string
    {
        return Invite::class;
    }

    /**
     * @param $crew
     * @param $user_id
     * @return void
     */
    public function addUserToCrew($crew, $user_id): void
    {
        $crew->members()->attach($user_id);
    }

    /**
     * @param $crew
     * @param $user_id
     * @return void
     */
    public function removeUserFromCrew($crew, $user_id): void
    {
        $crew->members()->detach($user_id);
    }
}

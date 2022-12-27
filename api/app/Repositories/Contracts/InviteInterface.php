<?php

namespace App\Repositories\Contracts;

interface InviteInterface extends BaseInterface
{
    public function addUserToCrew($crew, $user_id);
    public function removeUserFromCrew($crew, $user_id);
}

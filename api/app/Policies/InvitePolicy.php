<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Invite;
use Illuminate\Auth\Access\HandlesAuthorization;

class InvitePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Invite  $invite
     * @return mixed
     */
    public function view(User $user, Invite $invite)
    {
        //
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Invite  $invite
     * @return mixed
     */
    public function update(User $user, Invite $invite)
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Invite  $invite
     * @return mixed
     */
    public function delete(User $user, Invite $invite)
    {
        return $user->id == $invite->sender_id;
    }

    /**
     * Determine whether the user can resend the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Invite  $invite
     * @return mixed
     */
    public function resend(User $user, Invite $invite)
    {
        return $user->id == $invite->sender_id;
    }

    /**
     * Determine whether the user can respond to the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Invite  $invite
     * @return mixed
     */
    public function respond(User $user, Invite $invite)
    {
        return $user->email == $invite->recipient_email;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Invite  $invite
     * @return mixed
     */
    public function restore(User $user, Invite $invite)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Invite  $invite
     * @return mixed
     */
    public function forceDelete(User $user, Invite $invite)
    {
        //
    }
}

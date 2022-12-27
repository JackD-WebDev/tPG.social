<?php

namespace App\Mail;

use App\Models\Invite;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendInvitationToJoinCrew extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var Invite
     */
    public Invite $invite;
    /**
     * @var bool
     */
    public bool $user_exists;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Invite $invite, bool $user_exists)
    {
        $this->invite = $invite;
        $this->user_exists = $user_exists;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build(): static
    {
        if($this->user_exists) {
            $url = config('app.client_url').'/settings/crews';
            return $this->markdown('emails.invites.invite-existing-user')
                ->subject(auth()->user()->username.' has invited you to join '. $this->invite->crew->name)
                ->with([
                    'invite' => $this->invite,
                    'url' => $url
                ]);
        } else {
            $url = config('app.client_url').'/register?invite='.$this->invite->recipient_email;
            return $this->markdown('emails.invites.invite-new-user')
                ->subject(auth()->user()->username.' has invited you to join '. $this->invite->crew->name)
                ->with([
                    'invite' => $this->invite,
                    'url' => $url
                ]);
        }

    }
}

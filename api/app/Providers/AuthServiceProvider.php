<?php

namespace App\Providers;

use App\Models\Crew;
use App\Models\Post;
use App\Models\Video;
use App\Models\Invite;
use App\Models\Channel;
use App\Models\Comment;
use App\Models\Message;
use App\Models\Project;
use App\Policies\CrewPolicy;
use App\Policies\PostPolicy;
use App\Policies\VideoPolicy;
use App\Policies\InvitePolicy;
use App\Policies\ChannelPolicy;
use App\Policies\CommentPolicy;
use App\Policies\MessagePolicy;
use App\Policies\ProjectPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
        Project::class => ProjectPolicy::class,
        Comment::class => CommentPolicy::class,
        Crew::class => CrewPolicy::class,
        Invite::class => InvitePolicy::class,
        Message::class => MessagePolicy::class,
        Post::class => PostPolicy::class,
        Channel::class => ChannelPolicy::class,
        Video::class => VideoPolicy::class
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->registerPolicies();

        //
    }
}

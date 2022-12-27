<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Contracts\{TaskInterface,
    UserInterface,
    CrewInterface,
    PostInterface,
    VideoInterface,
    InviteInterface,
    ProjectInterface,
    CommentInterface,
    MessageInterface,
    ChannelInterface,
    ConversationInterface};
use App\Repositories\Eloquent\{TaskRepository,
    UserRepository,
    CrewRepository,
    PostRepository,
    VideoRepository,
    InviteRepository,
    ProjectRepository,
    CommentRepository,
    MessageRepository,
    ChannelRepository,
    ConversationRepository};

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->app->bind(UserInterface::class, UserRepository::class);
        $this->app->bind(ProjectInterface::class, ProjectRepository::class);
        $this->app->bind(CommentInterface::class, CommentRepository::class);
        $this->app->bind(CrewInterface::class, CrewRepository::class);
        $this->app->bind(InviteInterface::class, InviteRepository::class);
        $this->app->bind(ConversationInterface::class, ConversationRepository::class);
        $this->app->bind(MessageInterface::class, MessageRepository::class);
        $this->app->bind(PostInterface::class, PostRepository::class);
        $this->app->bind(ChannelInterface::class, ChannelRepository::class);
        $this->app->bind(VideoInterface::class, VideoRepository::class);
        $this->app->bind(TaskInterface::class, TaskRepository::class);

    }
}

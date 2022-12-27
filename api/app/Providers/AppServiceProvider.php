<?php

namespace App\Providers;

use App\Http\Helpers\ResponseHelper;
use Illuminate\Support\ServiceProvider;
use App\Repositories\Contracts\HelperInterface;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Http\Resources\Json\JsonResource;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->register(RepositoryServiceProvider::class);
        $this->app->register(FortifyServiceProvider::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        JsonResource::withoutWrapping();
        ResetPassword::createUrlUsing(function($notifiable, $token) {
            return url(config('app.client_url').'/reset-password/'.$token.'?email='.$notifiable->getEmailForPasswordReset());
        });
        $this->app->bind(HelperInterface::class, ResponseHelper::class);
    }
}

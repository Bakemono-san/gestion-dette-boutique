<?php

namespace App\Providers;

use App\Contracts\ArticleRepositoryImpl;
use App\Contracts\ArticleServiceInt;
use App\Contracts\ClientRepositoryInt;
use App\Facades\ClientRepositoryFacade;
use App\Repositories\ArticleRepository;
use App\Services\ArticleService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(ArticleServiceInt::class,ArticleService::class);
        $this->app->singleton(ArticleRepositoryImpl::class,ArticleRepository::class);
        $this->app->singleton(ClientRepositoryInt::class,ClientRepositoryFacade::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}

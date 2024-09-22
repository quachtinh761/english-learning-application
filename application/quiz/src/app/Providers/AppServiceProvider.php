<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $bindings = [
            \App\Http\Services\Interfaces\QuizServiceInterface::class => \App\Http\Services\QuizService::class,
            \App\Http\Services\Interfaces\QuizSubmissionServiceInterface::class => \App\Http\Services\QuizSubmissionService::class,
        ];

        foreach ($bindings as $abstract => $concrete) {
            $this->app->bind($abstract, $concrete);
        }
    }
}

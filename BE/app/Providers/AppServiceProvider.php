<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\StudentAnswerDetail;
use App\Observers\StudentAnswerDetailObserver;

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
        StudentAnswerDetail::observe(StudentAnswerDetailObserver::class);
    }
}

<?php

namespace App\Providers;

use App\View\Composers\PreviewRoleComposer;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        View::composer('*', PreviewRoleComposer::class);
    }
}

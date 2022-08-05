<?php

namespace App\Providers;

use App\Models\SimpleAlerte as Simple;
use TCG\Voyager\Facades\Voyager;
use Illuminate\Events\Dispatcher;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
        $this->app->register(\L5Swagger\L5SwaggerServiceProvider::class);
        Voyager::addAction(\App\Actions\FermetureDemande::class);
        Voyager::addAction(\App\Actions\FermetureQuickAlert::class);
        Voyager::addAction(\App\Actions\FermetureSimpleAlert::class);
        Voyager::addAction(\App\Actions\ActiveUser::class);
        Voyager::addAction(\App\Actions\DeactiveUser::class);
        // Voyager::addAction(\App\Actions\ValidateDeinstallation::class);
        // Voyager::addAction(\App\Actions\RefuseDeinstallation::class);
        Voyager::addAction(\App\Actions\ExportPosition::class);
        Voyager::addAction(\App\Actions\ExportPosition2::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {

        Simple::observe(\App\Observers\SimpleAlertObserver::class);
    }
}

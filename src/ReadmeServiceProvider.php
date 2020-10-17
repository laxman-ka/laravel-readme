<?php

namespace Diviky\Readme;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class ReadmeServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Route::group($this->routesConfig(), function () {
            $this->loadRoutesFrom($this->path() . '/routes/web.php');
        });

        if ($this->app->runningInConsole()) {
            $this->console();
        }
    }

    public function register()
    {
        $this->mergeConfigFrom($this->path() . '/config/readme.php', 'readme');
    }

    protected function path()
    {
        return __DIR__ . '/..';
    }

    /**
     * @return array
     */
    protected function routesConfig()
    {
        return [
            'prefix'     => config('readme.docs.route'),
            'namespace'  => 'Diviky\Readme\Http\Controllers',
            'domain'     => config('readme.domain', null),
            'as'         => 'readme.',
            'middleware' => config('readme.docs.middleware'),
        ];
    }

    protected function console()
    {
        $this->publishes([
            $this->path() . '/config/readme.php' => config_path('readme.php'),
        ], 'config');
    }
}

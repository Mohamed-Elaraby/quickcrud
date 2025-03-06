<?php

namespace Mohamedelaraby\QuickCrud;

use Illuminate\Support\ServiceProvider;

class QuickCrudServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                \Mohamedelaraby\QuickCrud\Console\Commands\GenerateCrud::class,
            ]);
        }
    }

    public function register()
    {
    }
}
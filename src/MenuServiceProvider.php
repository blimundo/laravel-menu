<?php

namespace Blimundo\Menu;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;

class MenuServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
    	AliasLoader::getInstance()->alias('MenuGenerator', Generator::class);

    	if ($this->app->runningInConsole()) {
    		$this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
    	}
    }
}

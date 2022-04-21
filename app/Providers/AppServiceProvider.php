<?php

namespace App\Providers;

use Illuminate\Support\Env;
use Illuminate\Support\ServiceProvider;
use VK\Client\Enums\VKLanguage;
use VK\Client\VKApiClient;

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
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->singleton(VKApiClient::class, function (): VKApiClient
        {
            return new VKApiClient(Env::get('VK_API'), VKLanguage::RUSSIAN);
        });
    }
}

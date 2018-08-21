<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Overtrue\Weather\Weather;

class WeatherServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Register the service provider.
     */
    public function register()
    {
        $this->app->singleton(Weather::class, function () {
            return new Weather(config('services.weather.ak'), config('services.weather.sn'));
        });

        $this->app->alias(Weather::class, 'weather');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        dd(config('services.weather.ak'));
        return [Weather::class, 'weather'];
    }
}

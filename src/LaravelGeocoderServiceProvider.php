<?php
namespace PDERAS\LaravelGeocoder;

use Illuminate\Support\ServiceProvider;

class LaravelGeocoderServiceProvider extends ServiceProvider
{
    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/config/geocode.php', 'geocode');
    }
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/config/geocode.php' => config_path('geocode.php'),
        ]);
    }
}
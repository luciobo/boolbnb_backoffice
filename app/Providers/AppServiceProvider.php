<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Braintree\Gateway;

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
        $this->app->singleton(Gateway::class, function($app){
            return new Gateway(
                [
                    'environment'=>'sandbox',
                    'merchantId'=>"njjdtsh27pvsd7nd",
                    'publicKey'=>"rbbyvyfn59xv2zxs",
                    "privateKey"=>"31f31381c64ce9066e04bdd93d41a9c9"
                ]
            );
        });
    }
}
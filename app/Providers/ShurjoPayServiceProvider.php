<?php

namespace App\Providers;

use GuzzleHttp\Client;
use ShurjopayPlugin\Shurjopay;
use Illuminate\Support\Facades\Http;
use ShurjopayPlugin\ShurjopayConfig;
use Illuminate\Support\ServiceProvider;
use ShurjopayPlugin\ShurjopayEnvReader;

class ShurjoPayServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public $sp_token;
    public function register()
    {
        $this->app->singleton(Shurjopay::class, function ($app) {
            $shurjopay = new Shurjopay($this->getShurjopayConfig());
            return $shurjopay;
        });

    }

    private function getShurjopayConfig(): ShurjopayConfig
    {
        $conf = new ShurjopayConfig();
        $conf->username = env('SP_USERNAME');
        $conf->password = env('SP_PASSWORD');
        $conf->api_endpoint = env('SHURJOPAY_API');
        $conf->callback_url = env('SP_CALLBACK');
        $conf->log_path = env('SP_LOG_LOCATION');
        $conf->order_prefix = env('SP_PREFIX');
        $conf->ssl_verifypeer = env('CURLOPT_SSL_VERIFYPEER');
        return $conf;
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}

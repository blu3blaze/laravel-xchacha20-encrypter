<?php

namespace Blu3blaze\Encrypter;

use Illuminate\Support\Str;
use Illuminate\Support\ServiceProvider;

class EncrypterServiceProvider extends ServiceProvider {
    /**
     * Register encrypter in application
     *
     * @return void
     */
    public function register(): void {
        $this->app->singleton('encrypter', function ($app) {
            $config = $app->make('config')->get('app');

            $key = Str::of($config['key'])->after('base64:')->fromBase64();

            return new Encrypter($key);
        });
    }
}
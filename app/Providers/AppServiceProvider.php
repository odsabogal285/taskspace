<?php

namespace App\Providers;

use Illuminate\Support\Facades\Response;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if ($this->app->environment('local')) {
            $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
            $this->app->register(TelescopeServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

        /*\Illuminate\Support\Facades\Response::macro('jsonErrors', function ($errors) {
            return response()->json([
                'errors' => $errors,
            ], 422);
        });*/
        Response::macro('success', function ($message, $data, $status = 200) {
            return Response::json([
                'status' => 'success',
                'message' => $message,
                'data' => $data
            ], $status);
        });

        Response::macro('error', function ($message, $status = 404) {
            return Response::json([
                'status' => 'error',
                'message' => $message,
                'data' => null
            ], $status);
        });
    }
}

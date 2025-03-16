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
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //Macro for success message

        Response::macro('success', function ($data = [], $message = 'Request was successful', $status = 200, $errors = []) {

            return  response()->json([
                'data' => $data,
                'meta'=> [
                    'message' => $message,
                    'status'=> $status,
                    'errors'=>$errors
                ]
            ],$status);
        });


        //Macro for Error Message
        Response::macro('error', function ($data = [], $message = 'There was an error', $status = 400, $errors = []) {

            return  response()->json([
                'data' => $data,
                'meta'=> [
                    'message' => $message,
                    'status'=> $status,
                    'errors'=>$errors
                ]
            ],$status);
        });

    }
}

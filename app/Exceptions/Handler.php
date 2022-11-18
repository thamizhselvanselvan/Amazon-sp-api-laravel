<?php

namespace App\Exceptions;

use Throwable;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {

            if (App::environment(['Production', 'production', 'Staging', 'staging'])) {

                $getMessage = $e->getMessage();
                $getCode = $e->getCode();
                $getFile = $e->getFile();
                $getLine = $e->getLine();

                $slackMessage = "Message: $getMessage
                Code: $getCode
                File: $getFile
                Line: $getLine";
                Log::channel('slack_360')->error($slackMessage);
            }
        });
    }


    /**
     * Render an exception into an HTTP response.
     * 
     * @param \Illumninate\Http\Request $request
     * @param \Throwable $throwable
     * @return \Illuminate\Http\Response
     */
    public function render($request, Throwable $throwable)
    {
        return parent::render($request, $throwable);
    }
}

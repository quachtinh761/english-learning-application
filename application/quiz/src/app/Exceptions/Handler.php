<?php

namespace App\Exceptions;

use App\Http\Responses\InternalServerErrorResponse;
use App\Http\Responses\InvalidInputResponse;
use App\Http\Responses\NotFoundErrorResponse;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Mockery\Matcher\Not;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
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
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request $request
     * @param Throwable $e
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws Throwable
     */
    public function render($request, Throwable $e)
    {
        if ($e instanceof \Illuminate\Validation\ValidationException) {
            return InvalidInputResponse::create($e->errors());
        }

        if ($e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {
            return NotFoundErrorResponse::create($e->getMessage());
        }

        return InternalServerErrorResponse::create($e->getMessage());
    }
}

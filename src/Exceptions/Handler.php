<?php

namespace Singsys\LQ\Exceptions;

use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Encryption\DecryptException;
use League\OAuth2\Server\Exception\OAuthServerException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Convert an authentication exception into a response.
     *
     * @param \Illuminate\Http\Request                 $request
     * @param \Illuminate\Auth\AuthenticationException $exception
     *
     * @return \Illuminate\Http\Response
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        $response = app('Lq\Response');
        $response->message = $exception->getMessage();
        $response->error_code = 'unauthenticated';

        return $response->out(401);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Exception               $exception
     *
     * @return \Illuminate\Http\Response
     */
    public function lqRender($request, Exception $exception)
    {
        $response = app('Lq\Response');

        if ($exception instanceof ValidationException) {
            $response->errors = $exception->validator->errors();
            $response->error = $exception->validator->errors()->first();

            return $response->out(422);
        } elseif ($exception instanceof DecryptException) {
            $response->message = $exception->getMessage();
            $response->error_code = 'unable_to_decrypt';

            return $response->out(500);
        } elseif ($exception instanceof OAuthServerException) {
            $response->message = $exception->getMessage();
            $response->error_code = !$response->error_code ? $exception->getErrorType() : $response->error_code;

            return $response->out($exception->getHttpStatusCode());
        } elseif ($exception instanceof AuthorizationException) {
            $response->message = 'User does have the permission to access this route.';
            $response->error_code = 'forbidden';

            return $response->out(403);
        }

        return parent::render($request, $exception);
    }
}

# composer require singsys/laravel-quick 
- Add trait "Singsys\LQ\Lib\Concerns\LqResponse" in the file: "App\Http\Controllers\Controller"
```
After that you can use the following methods in your controller 
setData(Array $data) to set the data in Response conllection.
setError(String $error) to set the single error
setErrors(Array $errors) to set all errors of form.
setErrorCode(String #error_code) to set the error code.
response(Number $request_status_code = 200) to set the request status
```
- Add trait "Singsys\LQ\Lib\Concerns\LqToken" in User model, and after you can directly generate the Pasword token from the user model Like:
```
$user = User::findOrFail($id);
$token = $user->createUserToken();
$user->setAttribute('token', $token);
return $this->setData(['user' => $user])
    ->response();
```
- Add override the following method of the file: App\Exceptions\Handler
```
    /**
     * Convert an authentication exception into a response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Auth\AuthenticationException  $exception
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
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        $response = app('Lq\Response');

        if( $exception instanceof ValidationException ) {

            $response->errors = $exception->validator->errors();
            $response->error = $exception->validator->errors()->first();
            return $response->out(422);
        }
        else if($exception instanceof DecryptException) {

            $response->message = $exception->getMessage();
            $response->error_code = 'unable_to_decrypt';
            return $response->out(500);
        }
        else if($exception instanceof OAuthServerException) {

            $response->message = $exception->getMessage();
            $response->error_code = !$response->error_code ? $exception->getErrorType() : $response->error_code;
            return $response->out($exception->getHttpStatusCode());
        }
        else  if($exception instanceof AuthorizationException) {

            $response->message =  'User does have the permission to access this route.';
            $response->error_code = 'forbidden';
            return $response->out(403);
        }
        else if ($request->wantsJson()) {

            // Define the response
            $response->error_code = 'internal_server_error';
            // Add the exception class name, message and stack trace to response
            $response->exception = get_class($exception); // Reflection might be better here
            $response->message = $exception->getMessage();
            $response->trace = $exception->getTrace();

            // Default response of 400
            $status = 400;

            // If this exception is an instance of HttpException
            if ($this->isHttpException($exception)) {
                // Grab the HTTP status code from the Exception
                $status = $exception->getStatusCode();
            }

            // Return a JSON response with the response array and status code
            return $response->out($status);
        }

       return parent::render($request, $exception);
    }
```
- Execute the following command on terminal:
```
php artisan migrate
php artisan lq:install
```


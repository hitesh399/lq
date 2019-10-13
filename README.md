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
- replace the file App\Exceptions\Handler from below content
```
<?php

namespace App\Exceptions;

use Singsys\LQ\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    /**
     * Render an exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Exception               $exception
     *
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        if ($request->is('api/*')) {
            return $this->lqRender($request, $exception);
        }

        return parent::render($request, $exception);
    }
}

```
- Execute the following command on terminal:
```
php artisan migrate
php artisan lq:install
php artisan vendor:publish --tag=lq-config
```
- Modify the RouteServiceProvider file, you just need to put the LqApiMiddleware globally for all Apis Like:
```
protected function mapApiRoutes()
{
    Route::prefix('api')
         ->middleware([\Singsys\LQ\Middleware\LqApiMiddleware::class])
         ->namespace($this->namespace . '\Api')
         ->group(base_path('routes/api.php'));
}
```
    

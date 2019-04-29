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

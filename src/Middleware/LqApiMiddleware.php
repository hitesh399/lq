<?php

namespace Singsys\LQ\Middleware;

use Closure;
use Singsys\LQ\Lib\ClientRepository;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Validation\ValidationException;
use Illuminate\Contracts\Auth\Factory as Auth;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Auth\Access\AuthorizationException;

class LqApiMiddleware extends Authenticate
{

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, ...$guard)
    {

        $request::macro('client', function () {return null; });
        $request::macro('device', function () {return null; });
        # Save Request Error Log into Database
        app('LqRequestLog')->createRequest();

        /**
         * Client ID should be present in every request header
         */
        $this->verifyClient($request);

        /**
         * the device id should be present in every request header
         */
        $this->findDeviceInfo($request);
        /**
         * Check the Route Access
         */
        $this->checkRouteAccess($request, ['api']);

        return $next($request);
    }
    /**
     * throw exception, if Client id is not valid.
     */
    private function invalidClientResponse() {

        app('Lq\Response')->message = trans('auth.invalid_client');
        throw ValidationException::withMessages([]);
    }

    /**
     * Throw the exception, if Device is not present in header
     */
    private function invalidDeviceIdResponse() {

        app('Lq\Response')->message = trans('auth.invalid_device_id');
        throw ValidationException::withMessages([]);
    }

    /**
     * To verify the client id
     */
    private function verifyClient($request)  {
        $lq_client = new ClientRepository();
        try {

            $client_id = \Crypt::decryptString($request->header('client-id'));
            $client = $lq_client->getClient($client_id);

            if (!$client) {
                $this->invalidClientResponse();
            }

            $request::macro('client', function () use ($client) {
                return $client;
            });

        } catch (DecryptException $e) {

            $this->invalidClientResponse();
        }
    }

    /**
     * To Find and create the device
     */
    private function findDeviceInfo($request) {

        if (!$request->header('device-id')) {
            $this->invalidDeviceIdResponse();
        }

        $device_id = $request->header('device-id');
        $request::macro('device', function () use ($device_id, $request) {

            $model = config('lq.device_class');
            return $model::firstOrCreate(['device_id' => $device_id], ['device_id' => $device_id, 'client_id' => $request->client()->id]);
        });
    }

    /**
     * To check the current route permission for the login user.
     */
    private function checkRouteAccess($request, $guards) {

        $permission = app('permission');

        /**
         * Get the Current Route detail from database or cache.
         */
        $current_permission = $permission->current();

        $lq_response = app('Lq\Response');

        if ($current_permission && isset($current_permission['is_public']) && $current_permission['is_public'] == 'N') {
            # Check the user does have the valid token to access current route.
            $this->authenticate($request, $guards);

            # Get Login user Role id.
            $role_id = $request->user()->role_id;

            # Add Current Permission Limitation and allowed field in final response.
            $current_permission = $permission->roleCurrentPermission($role_id);

            # Removing database table column from response.
            if($current_permission && !empty($current_permission['fields'])) {

                $fields = $current_permission['fields'];
                array_walk($fields, function (&$field){
                    unset($field['table_columns']);
                    return $field;
                });
                $current_permission['fields'] = $fields;
            }

            $lq_response->current_permission = $current_permission;

            # Check the curent user has the privilege to access the current route.

            if (!$permission->canAccess($role_id) && $role_id != 1) {
                throw new AuthorizationException;
            }
        }
    }
}

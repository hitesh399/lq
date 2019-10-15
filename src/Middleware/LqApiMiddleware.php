<?php

namespace Singsys\LQ\Middleware;

use Closure;
use Illuminate\Http\Request;
use Singsys\LQ\Lib\ClientRepository;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Encryption\DecryptException;

class LqApiMiddleware extends Authenticate
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request [All data]
     * @param \Closure                 $next    [Callback function to execute next process]
     * @param string|null              $guard   [Auth Guard]
     *
     * @return mixed
     */
    public function handle($request, Closure $next, ...$guard)
    {
        $request::macro(
            'client', function () {
                return null;
            }
        );
        $request::macro(
            'device', function () {
                return null;
            }
        );

        // Save Request Error Log into Database
        if (env('LQ_WRITE_ERROR')) {
            app('LqRequestLog')->createRequest();
        }

        /*
         * Client ID should be present in every request header
         */
        $this->_verifyClient($request);

        /*
         * the device id should be present in every request header
         */
        $this->_findDeviceInfo($request);
        /*
         * Check the Route Access
         */
        $this->_checkRouteAccess($request, ['api']);

        $response = $next($request);
        if (env('LQ_WRITE_ERROR')) {
            app('LqRequestLog')->ok($response);
        }

        return $response;
    }

    /**
     * throw exception, if Client id is not valid.
     *
     * @return void|
     */
    private function _invalidClientResponse()
    {
        app('Lq\Response')->message = trans('auth.invalid_client');
        throw ValidationException::withMessages([]);
    }

    /**
     * Throw the exception, if Device is not present in header.
     *
     * @return void|
     */
    private function _invalidDeviceIdResponse()
    {
        app('Lq\Response')->message = trans('auth.invalid_device_id');
        throw ValidationException::withMessages([]);
    }

    /**
     * To verify the client id.
     *
     * @param \Illuminate\http\Request $request [All Request data]
     *
     * @return void|
     */
    private function _verifyClient(Request $request)
    {
        $lq_client = new ClientRepository();
        try {
            $client_id = \Crypt::decryptString($request->header('client-id'));
            $client = $lq_client->getClient($client_id);

            if (!$client) {
                $this->_invalidClientResponse();
            }
            $request::macro(
                'client', function () use ($client) {
                    return $client;
                }
            );
        } catch (DecryptException $e) {
            $this->_invalidClientResponse();
        }
    }

    /**
     * To Find and create the device.
     *
     * @param \Illuminate\http\Request $request [All Request data]
     *
     * @return void|
     */
    private function _findDeviceInfo(Request $request)
    {
        if (!$request->header('device-id')) {
            $this->_invalidDeviceIdResponse();
        }

        $device_id = $request->header('device-id');
        $request::macro(
            'device', function () use ($device_id, $request) {
                $model = config('lq.device_class');

                return $model::firstOrCreate(
                    ['device_id' => $device_id],
                    [
                        'device_id' => $device_id,
                        'client_id' => $request->client()->id,
                    ]
                );
            }
        );
    }

    /**
     * Authenticate user without throw the Exception.
     *
     * @param \Illuminate\http\Request $request [All Request data]
     * @param array                    $guards  [Auth gaurd]
     *
     * @return void|
     */
    private function _authenticateWithoutException(Request $request, $guards)
    {
        if ($request->header('Authorization')) {
            try {
                $this->authenticate($request, $guards);
            } catch (\Exception $e) {
                // No need to throw exception,
            }
        }
    }

    /**
     * To set the current role in Permission Repo.
     *
     * @param \Illuminate\http\Request $request [All Request data]
     *
     * @return void|
     */
    private function _setCurrentRoleIdsInPermissonRepo(Request $request): void
    {
        $permission = app('permission');
        // Set User Current Role Id
        $user_device = \Auth::user()->devices()->where(
            'devices.id', $request->device()->id
        )->first();
        // dd($user_device);
        if ($user_device) {
            if ($request->client()->role_access_type == 'one_at_time') {
                $permission->setCurrentRoleIds([$user_device->pivot->role_id]);
            } else {
                $permission->setCurrentRoleIds(
                    $request->user()->roles->pluck('id')->toArray()
                );
            }
        }
    }

    /**
     * To add only current app role in Auth Collection.
     *
     * @param \Illuminate\http\Request $request [All Request data]
     *
     * @return void|
     */
    private function _onlyAppRoles(Request $request): void
    {
        $client_id = $request->client()->id;
        $roles = $request->user()->roles->filter(
            function ($item) use ($client_id) {
                return in_array($client_id, $item->client_ids);
            }
        );
        $request->user()->setRelation('roles', $roles);
    }

    /**
     * To check the current route permission for the login user.
     *
     * @param \Illuminate\http\Request $request [All Request data]
     *
     * @return void|
     */
    private function _checkRouteAccess($request, $guards)
    {
        $permission = app('permission');

        /**
         * Get the Current Route detail from database or cache.
         */
        $current_permission = $permission->current();

        if ($current_permission && isset($current_permission['is_public']) && $current_permission['is_public'] == 'N') {
            // Check the user does have the valid token to access current route.
            if (\Config::get('lq.check_authentication')) {
                $this->authenticate($request, $guards);
            } else {
                $this->_authenticateWithoutException($request, $guards);
            }

            $this->_assignCurrentPermissions($request);
        } elseif ($request->header('Authorization')) {
            $this->_authenticateWithoutException($request, $guards);
            $this->_assignCurrentPermissions($request);
        }
    }

    /**
     * To set the current route permission in response.
     *
     * @param \Illuminate\http\Request $request [All Request data]
     *
     * @return void|
     */
    private function _assignCurrentPermissions($request)
    {
        $permission = app('permission');
        if ($request->user()) {
            $lq_response = app('Lq\Response');
            // Get Login user Role id.
            $this->_onlyAppRoles($request);
            $this->_setCurrentRoleIdsInPermissonRepo($request);
            $active_role_id = $permission->canAccess();
            if ($active_role_id) {
                $permission->setActiveRoleId($active_role_id);
            }

            // Add Current Permission Limitation and allowed field in final response.
            $current_permission = $permission->getCurrentRolePermissions();

            // Removing database table column from response.
            if ($current_permission && !empty($current_permission['fields'])) {
                $fields = $current_permission['fields'];
                array_walk(
                        $fields,
                        function (&$field) {
                            unset($field['table_columns']);

                            return $field;
                        }
                    );
                $current_permission['fields'] = $fields;
            }

            $lq_response->current_permission = $current_permission;
            // Check the curent user has the privilege to access the current route.

            if (!$active_role_id && \Config::get('lq.check_authentication')) {
                throw new AuthorizationException();
            }
        }
    }
}

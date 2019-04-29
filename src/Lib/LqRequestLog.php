<?php

namespace Singsys\LQ\Lib;
use Config;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
//use Illuminate\Support\Str;

class LqRequestLog {

    public $requestLogModel = null;
    public $requestLog = null;

    public function __construct() {

        $this->requestLogModel = Config::get('lq.request_log_class', \Singsys\LQ\Models\RequestLog::class);
    }

    /**
     * Store the request data into database
     */
    public function createRequest() {

        $request = app(Request::class);

        if($request->path() == 'api/developer/request-log') {
            return $this;
        }

        $url = $request->fullUrl();
        $ip_address = $request->ip();
        $request_data = $request->toArray();
        $request_method =  $request->getMethod();
        $route_name = Route::currentRouteName();
        $user_id  = $request->user() ? $request->user()->id : null;
        $request_headers = $request->headers->all();

        $this->requestLog = $this->requestLogModel::create([
            'url' => $url,
            'ip_address' => $ip_address ? $ip_address : '',
            'request' => $request_data,
            'request_method' => $request_method,
            'route_name' => $route_name ? $route_name : '',
            'user_id' => $user_id,
            'request_headers' => $request_headers,
        ]);

        return $this;
    }
    /**
     * End the request with a exception
     */
    public function expection (Exception $e) {

    }
    /**
     * @param Response | JsonResponse
     */
    public function ok( $response) {

        if($response instanceof JsonResponse && $this->requestLog) {

            $headers = $response->headers->all();
            $data = $response->getData();
            $status = $response->status();

            $this->requestLog->update(
                array_merge( [
                    'response_headers' => $headers,
                    'response' => $data,
                    'response_status' => $status === 500 ? 'exception' : 'ok',
                    'status_code' => $status
                    ], $this->identifications()
                )
            );
        }

    }

    /**
     * To get the client id and device id
     */
    public function identifications () {

        $request = app(Request::class);
        $device_id = $request->device() ? $request->device()->id : null;
        $client_id =  $request->client() ? $request->client()->id : null;
        return compact('device_id', 'client_id');
    }
}

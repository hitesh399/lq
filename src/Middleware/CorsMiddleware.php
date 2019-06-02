<?php

namespace Singsys\LQ\Middleware;

use Closure;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class CorsMiddleware {

   public function handle($request, Closure $next)
   {
        // ALLOW OPTIONS METHOD
        $headers = [
            'Access-Control-Allow-Methods'=> 'POST, GET, OPTIONS, PUT, DELETE, PATCH',
            'Access-Control-Allow-Headers'=> 'cache-control, x-requested-with, Content-Type, Origin, client-id, device-id, Authorization, time-offset',
            'Access-Control-Allow-Origin' => '*'
        ];
        if($request->getMethod() == "OPTIONS") {
            // The client-side application can set only headers allowed in Access-Control-Allow-Headers
            return \Response::json(['status' => true], 200, $headers);
        }

        $response = $next($request);

        foreach($headers as $key => $value) {

            if($response instanceof BinaryFileResponse) {
                $response->headers->set($key, $value);

            } else {
                $response->header($key, $value);
            }
        }
        return $response;
    }
}

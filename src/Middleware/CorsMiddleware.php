<?php

namespace Singsys\LQ\Middleware;

use Closure;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class CorsMiddleware
{
    public function handle($request, Closure $next)
    {
        $site_config = app('site_config');
        // ALLOW OPTIONS METHOD
        $headers = [
            'Access-Control-Allow-Methods'=> 'POST, GET, OPTIONS, PUT, DELETE, PATCH',
            'Access-Control-Allow-Headers'=> 'cache-control, x-requested-with, Content-Type, Origin, client-id, device-id, Authorization, time-offset'
        ];
        $possibleOrigins = $site_config->get('ACCESS_CONTROL_ALLOW_ORIGIN');

        if (env('APP_DEBUG')) {
            $headers['Access-Control-Allow-Origin'] = '*';
        } elseif ($possibleOrigins) {
            $possibleOrigins = explode(',', $possibleOrigins);
            if (in_array($request->header('origin'), $possibleOrigins)) {
                $headers['Access-Control-Allow-Origin'] = $request->header('origin');
            }
        }

        if ($request->getMethod() == "OPTIONS") {
            // The client-side application can set only headers allowed in Access-Control-Allow-Headers
            return \Response::json(['status' => true], 200, $headers);
        }

        $response = $next($request);

        foreach ($headers as $key => $value) {
            if ($response instanceof BinaryFileResponse) {
                $response->headers->set($key, $value);
            } else {
                $response->header($key, $value);
            }
        }
        return $response;
    }
}

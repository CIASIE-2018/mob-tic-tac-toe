<?php

namespace App\Http\Middleware;

use Closure;

class SuperMiddleware
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        $response->setContent("DEBUT" . $response->content() . "FIN");

        return $response;
    }
}

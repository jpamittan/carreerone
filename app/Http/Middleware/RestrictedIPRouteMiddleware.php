<?php

namespace App\Http\Middleware;

use Closure;

/**
 * Class RestrictedIPRouteMiddleware
 * @package App\Http\Middleware
 */
class RestrictedIPRouteMiddleware {
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next) {
        $restrictedIpAddresses = config('restricted_ip.addresses');
        if (!empty($restrictedIpAddresses) && !in_array(getenv('REMOTE_ADDR'), $restrictedIpAddresses)) {
            logger(getenv('REMOTE_ADDR') . " tried to access a restricted route URL");
            return abort(404);
        } else {
            return $next($request);
        }
    }
}

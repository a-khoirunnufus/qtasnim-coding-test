<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BasicAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->hasHeader('Authorization')) {
            return response()->json(['error' => 'Request unauthorized!'], 401);
        }

        $value = $request->header('Authorization');
        $value = explode(' ', $value);

        if (!isset($value[0]) && !isset($value[1])) {
            return response()->json(['error' => 'Request unauthorized!'], 401);
        }

        if ($value[0] != 'Basic') {
            return response()->json(['error' => 'Request unauthorized!'], 401);
        }

        $decode = base64_decode($value[1]);
        $decode = explode(':', $decode);

        if (!isset($decode[0]) || !isset($decode[1])) {
            return response()->json(['error' => 'Request unauthorized!'], 401);
        }

        if ($decode[0] != 'qtasnim-coding-test' || $decode[1] != 'qtasnim-coding-test') {
            return response()->json(['error' => 'Request unauthorized!'], 401);
        }

        return $next($request);
    }
}

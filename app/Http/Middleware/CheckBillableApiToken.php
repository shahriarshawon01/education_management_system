<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckBillableApiToken
{
    public function handle(Request $request, Closure $next)
    {
        // $token = $request->header('api_key') ?? $request->query('api_key');
        $token = $request->header('api_key')
            ?? $request->header('api-key')
            ?? $request->query('api_key');
        $tokenKey = 'RANDOM098093247TEST';

        if ($token != $tokenKey) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        return $next($request);
    }
}



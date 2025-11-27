<?php

namespace App\Http\Middleware;

use App\Helpers\Helper;
use Closure;
use Illuminate\Http\Request;

class StudentAuthCheckMiddleware
{
    use Helper;

    public function handle(Request $request, Closure $next)
    {
        if (auth()->check() && auth()->user()->type == 5){
            return $next($request);
        }

        return $this->notPermitted();
    }
}

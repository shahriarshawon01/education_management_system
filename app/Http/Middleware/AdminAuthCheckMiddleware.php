<?php

namespace App\Http\Middleware;

use App\Helpers\Helper;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminAuthCheckMiddleware
{
    use Helper;

    // public function handle(Request $request, Closure $next)
    // {
    //     if (auth()->check() && auth()->user()->type != 3) {
    //         $this->insertActivityLog($request);
    //         return $next($request);
    //     }
    //     return $this->notPermitted();
    // }

    public function handle(Request $request, Closure $next)
    {
        // Permission check
        if (!auth()->check() || auth()->user()->type == 3) {
            return $this->notPermitted();
        }

        // Optional: skip log for some routes
        $excludedPaths = [
            'api/canteen_weekly_menu/download/*',
            'api/general',
        ];

        foreach ($excludedPaths as $path) {
            if (\Str::is($path, $request->path())) {
                return $next($request);
            }
        }

        // Validate LogData header
        $logHeader = $request->header('LogData');
        $logData = $logHeader ? json_decode($logHeader) : null;

        if (is_object($logData) && isset($logData->method)) {
            $this->insertActivityLog($request);
        }

        return $next($request);
    }


    public function insertActivityLog($request)
    {
        $logData = json_decode($request->header('LogData'));
        $exceptedUrl = ['api/general'];
        if ($logData->method != 'get' && !in_array(request()->path(), $exceptedUrl)) {
            $input = $request->all();
            unset($input['log']);
            $data = json_encode($input);

            $ip = $this->getClientIp();
            $host = gethostbyaddr($ip);
            if ($host === $ip) {
                $host = 'Unknown';
            }

            $driver = env('LOG_DATABASE_DRIVER') ? env('LOG_DATABASE_DRIVER') : null;
            DB::connection($driver)->table('activity_logs')->insert([
                'user_id' => Auth()->user()->id,
                'url' => $logData->url,
                'host_name' => $host,
                'method' => $logData->method,
                'ip_address' => $ip,
                'module' => $logData->module,
                'data' => $data
            ]);
        }
    }

    public function getClientIp()
    {
        $request = request();

        $headers = [
            'HTTP_X_REAL_IP',
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'REMOTE_ADDR',
        ];

        foreach ($headers as $key) {
            if ($ip = $request->server($key)) {
                return trim(explode(',', $ip)[0]);
            }
        }

        return $request->getClientIp();
    }
}

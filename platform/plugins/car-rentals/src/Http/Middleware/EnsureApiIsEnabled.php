<?php

namespace Botble\CarRentals\Http\Middleware;

use Botble\Api\Facades\ApiHelper;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class EnsureApiIsEnabled
{
    public function handle(Request $request, Closure $next)
    {
        if (! class_exists('ApiHelper') || ! ApiHelper::enabled()) {
            return response()->json([
                'message' => 'API is disabled. Please contact the administrator.',
                'error' => 'API_DISABLED',
            ], Response::HTTP_SERVICE_UNAVAILABLE);
        }

        return $next($request);
    }
}

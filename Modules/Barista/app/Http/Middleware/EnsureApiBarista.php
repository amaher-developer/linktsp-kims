<?php

namespace Modules\Barista\Http\Middleware;

use Closure;
use Modules\Staff\Models\Staff;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureApiBarista
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user instanceof Staff || ! $user->isBarista()) {
            abort(403, 'This endpoint is only available to barista accounts.');
        }

        return $next($request);
    }
}

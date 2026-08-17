<?php

namespace Modules\Cashier\Http\Middleware;

use Closure;
use Modules\Staff\Models\Staff;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureApiCashier
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user instanceof Staff || ! $user->isCashier()) {
            abort(403, 'This endpoint is only available to cashier accounts.');
        }

        return $next($request);
    }
}

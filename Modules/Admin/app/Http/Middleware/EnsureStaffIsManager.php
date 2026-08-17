<?php

namespace Modules\Admin\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * The admin panel is manager/admin only — barista and cashier roles get
 * mobile/API access in a later phase, not a session-based Blade login.
 */
class EnsureStaffIsManager
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user('staff')?->isManager()) {
            abort(403);
        }

        return $next($request);
    }
}

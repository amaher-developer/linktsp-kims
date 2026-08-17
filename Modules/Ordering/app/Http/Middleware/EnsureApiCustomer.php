<?php

namespace Modules\Ordering\Http\Middleware;

use Closure;
use Modules\Ordering\Models\Customer;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureApiCustomer
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user() instanceof Customer) {
            abort(403, 'This endpoint is only available to customer accounts.');
        }

        return $next($request);
    }
}

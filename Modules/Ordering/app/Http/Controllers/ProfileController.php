<?php

namespace Modules\Ordering\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Ordering\Http\Resources\CustomerResource;

class ProfileController extends Controller
{
    public function show(Request $request): CustomerResource
    {
        return new CustomerResource($request->user()->load('loyaltyAccount'));
    }
}

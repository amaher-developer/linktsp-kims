<?php

namespace Modules\Ordering\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\Ordering\Http\Requests\CustomerLoginRequest;
use Modules\Ordering\Http\Resources\CustomerResource;
use Modules\Ordering\Models\Customer;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class CustomerAuthController extends Controller
{
    public function login(CustomerLoginRequest $request): JsonResponse
    {
        $customer = Customer::where('mobile', $request->string('mobile'))->first();

        if (! $customer || ! Hash::check($request->string('password'), $customer->password) || ! $customer->is_active) {
            throw ValidationException::withMessages([
                'mobile' => __('auth.failed'),
            ]);
        }

        $token = $customer->createToken($request->string('device_name')->toString() ?: 'mobile-app');

        return response()->json([
            'customer' => new CustomerResource($customer),
            'token' => $token->plainTextToken,
        ]);
    }
}

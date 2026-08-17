<?php

namespace Modules\Staff\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\Staff\Http\Requests\StaffLoginRequest;
use Modules\Staff\Http\Resources\StaffResource;
use Modules\Staff\Models\Staff;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class StaffAuthController extends Controller
{
    public function login(StaffLoginRequest $request): JsonResponse
    {
        $staff = Staff::where('email', $request->string('email'))->first();

        if (! $staff || ! $staff->password || ! Hash::check($request->string('password'), $staff->password) || ! $staff->is_active) {
            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        $token = $staff->createToken($request->string('device_name')->toString() ?: 'staff-app');

        return response()->json([
            'staff' => new StaffResource($staff->load('role')),
            'token' => $token->plainTextToken,
        ]);
    }
}

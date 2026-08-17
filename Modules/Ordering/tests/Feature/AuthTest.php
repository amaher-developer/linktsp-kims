<?php

use Modules\Ordering\Models\Customer;

test('customer can log in with mobile and password and receives a bearer token usable on a protected route', function () {
    $customer = Customer::factory()->create([
        'mobile' => '01012345678',
        'password' => bcrypt('secret123'),
    ]);

    $response = $this->postJson(apiUrl('/auth/customer/login'), [
        'mobile' => '01012345678',
        'password' => 'secret123',
    ]);

    $response->assertOk()->assertJsonStructure(['customer' => ['id', 'mobile'], 'token']);

    $token = $response->json('token');

    $me = $this->withHeader('Authorization', "Bearer {$token}")
        ->getJson(apiUrl('/me'));

    $me->assertOk()->assertJsonPath('data.id', $customer->id);
});

test('customer login fails with wrong password', function () {
    Customer::factory()->create([
        'mobile' => '01012345678',
        'password' => bcrypt('secret123'),
    ]);

    $response = $this->postJson(apiUrl('/auth/customer/login'), [
        'mobile' => '01012345678',
        'password' => 'wrong-password',
    ]);

    $response->assertUnprocessable()->assertJsonValidationErrors('mobile');
});

test('staff can log in with email and password', function () {
    $staff = staffWithRole('cashier');
    $staff->update(['password' => bcrypt('secret123')]);

    $response = $this->postJson(apiUrl('/auth/staff/login'), [
        'email' => $staff->email,
        'password' => 'secret123',
    ]);

    $response->assertOk()->assertJsonStructure(['staff' => ['id', 'role'], 'token']);
});

test('requests without a token are unauthenticated', function () {
    $response = $this->getJson(apiUrl('/me'));

    $response->assertUnauthorized();
});

test('an authenticated user can log out and the token is revoked', function () {
    $customer = Customer::factory()->create(['password' => bcrypt('secret123')]);
    $token = $customer->createToken('test')->plainTextToken;

    $logout = $this->withHeader('Authorization', "Bearer {$token}")
        ->postJson(apiUrl('/auth/logout'));

    $logout->assertOk();
    $this->assertDatabaseCount('personal_access_tokens', 0);

    // The auth guard caches its resolved user for the lifetime of the test's
    // booted application; without this, the second request below would
    // reuse that cached resolution instead of re-checking the (now
    // deleted) token against the database.
    auth()->forgetGuards();

    $after = $this->withHeader('Authorization', "Bearer {$token}")
        ->getJson(apiUrl('/me'));

    $after->assertUnauthorized();
});

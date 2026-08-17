<?php

use Modules\Staff\Models\Staff;

function adminUrl(string $path): string
{
    return '/admin'.$path;
}

test('login screen can be rendered', function () {
    $response = $this->get(adminUrl('/login'));

    $response->assertStatus(200);
});

test('staff can authenticate using the login screen', function () {
    $staff = Staff::factory()->create();

    $response = $this->post(adminUrl('/login'), [
        'email' => $staff->email,
        'password' => 'password',
    ]);

    $this->assertAuthenticatedAs($staff, 'staff');
    $response->assertRedirect(route('dashboard', absolute: false));
});

test('staff can not authenticate with invalid password', function () {
    $staff = Staff::factory()->create();

    $this->post(adminUrl('/login'), [
        'email' => $staff->email,
        'password' => 'wrong-password',
    ]);

    $this->assertGuest('staff');
});

test('staff can logout', function () {
    $staff = Staff::factory()->create();

    $response = $this->actingAs($staff, 'staff')->post(adminUrl('/logout'));

    $this->assertGuest('staff');
    $response->assertRedirect('/');
});

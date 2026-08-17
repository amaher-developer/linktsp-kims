<?php

function adminProfileUrl(string $path = '/admin/profile'): string
{
    return $path;
}

test('profile page is displayed', function () {
    $staff = managerStaff();

    $response = $this
        ->actingAs($staff, 'staff')
        ->get(adminProfileUrl());

    $response->assertOk();
});

test('profile information can be updated', function () {
    $staff = managerStaff();

    $response = $this
        ->actingAs($staff, 'staff')
        ->patch(adminProfileUrl(), [
            'name' => 'Test Staff',
            'email' => 'test-staff@example.com',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/admin/profile');

    $staff->refresh();

    $this->assertSame('Test Staff', $staff->name);
    $this->assertSame('test-staff@example.com', $staff->email);
    $this->assertNull($staff->email_verified_at);
});

test('email verification status is unchanged when the email address is unchanged', function () {
    $staff = managerStaff();

    $response = $this
        ->actingAs($staff, 'staff')
        ->patch(adminProfileUrl(), [
            'name' => 'Test Staff',
            'email' => $staff->email,
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/admin/profile');

    $this->assertNotNull($staff->refresh()->email_verified_at);
});

test('staff can delete their account', function () {
    $staff = managerStaff();

    $response = $this
        ->actingAs($staff, 'staff')
        ->delete(adminProfileUrl(), [
            'password' => 'password',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/');

    $this->assertGuest('staff');
    $this->assertNull($staff->fresh());
});

test('correct password must be provided to delete account', function () {
    $staff = managerStaff();

    $response = $this
        ->actingAs($staff, 'staff')
        ->from('/admin/profile')
        ->delete(adminProfileUrl(), [
            'password' => 'wrong-password',
        ]);

    $response
        ->assertSessionHasErrorsIn('userDeletion', 'password')
        ->assertRedirect('/admin/profile');

    $this->assertNotNull($staff->fresh());
});

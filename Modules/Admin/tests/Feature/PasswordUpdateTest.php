<?php

use Illuminate\Support\Facades\Hash;

test('password can be updated', function () {
    $staff = managerStaff();

    $response = $this
        ->actingAs($staff, 'staff')
        ->from('/admin/profile')
        ->put('/admin/password', [
            'current_password' => 'password',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/admin/profile');

    $this->assertTrue(Hash::check('new-password', $staff->refresh()->password));
});

test('correct password must be provided to update password', function () {
    $staff = managerStaff();

    $response = $this
        ->actingAs($staff, 'staff')
        ->from('/admin/profile')
        ->put('/admin/password', [
            'current_password' => 'wrong-password',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

    $response
        ->assertSessionHasErrorsIn('updatePassword', 'current_password')
        ->assertRedirect('/admin/profile');
});

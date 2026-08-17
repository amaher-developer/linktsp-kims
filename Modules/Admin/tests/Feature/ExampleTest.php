<?php

it('redirects the app root to the dashboard', function () {
    $response = $this->get('/');

    $response->assertRedirect(route('dashboard', absolute: false));
});

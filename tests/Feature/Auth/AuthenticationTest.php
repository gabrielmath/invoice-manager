<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);


it('should users can authenticate', function () {
    $user = User::factory()->create();

    $response = $this->post('/api/auth/login', [
        'email'    => $user->email,
        'password' => 'password'
    ]);

    $this->assertAuthenticated();

    $response->assertOk();
});

it('users should not be able to authenticate with invalid password', function () {
    $user = User::factory()->create();

    $response = $this->post('/api/auth/login', [
        'email'    => $user->email,
        'password' => 'wrong-password'
    ]);

    $this->assertGuest();

    $response->assertUnauthorized();
});

it('users should not be able to authenticate without the email', function () {
    $this->postJson('/api/auth/login', [
        'email'    => '',
        'password' => 'password'
    ])
        ->assertInvalid(['email' => 'The email field is required.'])
        ->assertUnprocessable();
});

it('users should not be able to authenticate without the password', function () {
    $user = User::factory()->create();

    $this->postJson('/api/auth/login', [
        'email'    => $user->email,
        'password' => ''
    ])
        ->assertInvalid(['password' => 'The password field is required.'])
        ->assertUnprocessable();
});

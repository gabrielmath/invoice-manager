<?php

use App\Models\User;
use Symfony\Component\HttpFoundation\Response;


it('should create a user', function () {
    $message = ['message' => 'User created successfully!'];
    $newUser = [
        'name'             => 'Usuário Teste',
        'email'            => 'user@test.com',
        'password'         => '12345678',
        'confirm_password' => '12345678',
    ];

    $response = $this->postJson('/api/auth/register', $newUser);

    $response->assertStatus(Response::HTTP_CREATED)->assertJson($message);
});

it('should not create a duplicate user', function () {
    $newUser = [
        'name'     => 'Usuário Teste',
        'email'    => 'user@test.com',
        'password' => '12345678',
    ];

    User::create($newUser);

    $newUser['confirm_password'] = '12345678';

    $this
        ->postJson('/api/auth/register', $newUser)
        ->assertInvalid(['email' => 'The email has already been taken.'])
        ->assertUnprocessable();
});

it('should not create a user without a name', function () {
    $newUser = [
        'name'             => '',
        'email'            => 'user@test.com',
        'password'         => '12345678',
        'confirm_password' => '12345678',
    ];

    $this
        ->postJson('/api/auth/register', $newUser)
        ->assertInvalid(['name' => 'The name field is required.'])
        ->assertUnprocessable();
});

it('should not create a user without a email', function () {
    $newUser = [
        'name'             => 'Usuário Teste',
        'email'            => '',
        'password'         => '12345678',
        'confirm_password' => '12345678',
    ];

    $this
        ->postJson('/api/auth/register', $newUser)
        ->assertInvalid(['email' => 'The email field is required.'])
        ->assertUnprocessable();
});

it('should not create a user with invalid email', function () {
    $newUser = [
        'name'             => 'Usuário Teste',
        'email'            => 'test',
        'password'         => '12345678',
        'confirm_password' => '12345678',
    ];

    $this
        ->postJson('/api/auth/register', $newUser)
        ->assertInvalid(['email' => 'The email field must be a valid email address.'])
        ->assertUnprocessable();
});

it('should not create a user with a password shorter than 8 digits', function () {
    $newUser = [
        'name'             => 'Usuário Teste',
        'email'            => 'test@email.com',
        'password'         => '123456',
        'confirm_password' => '123456',
    ];

    $this
        ->postJson('/api/auth/register', $newUser)
        ->assertInvalid(['password' => 'The password field must be at least 8 characters.'])
        ->assertUnprocessable();
});

it('should not create a user who failed to confirm the password', function () {
    $newUser = [
        'name'             => 'Usuário Teste',
        'email'            => 'test@email.com',
        'password'         => '12345678',
        'confirm_password' => '123456',
    ];

    $this
        ->postJson('/api/auth/register', $newUser)
        ->assertInvalid(['confirm_password' => 'The confirm password field must match password.'])
        ->assertUnprocessable();
});

it('should not create a user without a password', function () {
    $newUser = [
        'name'             => 'Test User',
        'email'            => 'user@test.com',
        'password'         => '',
        'confirm_password' => '',
    ];

    $this
        ->postJson('/api/auth/register', $newUser)
        ->assertInvalid([
            'password'         => 'The password field is required.',
            'confirm_password' => 'The confirm password field is required.',
        ])
        ->assertUnprocessable();
});

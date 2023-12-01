<?php

use App\Models\Invoice;
use App\Models\User;

it('should return a invoice list of user', function () {
    $user = User::factory()->create();
    Invoice::factory(10)->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)
        ->getJson(route('invoices.index'));

    $response
        ->assertJsonCount(10, 'data')
        ->assertSuccessful();
});

it('should not list with logged out user', function () {
    $user = User::factory()->create();
    Invoice::factory(10)->create(['user_id' => $user->id]);

    $this
        ->getJson(route('invoices.index'))
        ->assertJson(['message' => 'Unauthenticated.'])
        ->assertUnauthorized();
});

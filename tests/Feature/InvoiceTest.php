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

it('should view a invoice of user', function () {
    $user = User::factory()->create();
    Invoice::factory(10)->create(['user_id' => $user->id]);

    /** @var Invoice $invoice */
    $invoice = Invoice::first();

    $response = $this->actingAs($user)
        ->getJson(route('invoices.show', ['invoice' => $invoice->id]));

    $response
        ->assertJson([
            'invoice' => [
                'id'                 => $invoice->id,
                'numero'             => $invoice->number,
                'data_emissao'       => $invoice->issue_date->format('d/m/Y'),
                'valor'              => $invoice->money_value,
                'cnpj_remetente'     => $invoice->sender_doc,
                'nome_remetente'     => $invoice->sender_name,
                'cnpj_transportador' => $invoice->transporter_doc,
                'nome_transportador' => $invoice->transporter_name,
            ]
        ])
        ->assertSuccessful();
});

it('should can not found if invoice not exists', function () {
    $user = User::factory()->create();

    Invoice::factory(2)->create(['user_id' => $user->id]);

    /** @var Invoice $invoice */
    $invoice = Invoice::first();
    $invoice->delete();

    $this
        ->actingAs($user)
        ->getJson(route('invoices.show', ['invoice' => 1]))
        ->assertNotFound();
});

it('should can not view a invoice of other user', function () {
    $user = User::factory()->create();
    $user2 = User::factory()->create();

    Invoice::factory(2)->create(['user_id' => $user->id]);
    Invoice::factory(2)->create(['user_id' => $user2->id]);

    /** @var Invoice $invoice */
    $invoice = Invoice::first();

    $this
        ->actingAs($user2)
        ->getJson(route('invoices.show', ['invoice' => $invoice->id]))
        ->assertForbidden();
});

it('should can not view a invoice with logged out user', function () {
    $user = User::factory()->create();

    Invoice::factory(2)->create(['user_id' => $user->id]);

    /** @var Invoice $invoice */
    $invoice = Invoice::first();

    $this
        ->getJson(route('invoices.show', ['invoice' => $invoice->id]))
        ->assertJson(['message' => 'Unauthenticated.'])
        ->assertUnauthorized();
});

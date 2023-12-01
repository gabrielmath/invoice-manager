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

it('should create a new invoice', function () {
    $user = User::factory()->create();

    $data = [
        'valor'              => 70.00,
        'cnpj_remetente'     => '32.650.223/0001-90',
        'nome_remetente'     => 'Remetente Teste',
        'cnpj_transportador' => '41.659.336/0001-48',
        'nome_transportador' => 'Transportador Teste',
    ];

    $response = $this
        ->actingAs($user)
        ->postJson(route('invoices.store'), $data);

    $invoice = Invoice::first();

    $response->assertJson([
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

it('should not be able to create an invoice for another user', function () {
    $user = User::factory()->create();
    $user2 = User::factory()->create();

    $data = [
        'user_id'            => $user2->id,
        'valor'              => 70.00,
        'cnpj_remetente'     => '32.650.223/0001-90',
        'nome_remetente'     => 'Remetente Teste',
        'cnpj_transportador' => '41.659.336/0001-48',
        'nome_transportador' => 'Transportador Teste',
    ];

    $this
        ->actingAs($user)
        ->postJson(route('invoices.store'), $data);

    /** @var Invoice $invoice */
    $invoice = Invoice::first();

    $this->assertNotEquals($invoice->user_id, $user2->id);
    $this->assertEquals($invoice->user_id, $user->id);
});

it('should not create a new invoice with empty data', function () {
    $user = User::factory()->create();

    $data = [
        'valor'              => '',
        'cnpj_remetente'     => '',
        'nome_remetente'     => '',
        'cnpj_transportador' => '',
        'nome_transportador' => '',
    ];

    $this
        ->actingAs($user)
        ->postJson(route('invoices.store'), $data)
        ->assertUnprocessable();
});

it('should can not create a new invoice with invalid document', function () {
    $user = User::factory()->create();

    $data = [
        'valor'              => 70.00,
        'cnpj_remetente'     => '32.650.223/0001-9',
        'nome_remetente'     => 'Remetente Teste',
        'cnpj_transportador' => '41.659.336/0001-4',
        'nome_transportador' => 'Transportador Teste',
    ];

    $this
        ->actingAs($user)
        ->postJson(route('invoices.store'), $data)
        ->assertInvalid([
            'sender_document'      => 'The field sender document is not valid.',
            'transporter_document' => 'The field transporter document is not valid.',
        ])
        ->assertUnprocessable();
});

it('should can not create a new invoice with logged out user', function () {
    $data = [
        'valor'              => 70.00,
        'cnpj_remetente'     => '32.650.223/0001-90',
        'nome_remetente'     => 'Remetente Teste',
        'cnpj_transportador' => '41.659.336/0001-48',
        'nome_transportador' => 'Transportador Teste',
    ];

    $this
        ->postJson(route('invoices.store'), $data)
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

it('should can delete a invoice of user', function () {
    $user = User::factory()->create();

    Invoice::factory(2)->create(['user_id' => $user->id]);

    /** @var Invoice $invoice */
    $invoice = Invoice::first();

    $this
        ->actingAs($user)
        ->getJson(route('invoices.destroy', ['invoice' => $invoice->id]))
        ->assertSuccessful();
});

it('should can not delete if invoice not exists and show not found status', function () {
    $user = User::factory()->create();

    Invoice::factory(2)->create(['user_id' => $user->id]);

    /** @var Invoice $invoice */
    $invoice = Invoice::first();
    $invoice->delete();

    $this
        ->actingAs($user)
        ->getJson(route('invoices.destroy', ['invoice' => 1]))
        ->assertNotFound();
});

it('should can not delete a invoice of other user', function () {
    $user = User::factory()->create();
    $user2 = User::factory()->create();

    Invoice::factory(2)->create(['user_id' => $user->id]);
    Invoice::factory(2)->create(['user_id' => $user2->id]);

    /** @var Invoice $invoice */
    $invoice = Invoice::first();

    $this
        ->actingAs($user2)
        ->getJson(route('invoices.destroy', ['invoice' => $invoice->id]))
        ->assertForbidden();
});

it('should can not delete a invoice with logged out user', function () {
    $user = User::factory()->create();

    Invoice::factory(2)->create(['user_id' => $user->id]);

    /** @var Invoice $invoice */
    $invoice = Invoice::first();

    $this
        ->getJson(route('invoices.destroy', ['invoice' => $invoice->id]))
        ->assertJson(['message' => 'Unauthenticated.'])
        ->assertUnauthorized();
});

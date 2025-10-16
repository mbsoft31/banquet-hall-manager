<?php

use Mbsoft\BanquetHallManager\Models\Invoice;
use Mbsoft\BanquetHallManager\Models\Event;
use Illuminate\Foundation\Auth\User;

beforeEach(function () {
    $this->withTenant();
    $this->user = $this->createAuthenticatedUser();
});

it('can list invoices with pagination', function () {
    Invoice::factory()->count(5)->create();

    $response = $this->actingAs($this->user)
        ->getJson('/api/bhm/invoices');

    $response->assertOk();
    
    expect($response->json())
        ->toBeValidPaginatedResponse()
        ->and($response->json('data'))->toHaveCount(5);
});

it('can create a new invoice', function () {
    $event = Event::factory()->create();
    
    $invoiceData = [
        'event_id' => $event->id,
        'invoice_number' => 'INV-2024-001',
        'issue_date' => now()->format('Y-m-d'),
        'due_date' => now()->addDays(30)->format('Y-m-d'),
        'subtotal' => 5000.00,
        'tax_amount' => 400.00,
        'total_amount' => 5400.00,
        'status' => 'draft',
    ];

    $response = $this->actingAs($this->user)
        ->postJson('/api/bhm/invoices', $invoiceData);

    $response->assertCreated()
        ->assertJson([
            'data' => [
                'invoice_number' => 'INV-2024-001',
                'status' => 'draft',
                'total_amount' => 5400.00,
            ]
        ]);

    $this->assertDatabaseHas('bhm_invoices', [
        'invoice_number' => 'INV-2024-001',
        'event_id' => $event->id,
    ]);
});

it('can send an invoice', function () {
    $invoice = Invoice::factory()->create(['status' => 'draft']);

    $response = $this->actingAs($this->user)
        ->patchJson("/api/bhm/invoices/{$invoice->id}/send");

    $response->assertOk();
    
    $invoice->refresh();
    expect($invoice->status)->toBe('sent');
});

it('can mark invoice as paid', function () {
    $invoice = Invoice::factory()->create(['status' => 'sent']);

    $response = $this->actingAs($this->user)
        ->patchJson("/api/bhm/invoices/{$invoice->id}/mark-paid");

    $response->assertOk();
    
    $invoice->refresh();
    expect($invoice->status)->toBe('paid');
});

it('can filter invoices by status', function () {
    Invoice::factory()->create(['status' => 'paid']);
    Invoice::factory()->create(['status' => 'pending']);
    Invoice::factory()->create(['status' => 'overdue']);

    $response = $this->actingAs($this->user)
        ->getJson('/api/bhm/invoices?status=paid');

    $response->assertOk();
    
    expect($response->json('data'))
        ->toHaveCount(1)
        ->and($response->json('data.0.status'))->toBe('paid');
});

it('validates required fields when creating invoice', function () {
    $response = $this->actingAs($this->user)
        ->postJson('/api/bhm/invoices', []);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors([
            'event_id', 
            'issue_date', 
            'due_date', 
            'subtotal', 
            'total_amount'
        ]);
});
<?php

use Mbsoft\BanquetHallManager\Models\Payment;
use Mbsoft\BanquetHallManager\Models\Invoice;
use Illuminate\Foundation\Auth\User;

beforeEach(function () {
    $this->withTenant();
    $this->user = $this->createAuthenticatedUser();
});

it('can list payments', function () {
    Payment::factory()->count(3)->create();

    $response = $this->actingAs($this->user)
        ->getJson('/api/bhm/payments');

    $response->assertOk();
    
    expect($response->json('data'))->toHaveCount(3);
});

it('can create a new payment', function () {
    $invoice = Invoice::factory()->create([
        'total_amount' => 2500.00,
        'status' => 'sent'
    ]);
    
    $paymentData = [
        'invoice_id' => $invoice->id,
        'amount' => 2500.00,
        'payment_method' => 'credit_card',
        'payment_date' => now()->format('Y-m-d'),
        'status' => 'completed',
        'transaction_id' => 'txn_123456789',
        'notes' => 'Full payment received',
    ];

    $response = $this->actingAs($this->user)
        ->postJson('/api/bhm/payments', $paymentData);

    $response->assertCreated()
        ->assertJson([
            'data' => [
                'amount' => 2500.00,
                'payment_method' => 'credit_card',
                'status' => 'completed',
            ]
        ]);

    $this->assertDatabaseHas('bhm_payments', [
        'invoice_id' => $invoice->id,
        'transaction_id' => 'txn_123456789',
    ]);
});

it('automatically updates invoice status when fully paid', function () {
    $invoice = Invoice::factory()->create([
        'total_amount' => 1000.00,
        'status' => 'sent'
    ]);
    
    $paymentData = [
        'invoice_id' => $invoice->id,
        'amount' => 1000.00,
        'payment_method' => 'bank_transfer',
        'payment_date' => now()->format('Y-m-d'),
        'status' => 'completed',
    ];

    $response = $this->actingAs($this->user)
        ->postJson('/api/bhm/payments', $paymentData);

    $response->assertCreated();
    
    // Check if invoice status was updated
    $invoice->refresh();
    expect($invoice->status)->toBe('paid');
});

it('handles partial payments correctly', function () {
    $invoice = Invoice::factory()->create([
        'total_amount' => 2000.00,
        'status' => 'sent'
    ]);
    
    // First partial payment
    $this->actingAs($this->user)
        ->postJson('/api/bhm/payments', [
            'invoice_id' => $invoice->id,
            'amount' => 1200.00,
            'payment_method' => 'credit_card',
            'payment_date' => now()->format('Y-m-d'),
            'status' => 'completed',
        ]);
    
    $invoice->refresh();
    expect($invoice->status)->toBe('partial'); // Assuming you have partial status
    
    // Second payment to complete
    $this->actingAs($this->user)
        ->postJson('/api/bhm/payments', [
            'invoice_id' => $invoice->id,
            'amount' => 800.00,
            'payment_method' => 'credit_card',
            'payment_date' => now()->format('Y-m-d'),
            'status' => 'completed',
        ]);
    
    $invoice->refresh();
    expect($invoice->status)->toBe('paid');
});

it('validates payment amount does not exceed invoice total', function () {
    $invoice = Invoice::factory()->create([
        'total_amount' => 1000.00
    ]);
    
    $paymentData = [
        'invoice_id' => $invoice->id,
        'amount' => 1500.00, // Exceeds invoice total
        'payment_method' => 'credit_card',
        'payment_date' => now()->format('Y-m-d'),
        'status' => 'completed',
    ];

    $response = $this->actingAs($this->user)
        ->postJson('/api/bhm/payments', $paymentData);

    $response->assertUnprocessable();
});
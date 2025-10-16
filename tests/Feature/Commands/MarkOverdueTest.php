<?php

use Mbsoft\BanquetHallManager\Models\Invoice;
use Mbsoft\BanquetHallManager\Commands\MarkOverdue;
use Illuminate\Support\Facades\Artisan;

beforeEach(function () {
    $this->withTenant();
});

it('marks overdue invoices', function () {
    // Create invoices with different due dates
    $overdueInvoice = Invoice::factory()->create([
        'status' => 'pending',
        'due_date' => now()->subDays(5),
    ]);
    
    $currentInvoice = Invoice::factory()->create([
        'status' => 'pending',
        'due_date' => now()->addDays(5),
    ]);

    Artisan::call('bhm:mark-overdue');

    $overdueInvoice->refresh();
    $currentInvoice->refresh();

    expect($overdueInvoice->status)->toBe('overdue')
        ->and($currentInvoice->status)->toBe('pending');
});

it('does not mark already paid invoices as overdue', function () {
    $paidInvoice = Invoice::factory()->create([
        'status' => 'paid',
        'due_date' => now()->subDays(10),
    ]);

    Artisan::call('bhm:mark-overdue');

    $paidInvoice->refresh();
    expect($paidInvoice->status)->toBe('paid');
});

it('outputs correct information when run', function () {
    Invoice::factory()->count(3)->create([
        'status' => 'pending',
        'due_date' => now()->subDays(2),
    ]);

    Artisan::call('bhm:mark-overdue');
    
    $output = Artisan::output();
    expect($output)->toContain('3 invoices marked as overdue');
});
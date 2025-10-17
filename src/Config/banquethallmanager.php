<?php

return [
    // Basic Configuration
    'default_hall_capacity' => 100,
    'booking_deadline_hours' => 24,
    'payment_due_days' => 30,
    'tax_rate' => 0.18,
    'currencies' => ['DZD', 'USD', 'EUR'],
    'payment_methods' => ['cash', 'credit_card', 'bank_transfer', 'check', 'paypal'],
    'event_types' => ['wedding', 'conference', 'birthday', 'corporate'],
    'notification_channels' => ['mail'],
    'invoice_prefix' => 'BHM',
    
    // Multi-tenancy Configuration
    'multi_tenancy' => true,
    'enforce_tenant_header' => true,
    'default_tenant_id' => env('BHM_DEFAULT_TENANT_ID', 1),
    'current_tenant_id' => env('BHM_CURRENT_TENANT_ID', 1),
    'tenant_model' => \Illuminate\Foundation\Auth\User::class,
    'enable_tenant_scoping' => env('BHM_ENABLE_TENANT_SCOPING', true),
    
    // Pagination
    'default_per_page' => 15,
    
    // Currency
    'currency' => env('BHM_CURRENCY', 'USD'),
    'currency_symbol' => env('BHM_CURRENCY_SYMBOL', '$'),

    // Role enforcement is always enabled; mapping below is used when Spatie roles/permissions are not available
    'roles' => [
        'read' => ['viewer', 'staff', 'manager', 'admin'],
        'write' => ['staff', 'manager', 'admin'],
        'delete' => ['manager', 'admin'],
    ],
    
    // Spatie permission names (if package installed)
    'permissions' => [
        'read' => 'bhm.read',
        'write' => 'bhm.write',
        'delete' => 'bhm.delete',
    ],
];

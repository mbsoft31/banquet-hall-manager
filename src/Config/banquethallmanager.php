<?php

return [
    'default_hall_capacity' => 100,
    'booking_deadline_hours' => 24,
    'payment_due_days' => 30,
    'tax_rate' => 0.18,
    'currencies' => ['DZD', 'USD', 'EUR'],
    'payment_methods' => ['cash', 'credit_card', 'bank_transfer', 'check', 'paypal'],
    'event_types' => ['wedding', 'conference', 'birthday', 'corporate'],
    'notification_channels' => ['mail'],
    'invoice_prefix' => 'BHM',
    'multi_tenancy' => true,

    // Enforce that every request has a tenant context; if a user has tenant_id, it overrides header
    'enforce_tenant_header' => true,

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

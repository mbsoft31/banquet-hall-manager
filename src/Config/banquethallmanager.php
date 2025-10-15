<?php

return [
    'default_hall_capacity' => 100,
    'booking_deadline_hours' => 24,
    'payment_due_days' => 30,
    'tax_rate' => 0.18,
    'currencies' => ['DZD', 'USD', 'EUR'],
    'payment_methods' => ['cash', 'card', 'bank_transfer'],
    'event_types' => ['wedding', 'conference', 'birthday', 'corporate'],
    'notification_channels' => ['mail'],
    'invoice_prefix' => 'BHM',
    'multi_tenancy' => true,
];


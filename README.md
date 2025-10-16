# Banquet Hall Manager

[![Latest Version on Packagist](https://img.shields.io/packagist/v/mbsoft/banquet-hall-manager.svg?style=flat-square)](https://packagist.org/packages/mbsoft/banquet-hall-manager)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/mbsoft31/banquet-hall-manager/tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/mbsoft31/banquet-hall-manager/actions?query=workflow%3Atests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/mbsoft31/banquet-hall-manager/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/mbsoft31/banquet-hall-manager/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/mbsoft/banquet-hall-manager.svg?style=flat-square)](https://packagist.org/packages/mbsoft/banquet-hall-manager)

A comprehensive Laravel package for managing banquet halls, events, bookings, invoices, payments, staff, and analytics. Perfect for event venues, wedding halls, conference centers, and multi-purpose event spaces.

## Features

- **Multi-tenant Architecture** - Support multiple venues/organizations
- **Event Management** - Complete event lifecycle management
- **Hall Management** - Manage multiple halls with capacity and pricing
- **Client Management** - Track clients and their event history
- **Booking System** - Service bookings with pricing and inventory
- **Invoice & Payment Processing** - Complete financial management
- **Staff Management** - Assign staff to events with roles
- **Analytics & Reporting** - Revenue tracking and business insights
- **Authorization** - Policy-based access control
- **REST API** - Complete API for frontend integration
- **Automated Tasks** - Scheduled commands for business automation

## Requirements

- PHP 8.2 or higher
- Laravel 12.0 or higher
- Database: MySQL, PostgreSQL, SQLite, or SQL Server

## Installation

You can install the package via Composer:

```bash
composer require mbsoft/banquet-hall-manager
```

Publish the configuration file:

```bash
php artisan vendor:publish --tag="banquet-hall-manager-config"
```

Run the migrations:

```bash
php artisan migrate
```

## Configuration

The package configuration file will be published to `config/banquethallmanager.php`. Key configuration options:

```php
return [
    // Default tenant ID for single-tenant setups
    'default_tenant_id' => env('BHM_DEFAULT_TENANT_ID', 1),
    
    // Tenant model for multi-tenant setups
    'tenant_model' => \App\Models\User::class,
    
    // Enable/disable tenant scoping
    'enable_tenant_scoping' => env('BHM_ENABLE_TENANT_SCOPING', true),
    
    // Default pagination
    'default_per_page' => 15,
    
    // Currency settings
    'currency' => env('BHM_CURRENCY', 'USD'),
    'currency_symbol' => env('BHM_CURRENCY_SYMBOL', '$'),
];
```

## Usage

### Basic API Endpoints

The package provides a complete REST API:

```php
// Events
GET    /api/bhm/events
POST   /api/bhm/events
GET    /api/bhm/events/{event}
PUT    /api/bhm/events/{event}
DELETE /api/bhm/events/{event}
PATCH  /api/bhm/events/{event}/reschedule
PATCH  /api/bhm/events/{event}/cancel

// Halls
GET    /api/bhm/halls
POST   /api/bhm/halls
GET    /api/bhm/halls/{hall}
PUT    /api/bhm/halls/{hall}
DELETE /api/bhm/halls/{hall}

// Similar patterns for: clients, bookings, invoices, payments, staff, service-types

// Analytics
GET    /api/bhm/analytics/revenue
GET    /api/bhm/analytics/bookings-by-hall
GET    /api/bhm/analytics/monthly-trends
```

### Creating Events

```php
use Mbsoft\BanquetHallManager\Models\Event;
use Mbsoft\BanquetHallManager\Models\Hall;
use Mbsoft\BanquetHallManager\Models\Client;

$hall = Hall::create([
    'name' => 'Grand Ballroom',
    'capacity' => 300,
    'hourly_rate' => 200.00,
]);

$client = Client::create([
    'name' => 'John & Jane Smith',
    'email' => 'smith@example.com',
    'phone' => '+1234567890',
]);

$event = Event::create([
    'hall_id' => $hall->id,
    'client_id' => $client->id,
    'name' => 'Smith Wedding Reception',
    'type' => 'wedding',
    'start_at' => now()->addDays(30),
    'end_at' => now()->addDays(30)->addHours(6),
    'guest_count' => 250,
    'status' => 'confirmed',
    'total_amount' => 5000.00,
]);
```

### Working with Bookings

```php
use Mbsoft\BanquetHallManager\Models\Booking;
use Mbsoft\BanquetHallManager\Models\ServiceType;

$catering = ServiceType::create([
    'name' => 'Catering Service',
    'description' => 'Full meal service',
    'default_price' => 25.00,
]);

$booking = Booking::create([
    'event_id' => $event->id,
    'service_type_id' => $catering->id,
    'quantity' => 250,
    'unit_price' => 25.00,
    'total_price' => 6250.00,
]);
```

### Multi-Tenant Usage

The package supports multi-tenancy out of the box:

```php
// Set current tenant context
config(['banquethallmanager.current_tenant_id' => $tenantId]);

// All queries will be automatically scoped to the current tenant
$events = Event::all(); // Only returns events for current tenant
```

### Authorization

The package includes comprehensive authorization policies:

```php
// In your controller
$this->authorize('view', $event);
$this->authorize('create', Event::class);
$this->authorize('update', $event);
$this->authorize('delete', $event);

// Custom gates
Gate::allows('bhm.read');
Gate::allows('bhm.write');
Gate::allows('bhm.delete');
```

### Scheduled Commands

The package includes automated business logic:

```bash
# Mark overdue invoices (runs hourly)
php artisan bhm:mark-overdue

# Send payment reminders (runs daily at 9 AM)
php artisan bhm:send-reminders

# Seed initial roles and permissions
php artisan bhm:seed-roles

# Seed demo data for testing
php artisan bhm:seed-demo
```

## Testing

The package includes comprehensive test coverage:

```bash
# Run all tests
composer test

# Run tests with coverage
composer test-coverage

# Run performance profiling
composer test-profile
```

### Test Coverage

- ✅ **Unit Tests** - All models, policies, and business logic
- ✅ **Feature Tests** - Complete API endpoint coverage
- ✅ **Integration Tests** - Full workflow testing
- ✅ **Validation Tests** - Form request validation
- ✅ **Command Tests** - Scheduled task testing
- ✅ **Middleware Tests** - Tenant context and authorization

## Architecture

### Database Schema

The package creates the following tables:

- `bhm_halls` - Event venues/halls
- `bhm_clients` - Customer information
- `bhm_events` - Core event data
- `bhm_service_types` - Available services
- `bhm_bookings` - Service bookings per event
- `bhm_staff` - Staff member information
- `bhm_invoices` - Billing information
- `bhm_payments` - Payment transactions
- `bhm_event_staff` - Event staff assignments (pivot)

### Key Design Patterns

- **Repository Pattern** - Clean data access layer
- **Policy Pattern** - Authorization logic separation
- **Factory Pattern** - Comprehensive test data generation
- **Service Provider Pattern** - Laravel integration
- **Multi-Tenancy Pattern** - Scalable SaaS architecture

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Mohamed Benslimane](https://github.com/mbsoft31)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.
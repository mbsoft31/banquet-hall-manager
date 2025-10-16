<?php

namespace Mbsoft\BanquetHallManager\Database\seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Mbsoft\BanquetHallManager\Models\Hall;
use Mbsoft\BanquetHallManager\Models\Client;
use Mbsoft\BanquetHallManager\Models\ServiceType;
use Mbsoft\BanquetHallManager\Models\Staff;
use Mbsoft\BanquetHallManager\Models\Event;
use Mbsoft\BanquetHallManager\Models\Booking;
use Mbsoft\BanquetHallManager\Models\Invoice;
use Mbsoft\BanquetHallManager\Models\Payment;

class BhmDemoSeeder extends Seeder
{
    public function run(): void
    {
        $tenantId = (int) (config('banquethallmanager.demo_tenant_id', 1));

        DB::transaction(function () use ($tenantId) {
            $halls = [
                Hall::create([
                    'tenant_id' => $tenantId,
                    'name' => 'Grand Ballroom',
                    'capacity' => 300,
                    'location' => 'Main Building',
                    'description' => 'Elegant hall suitable for weddings and conferences.',
                    'hourly_rate' => 200,
                    'amenities' => ['stage', 'sound', 'lighting'],
                    'status' => 'available',
                ]),
                Hall::create([
                    'tenant_id' => $tenantId,
                    'name' => 'Garden Pavilion',
                    'capacity' => 120,
                    'location' => 'Courtyard',
                    'description' => 'Outdoor pavilion with garden views.',
                    'hourly_rate' => 120,
                    'amenities' => ['outdoor', 'heater'],
                    'status' => 'available',
                ]),
            ];

            $serviceCatering = ServiceType::create([
                'tenant_id' => $tenantId,
                'name' => 'Catering',
                'default_price' => 25,
                'unit' => 'guest',
                'is_active' => true,
            ]);
            $serviceDecoration = ServiceType::create([
                'tenant_id' => $tenantId,
                'name' => 'Decoration',
                'default_price' => 500,
                'unit' => 'package',
                'is_active' => true,
            ]);

            $client = Client::create([
                'tenant_id' => $tenantId,
                'name' => 'Acme Corp',
                'email' => 'events@acme.test',
                'phone' => '+10000000000',
                'notes' => 'VIP client',
            ]);

            $staffMgr = Staff::create([
                'tenant_id' => $tenantId,
                'name' => 'Jane Manager',
                'phone' => '+10000000001',
                'role' => 'manager',
                'is_active' => true,
            ]);
            $staffChef = Staff::create([
                'tenant_id' => $tenantId,
                'name' => 'John Chef',
                'phone' => '+10000000002',
                'role' => 'staff',
                'is_active' => true,
            ]);

            $start = Carbon::now()->addDays(2)->setTime(10, 0);
            $end = (clone $start)->addHours(6);

            $event = Event::create([
                'tenant_id' => $tenantId,
                'hall_id' => $halls[0]->id,
                'client_id' => $client->id,
                'name' => 'Acme Quarterly Meetup',
                'type' => 'corporate',
                'start_at' => $start,
                'end_at' => $end,
                'guest_count' => 80,
                'status' => 'scheduled',
                'special_requests' => ['projector' => true],
                'total_amount' => 0,
            ]);

            $event->staff()->attach([
                $staffMgr->id => ['tenant_id' => $tenantId],
                $staffChef->id => ['tenant_id' => $tenantId],
            ]);

            $booking1 = Booking::create([
                'tenant_id' => $tenantId,
                'event_id' => $event->id,
                'service_type_id' => $serviceCatering->id,
                'description' => 'Buffet lunch',
                'quantity' => 80,
                'unit_price' => 25,
                'total_price' => 80 * 25,
            ]);

            $booking2 = Booking::create([
                'tenant_id' => $tenantId,
                'event_id' => $event->id,
                'service_type_id' => $serviceDecoration->id,
                'description' => 'Corporate theme decor',
                'quantity' => 1,
                'unit_price' => 500,
                'total_price' => 500,
            ]);

            $subtotal = $booking1->total_price + $booking2->total_price + ($halls[0]->hourly_rate * $start->diffInHours($end));
            $taxRate = (float) config('banquethallmanager.tax_rate', 0.18);
            $tax = round($subtotal * $taxRate, 2);
            $total = $subtotal + $tax;

            $invoice = Invoice::create([
                'tenant_id' => $tenantId,
                'event_id' => $event->id,
                'client_id' => $client->id,
                'invoice_number' => 'BHM-DEMO-'.str_pad((string) $event->id, 5, '0', STR_PAD_LEFT),
                'subtotal' => $subtotal,
                'tax_amount' => $tax,
                'discount_amount' => 0,
                'total_amount' => $total,
                'due_date' => Carbon::now()->addDays(7)->toDateString(),
                'status' => 'unpaid',
            ]);

            // Demo cash payment with change
            $cashTendered = $total + 100; // customer pays a bit extra
            Payment::create([
                'tenant_id' => $tenantId,
                'invoice_id' => $invoice->id,
                'amount' => $total,
                'method' => 'cash',
                'reference' => 'CASH-'.uniqid(),
                'cash_tendered' => $cashTendered,
                'change_given' => $cashTendered - $total,
                'paid_at' => Carbon::now(),
                'status' => 'completed',
            ]);
        });
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('bhm_invoices', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('tenant_id')->index();
            $t->foreignId('event_id')->constrained('bhm_events')->cascadeOnDelete();
            $t->foreignId('client_id')->constrained('bhm_clients')->cascadeOnDelete();
            $t->string('invoice_number')->nullable()->index();
            $t->date('issue_date')->nullable();
            $t->decimal('subtotal', 12, 2)->default(0);
            $t->decimal('tax_amount', 12, 2)->default(0);
            $t->decimal('discount_amount', 12, 2)->default(0);
            $t->decimal('total_amount', 12, 2)->default(0);
            $t->date('due_date')->nullable();
            $t->string('status')->default('pending');
            $t->text('notes')->nullable();
            $t->timestamps();

            $t->index(['tenant_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bhm_invoices');
    }
};


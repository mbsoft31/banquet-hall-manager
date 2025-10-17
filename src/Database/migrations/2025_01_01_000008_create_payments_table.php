<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('bhm_payments', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('tenant_id')->index();
            $t->foreignId('invoice_id')->constrained('bhm_invoices')->cascadeOnDelete();
            $t->decimal('amount', 12, 2);
            $t->string('payment_method');
            $t->date('payment_date')->nullable();
            $t->string('transaction_id')->nullable()->index();
            $t->text('notes')->nullable();
            $t->string('status')->default('completed');
            $t->timestamps();

            $t->index(['tenant_id', 'invoice_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bhm_payments');
    }
};


<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('bhm_bookings', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('tenant_id')->index();
            $t->foreignId('event_id')->constrained('bhm_events')->cascadeOnDelete();
            $t->string('description');
            $t->unsignedInteger('quantity')->default(1);
            $t->decimal('unit_price', 12, 2)->default(0);
            $t->decimal('total_price', 12, 2)->default(0);
            $t->timestamps();

            $t->index(['tenant_id', 'event_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bhm_bookings');
    }
};


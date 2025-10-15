<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('bhm_events', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('tenant_id')->index();
            $t->foreignId('hall_id')->constrained('bhm_halls')->cascadeOnDelete();
            $t->foreignId('client_id')->constrained('bhm_clients')->cascadeOnDelete();
            $t->string('name');
            $t->string('type')->index();
            $t->dateTime('start_at')->index();
            $t->dateTime('end_at')->index();
            $t->unsignedInteger('guest_count')->default(0);
            $t->string('status')->default('draft');
            $t->json('special_requests')->nullable();
            $t->decimal('total_amount', 12, 2)->default(0);
            $t->timestamps();

            $t->index(['tenant_id', 'hall_id', 'start_at', 'end_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bhm_events');
    }
};


<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('bhm_staff', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('tenant_id')->index();
            $t->string('name');
            $t->string('email')->nullable();
            $t->string('phone')->nullable();
            $t->string('role')->nullable();
            $t->float('hourly_rate')->nullable();
            $t->boolean('is_active')->default(true);
            $t->timestamps();
        });

        Schema::create('bhm_event_staff', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('tenant_id')->index();
            $t->foreignId('event_id')->constrained('bhm_events')->cascadeOnDelete();
            $t->foreignId('staff_id')->constrained('bhm_staff')->cascadeOnDelete();
            $t->timestamps();
            $t->unique(['tenant_id','event_id','staff_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bhm_event_staff');
        Schema::dropIfExists('bhm_staff');
    }
};


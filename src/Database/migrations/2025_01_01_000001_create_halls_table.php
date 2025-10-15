<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('bhm_halls', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('tenant_id')->index();
            $t->string('name');
            $t->unsignedInteger('capacity')->default(config('banquethallmanager.default_hall_capacity'));
            $t->string('location')->nullable();
            $t->text('description')->nullable();
            $t->decimal('hourly_rate', 10, 2)->default(0);
            $t->json('amenities')->nullable();
            $t->string('status')->default('active');
            $t->timestamps();
            $t->unique(['tenant_id','name']);
        });
    }
    public function down(): void { Schema::dropIfExists('bhm_halls'); }
};


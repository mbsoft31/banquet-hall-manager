<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bhm_event_staff', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('event_id');
            $table->unsignedBigInteger('staff_id');
            $table->timestamps();
            
            $table->foreign('event_id')->references('id')->on('bhm_events')->onDelete('cascade');
            $table->foreign('staff_id')->references('id')->on('bhm_staff')->onDelete('cascade');
            
            $table->unique(['event_id', 'staff_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bhm_event_staff');
    }
};
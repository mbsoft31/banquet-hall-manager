<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('bhm_bookings', function (Blueprint $t) {
            $t->foreignId('service_type_id')->nullable()->after('event_id')->constrained('bhm_service_types')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('bhm_bookings', function (Blueprint $t) {
            $t->dropConstrainedForeignId('service_type_id');
        });
    }
};


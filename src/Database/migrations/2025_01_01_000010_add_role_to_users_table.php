<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('users')) {
            return;
        }

        Schema::table('users', function (Blueprint $t) {
            if (!Schema::hasColumn('users', 'tenant_id')) {
                $t->unsignedBigInteger('tenant_id')->nullable()->index();
            }

            if (!Schema::hasColumn('users', 'role')) {
                $t->string('role')->nullable()->index();
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('users')) {
            return;
        }

        Schema::table('users', function (Blueprint $t) {
            if (Schema::hasColumn('users', 'role')) {
                $t->dropColumn('role');
            }

            if (Schema::hasColumn('users', 'tenant_id')) {
                $t->dropColumn('tenant_id');
            }
        });
    }
};


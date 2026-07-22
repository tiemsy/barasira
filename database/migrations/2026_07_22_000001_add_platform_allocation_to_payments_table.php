<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->decimal('platform_fee', 12, 2)->default(0)->after('amount');
            $table->decimal('provider_amount', 12, 2)->default(0)->after('platform_fee');
        });

        DB::table('payments')->update([
            'platform_fee' => DB::raw('ROUND(amount * 0.10, 2)'),
            'provider_amount' => DB::raw('amount - ROUND(amount * 0.10, 2)'),
        ]);
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['platform_fee', 'provider_amount']);
        });
    }
};

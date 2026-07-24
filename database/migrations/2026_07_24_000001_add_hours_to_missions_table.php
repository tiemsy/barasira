<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('missions', function (Blueprint $table): void {
            $table->decimal('initial_hours', 6, 2)->default(1)->after('price');
            $table->decimal('billable_hours', 6, 2)->default(1)->after('initial_hours');
        });
    }

    public function down(): void
    {
        Schema::table('missions', function (Blueprint $table): void {
            $table->dropColumn(['initial_hours', 'billable_hours']);
        });
    }
};

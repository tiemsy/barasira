<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('applications', function (Blueprint $table): void {
            $table->string('pricing_type', 20)->default('global')->after('message');
            $table->decimal('hourly_rate', 10, 2)->nullable()->after('pricing_type');
        });
    }

    public function down(): void
    {
        Schema::table('applications', fn (Blueprint $table) => $table->dropColumn(['pricing_type', 'hourly_rate']));
    }
};

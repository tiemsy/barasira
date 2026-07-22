<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('partner_promotions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('partner_id')->constrained()->cascadeOnDelete();
            $table->decimal('paid_amount', 15, 2);
            $table->dateTime('starts_at')->index();
            $table->dateTime('ends_at')->index();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['starts_at', 'ends_at', 'paid_amount']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('partner_promotions');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('payments')->where('method', 'stripe')->update(['method' => 'carte']);
        DB::statement("ALTER TABLE payments MODIFY status ENUM('en_attente','effectue','echoue','annule','rembourse') NOT NULL DEFAULT 'en_attente'");
        DB::statement("ALTER TABLE payments MODIFY method ENUM('orange_money','moov_money','carte','paypal') NULL");
        Schema::table('payments', function (Blueprint $table) {
            $table->string('provider')->nullable()->after('method');
            $table->string('payment_url', 2048)->nullable()->after('transaction_id');
            $table->json('provider_data')->nullable()->after('payment_url');
            $table->timestamp('paid_at')->nullable()->after('provider_data');
            $table->unique(['provider', 'transaction_id']);
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropUnique(['provider', 'transaction_id']);
            $table->dropColumn(['provider', 'payment_url', 'provider_data', 'paid_at']);
        });
        DB::table('payments')->whereIn('status', ['echoue', 'annule'])->update(['status' => 'en_attente']);
        DB::table('payments')->whereIn('method', ['orange_money', 'moov_money'])->update(['method' => 'stripe']);
        DB::statement("ALTER TABLE payments MODIFY status ENUM('en_attente','effectue','rembourse') NOT NULL DEFAULT 'en_attente'");
        DB::statement("ALTER TABLE payments MODIFY method ENUM('carte','paypal','stripe') NULL");
    }
};

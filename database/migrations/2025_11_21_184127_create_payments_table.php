<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Paiements
return new class extends Migration {
    public function up(): void {
        Schema::create('payments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('mission_id')->nullable()->constrained('missions')->onDelete('set null');
            $table->foreignId('payer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('receiver_id')->nullable()->constrained('users')->onDelete('set null');
            $table->decimal('amount',10,2);
            $table->enum('status',['en_attente','effectue','rembourse'])->default('en_attente');
            $table->enum('method',['carte','paypal','stripe'])->nullable();
            $table->string('transaction_id')->nullable();
            $table->timestamps();
            $table->index(['payer_id','receiver_id']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('payments');
    }
};

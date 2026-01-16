<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Litiges
return new class extends Migration {
    public function up(): void {
        Schema::create('disputes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('mission_id')->nullable()->constrained('missions')->onDelete('set null');
            $table->foreignId('complainant_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('defendant_id')->constrained('users')->onDelete('cascade');
            $table->text('reason');
            $table->enum('status',['ouverte','en_cours','resolue','fermee'])->default('ouverte');
            $table->text('resolution')->nullable();
            $table->timestamps();
            $table->index(['mission_id','status']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('disputes');
    }
};

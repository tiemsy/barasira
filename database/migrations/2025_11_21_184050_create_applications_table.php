<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Candidatures
return new class () extends Migration {
    public function up(): void
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('mission_id')->constrained('missions')->onDelete('cascade');
            $table->foreignId('worker_id')->constrained('users')->onDelete('cascade');
            $table->text('message')->nullable();
            $table->decimal('proposed_price', 10, 2)->nullable();
            $table->enum('status', ['en_attente','acceptee','refusee'])->default('en_attente');
            $table->timestamps();
            $table->unique(['mission_id','worker_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};

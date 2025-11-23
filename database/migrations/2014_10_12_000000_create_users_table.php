<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('first_name', 100)->nullable();
            $table->string('last_name', 100)->nullable();
            $table->string('email', 150)->unique();
            $table->string('password');
            $table->string('phone', 30)->nullable();
            $table->enum('role', ['client','prestataire','admin'])->default('client');
            $table->text('bio')->nullable();
            $table->string('avatar_url')->nullable();
            $table->decimal('rating', 3, 2)->default(0.00);
            $table->boolean('verified')->default(false);
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
            $table->index('role');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};

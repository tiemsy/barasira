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
        Schema::create('services', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('category_id')
                ->constrained('service_categories') // référence la table service_categories
                ->onDelete('cascade');

            $table->string('name');          // nom du service
            $table->text('description');     // description générale
            $table->string('icon')->nullable(); // icône ou pictogramme optionnel
            $table->integer('price_min')->default(0); // prix minimum suggéré
            $table->integer('price_max')->default(0); // prix maximum suggéré
            $table->boolean('is_active')->default(true); // service actif ou non

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};

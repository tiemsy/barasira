<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Services
return new class() extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('user_id')
                ->constrained('users') // référence la table users
                ->onDelete('cascade');
            $table->foreignId('service_category_id')
                ->constrained('service_categories') // référence la table service_categories
                ->onDelete('cascade');

            $table->foreignId('city_id')
                ->constrained('cities') // référence la table cities
                ->onDelete('cascade');
            $table->foreignId('municipality_id')->nullable()
                ->constrained('municipalities') // référence la table municipalities
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
        Schema::table('services', function (Blueprint $table) {
            $table->dropForeign(['city_id']);
            $table->dropForeign(['municipality_id']);
            $table->dropColumn(['city_id', 'municipality_id']);
            $table->dropIfExists('services');
        });
    }
};

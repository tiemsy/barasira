<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_skills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained()
                ->onDelete('cascade');

            $table->foreignId('service_id')
                ->constrained()
                ->onDelete('cascade');

            $table->string('level')->nullable();                 // beginner, intermediate, expert
            $table->integer('years_experience')->default(0);     // années d’expérience
            $table->string('certificate')->nullable();           // nom du certificat
            $table->string('certificate_file')->nullable();      // fichier justificatif
            $table->text('description')->nullable();             // description libre
            $table->boolean('verified')->default(false);         // validé par l’équipe

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_skills');
    }
};

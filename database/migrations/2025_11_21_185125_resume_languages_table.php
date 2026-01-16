<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// langues parlées liées au CV
return new class extends Migration {
    public function up(): void {
        Schema::create('resume_languages', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('resume_id')->constrained('resumes')->onDelete('cascade');
            $table->string('language',100);
            $table->enum('level',['debutant','intermediaire','avance','natif'])->default('intermediaire');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('resume_languages');
    }
};

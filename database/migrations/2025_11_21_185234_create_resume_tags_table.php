<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('resume_tags', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('resume_id')->constrained('resumes')->onDelete('cascade');
            $table->string('tag',100);
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('resume_tags');
    }
};

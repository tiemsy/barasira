<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('user_skills', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('service_id')->constrained('services')->onDelete('cascade');
            $table->integer('experience_years')->unsigned()->default(0);
            $table->decimal('hourly_rate',8,2)->nullable();
            $table->timestamps();
            $table->unique(['user_id','service_id']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('user_skills');
    }
};

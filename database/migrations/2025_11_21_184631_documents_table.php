<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Documents
return new class extends Migration {
    public function up(): void {
        Schema::create('documents', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('document_type',['CNI','justificatif_domicile','autre'])->default('autre');
            $table->string('file_url');
            $table->enum('status',['en_attente','valide','rejete'])->default('en_attente');
            $table->timestamp('uploaded_at')->nullable();
            $table->timestamps();
            $table->index(['user_id','status']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('documents');
    }
};

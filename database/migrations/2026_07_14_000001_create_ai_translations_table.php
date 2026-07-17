<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('ai_translations', function (Blueprint $table) {
            $table->id();
            $table->morphs('translatable');
            $table->string('field', 100);
            $table->string('source_locale', 10);
            $table->string('target_locale', 10);
            $table->char('source_hash', 64);
            $table->longText('original_text');
            $table->longText('translated_text');
            $table->string('provider', 30)->nullable();
            $table->timestamps();
            $table->unique(['translatable_type','translatable_id','field','source_locale','target_locale','source_hash'], 'ai_translations_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_translations');
    }
};

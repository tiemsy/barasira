<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->unique(['mission_id', 'reviewer_id'], 'reviews_mission_reviewer_unique');
        });

        $indexes = collect(Schema::getIndexes('reviews'))->pluck('name');

        Schema::table('reviews', function (Blueprint $table) use ($indexes) {
            if ($indexes->contains('reviews_mission_id_rollback_index')) {
                $table->dropIndex('reviews_mission_id_rollback_index');
            }

            if ($indexes->contains('reviews_reviewer_id_rollback_index')) {
                $table->dropIndex('reviews_reviewer_id_rollback_index');
            }
        });
    }

    public function down(): void
    {
        // MySQL peut réutiliser l'index unique pour maintenir les clés étrangères.
        // Des index simples doivent donc exister avant la suppression de l'unique.
        Schema::table('reviews', function (Blueprint $table) {
            $table->index('mission_id', 'reviews_mission_id_rollback_index');
            $table->index('reviewer_id', 'reviews_reviewer_id_rollback_index');
        });

        Schema::table('reviews', function (Blueprint $table) {
            $table->dropUnique('reviews_mission_reviewer_unique');
        });
    }
};

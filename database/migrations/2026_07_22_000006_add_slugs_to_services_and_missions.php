<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table): void {
            $table->string('slug')->nullable()->unique()->after('name');
        });

        Schema::table('missions', function (Blueprint $table): void {
            $table->string('slug')->nullable()->unique()->after('title');
        });

        $this->backfill('services', 'name', 'service');
        $this->backfill('missions', 'title', 'mission');
    }

    public function down(): void
    {
        Schema::table('services', function (Blueprint $table): void {
            $table->dropUnique(['slug']);
            $table->dropColumn('slug');
        });

        Schema::table('missions', function (Blueprint $table): void {
            $table->dropUnique(['slug']);
            $table->dropColumn('slug');
        });
    }

    private function backfill(string $table, string $source, string $fallback): void
    {
        DB::table($table)->select(['id', $source])->orderBy('id')->each(function (object $row) use ($table, $source, $fallback): void {
            $base = Str::slug((string) $row->{$source}) ?: $fallback;
            if (ctype_digit($base)) {
                $base = "{$fallback}-{$base}";
            }
            $slug = $base;
            $suffix = 2;

            while (DB::table($table)->where('slug', $slug)->exists()) {
                $slug = "{$base}-{$suffix}";
                $suffix++;
            }

            DB::table($table)->where('id', $row->id)->update(['slug' => $slug]);
        });
    }
};

<?php

namespace Tests\Unit;

use App\Services\DatabaseBrowserService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class DatabaseBrowserServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config([
            'database.default' => 'browser_test',
            'database.connections.browser_test' => [
                'driver' => 'sqlite',
                'database' => ':memory:',
                'prefix' => '',
                'foreign_key_constraints' => true,
            ],
        ]);
        DB::setDefaultConnection('browser_test');
        DB::purge('browser_test');
    }

    public function test_it_lists_tables_and_masks_sensitive_values(): void
    {
        Schema::connection('browser_test')->create('accounts', function (Blueprint $table) {
            $table->id();
            $table->string('email');
            $table->string('password');
            $table->string('api_token')->nullable();
        });
        DB::connection('browser_test')->table('accounts')->insert([
            'email' => 'admin@barasira.test',
            'password' => 'secret-password',
            'api_token' => 'secret-token',
        ]);

        $browser = app(DatabaseBrowserService::class);
        $table = collect($browser->tables())->first(fn (string $name) => str_ends_with($name, 'accounts'));
        $this->assertNotNull($table);
        $this->assertSame(['id', 'email', 'password', 'api_token'], array_column($browser->columns($table), 'name'));

        $row = $browser->rows($table, 25)->items()[0];
        $this->assertSame('admin@barasira.test', $row['email']);
        $this->assertSame('[MASQUÉ]', $row['password']);
        $this->assertSame('[MASQUÉ]', $row['api_token']);
    }
}

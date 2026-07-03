<?php

namespace Tests\Feature;

use App\Models\Sku;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SkuImportTest extends TestCase
{
    use RefreshDatabase;

    public function test_import_creates_rows_from_json_fixture(): void
    {
        $path = storage_path('framework/testing/sku_import_test.json');
        file_put_contents($path, json_encode([
            ['brand' => 'TestBrand', 'sku' => 'TEST-SKU-1', 'variant' => 'Single'],
            ['brand' => 'TestBrand', 'sku' => 'TEST-SKU-2', 'variant' => 'Single'],
        ]));

        $this->artisan('sku:import', ['--path' => $path])
            ->assertExitCode(0);

        $this->assertDatabaseCount('skus', 2);
        $this->assertDatabaseHas('skus', ['sku' => 'TEST-SKU-1']);

        unlink($path);
    }

    public function test_import_refuses_to_run_twice(): void
    {
        Sku::create(['brand' => 'Existing', 'sku' => 'EXISTING-1']);

        $path = storage_path('framework/testing/sku_import_test2.json');
        file_put_contents($path, json_encode([
            ['brand' => 'TestBrand', 'sku' => 'TEST-SKU-1'],
        ]));

        $this->artisan('sku:import', ['--path' => $path])
            ->assertExitCode(1);

        $this->assertDatabaseCount('skus', 1);

        unlink($path);
    }
}

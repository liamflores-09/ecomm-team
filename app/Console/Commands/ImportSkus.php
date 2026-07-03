<?php

namespace App\Console\Commands;

use App\Models\Sku;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ImportSkus extends Command
{
    protected $signature = 'sku:import {--path=database/seeders/data/sku_import.json}';
    protected $description = 'One-time import of historical SKU rows from the JSON extract of the source workbook';

    public function handle(): int
    {
        if (Sku::count() > 0) {
            $this->error('skus table already has data. Aborting to avoid duplicate import.');
            return self::FAILURE;
        }

        $path = $this->option('path');
        if (!File::exists($path)) {
            $this->error("File not found: {$path}");
            return self::FAILURE;
        }

        $rows = json_decode(File::get($path), true);
        $now = now();

        foreach (array_chunk($rows, 200) as $chunk) {
            $insertable = array_map(function ($row) use ($now) {
                $row['ready_for_cvp'] = !empty($row['ready_for_cvp']) ? 1 : 0;
                $row['cvp_uploaded'] = !empty($row['cvp_uploaded']) ? 1 : 0;
                $row['created_at'] = $now;
                $row['updated_at'] = $now;
                return $row;
            }, $chunk);
            Sku::insert($insertable);
        }

        $this->info('Imported ' . count($rows) . ' SKU rows.');
        return self::SUCCESS;
    }
}

<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\BrandCatalog;
use Illuminate\Database\Seeder;

class BrandCatalogSeeder extends Seeder
{
    public function run(): void
    {
        BrandCatalog::truncate();

        $brands = Brand::pluck('id', 'name');

        $catalogs = [
            [
                'brand'  => 'SONY',
                'title'  => 'Alpha Series Q3 2026 New Arrivals',
                'notes'  => 'Full-frame mirrorless bodies and G Master lens lineup. Includes ZV-E10 II and FX3 updates.',
                'status' => 'available',
                'link'   => 'https://drive.google.com',
            ],
            [
                'brand'  => 'DJI',
                'title'  => 'DJI Air 3S & Mini 4 Pro Bundle Catalog',
                'notes'  => 'Drone kits with fly-more combos. ND filter sets and carrying case accessories included.',
                'status' => 'available',
                'link'   => 'https://drive.google.com',
            ],
            [
                'brand'  => 'GODOX',
                'title'  => 'Studio Flash & LED Lighting 2026',
                'notes'  => 'AD300Pro, SL150III, and V1 Pro speedlight. Softbox and modifier combos available.',
                'status' => 'available',
                'link'   => 'https://drive.google.com',
            ],
            [
                'brand'  => 'SAMSUNG',
                'title'  => 'Pro Endurance & T9 SSD Q3 Catalog',
                'notes'  => 'High-endurance memory cards for security cams and dashcams. New T9 portable SSD colors.',
                'status' => 'available',
                'link'   => 'https://drive.google.com',
            ],
            [
                'brand'  => 'LOGITECH',
                'title'  => 'Creator & Streaming Bundle 2026',
                'notes'  => 'MX Brio 4K webcam, Yeti GX mic, and MX Keys combo deals. New Litra Beam LX light included.',
                'status' => 'upcoming',
                'link'   => 'https://drive.google.com',
            ],
            [
                'brand'  => 'RODE',
                'title'  => 'Wireless PRO & VideoMic Range',
                'notes'  => 'Wireless PRO dual-channel kit, NTG5 shotgun, and COLORS edition accessories for content creators.',
                'status' => 'available',
                'link'   => 'https://drive.google.com',
            ],
            [
                'brand'  => 'SHURE',
                'title'  => 'MV & SM Series Microphone Catalog',
                'notes'  => 'MV7+, SM7dB, and MoveMic lavalier. Includes updated packaging and bundle configurations.',
                'status' => 'available',
                'link'   => 'https://drive.google.com',
            ],
            [
                'brand'  => 'NIKON',
                'title'  => 'Z Series Holiday Seasonal Deals',
                'notes'  => 'Z6III and Z8 bundle promotions. Limited seasonal kits with extra battery and strap.',
                'status' => 'seasonal',
                'link'   => 'https://drive.google.com',
            ],
            [
                'brand'  => 'SMALLRIG',
                'title'  => 'Camera Cage & Rig Accessories 2026',
                'notes'  => 'Sony A7C II cage, DJI RS4 mounting plates, and universal cold shoe accessories.',
                'status' => 'available',
                'link'   => 'https://drive.google.com',
            ],
            [
                'brand'  => 'BLACKMAGIC',
                'title'  => 'Cinema Camera 6K & ATEM Mini Lineup',
                'notes'  => 'BMPCC 6K G2 production kits and ATEM Mini Extreme ISO for live production setups.',
                'status' => 'upcoming',
                'link'   => 'https://drive.google.com',
            ],
            [
                'brand'  => 'UGREEN',
                'title'  => 'Nexode Charger & Hub Collection Q3',
                'notes'  => 'Nexode Pro 160W GaN chargers, Revodok Max docking stations, and USB4 cable lineup.',
                'status' => 'available',
                'link'   => 'https://drive.google.com',
            ],
            [
                'brand'  => 'ZHIYUN',
                'title'  => 'Crane-M4 & Smooth Gimbal Series',
                'notes'  => 'Crane-M4 for compact cameras, Smooth 5S for smartphones. Combo kit with fill light attachment.',
                'status' => 'upcoming',
                'link'   => 'https://drive.google.com',
            ],
            [
                'brand'  => 'CANON',
                'title'  => 'EOS R & RF Lens Catalog 2026',
                'notes'  => 'EOS R8 and R50 body kits. RF 24-105mm f/4L, RF 50mm f/1.8, and RF-S 10-18mm new additions.',
                'status' => 'seasonal',
                'link'   => 'https://drive.google.com',
            ],
        ];

        foreach ($catalogs as $data) {
            $brandId = $brands[$data['brand']] ?? null;
            if (!$brandId) continue;

            BrandCatalog::create([
                'brand_id' => $brandId,
                'title'    => $data['title'],
                'notes'    => $data['notes'],
                'status'   => $data['status'],
                'link'     => $data['link'],
            ]);
        }
    }
}

<?php

namespace Tests\Unit;

use App\Models\Sku;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SkuModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_pr_sla_is_null_without_both_dates(): void
    {
        $sku = Sku::create(['brand' => 'B', 'sku' => 'S1', 'pr_date_started' => '2026-01-05']);
        $this->assertNull($sku->pr_sla);
    }

    public function test_pr_sla_is_day_difference(): void
    {
        $sku = Sku::create([
            'brand' => 'B', 'sku' => 'S2',
            'pr_date_started' => '2026-01-05',
            'pr_date_completed' => '2026-01-09',
        ]);
        $this->assertSame(4, $sku->pr_sla);
    }

    public function test_pr_sla_is_one_when_same_day(): void
    {
        $sku = Sku::create([
            'brand' => 'B', 'sku' => 'S3',
            'pr_date_started' => '2026-01-05',
            'pr_date_completed' => '2026-01-05',
        ]);
        $this->assertSame(1, $sku->pr_sla);
    }

    public function test_content_sla_is_posted_minus_pr_completed(): void
    {
        $sku = Sku::create([
            'brand' => 'B', 'sku' => 'S4',
            'pr_date_completed' => '2026-01-09',
            'content_date_posted' => '2026-01-17',
        ]);
        $this->assertSame(8, $sku->content_sla);
    }

    public function test_content_status_progression(): void
    {
        $notStarted = Sku::create(['brand' => 'B', 'sku' => 'S5']);
        $this->assertSame('—', $notStarted->content_status);

        $pending = Sku::create(['brand' => 'B', 'sku' => 'S6', 'content_date_started' => '2026-01-10']);
        $this->assertSame('PENDING', $pending->content_status);

        $done = Sku::create([
            'brand' => 'B', 'sku' => 'S7',
            'content_date_started' => '2026-01-10',
            'content_date_posted' => '2026-01-17',
        ]);
        $this->assertSame('DONE', $done->content_status);
    }

    public function test_posted_reflects_content_date_posted(): void
    {
        $notPosted = Sku::create(['brand' => 'B', 'sku' => 'S8']);
        $this->assertFalse($notPosted->posted);

        $posted = Sku::create(['brand' => 'B', 'sku' => 'S9', 'content_date_posted' => '2026-01-17']);
        $this->assertTrue($posted->posted);
    }
}

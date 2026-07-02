<?php

namespace Tests\Unit;

use App\Support\TaskLabels;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskLabelsTest extends TestCase
{
    use RefreshDatabase;

    public function test_returns_empty_array_when_no_categories_exist_at_all(): void
    {
        $this->assertSame([], TaskLabels::get('content'));
        $this->assertSame([], TaskLabels::get('graphics'));
    }
}

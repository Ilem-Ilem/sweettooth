<?php

namespace Tests\Feature\Livewire\BranchDashboard\SalesDashboard;

use App\Livewire\BranchDashboard\SalesDashboard\StockMonitor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Livewire\Livewire;
use Tests\TestCase;

class StockMonitorTest extends TestCase
{
    public function test_renders_successfully()
    {
        Livewire::test(StockMonitor::class)
            ->assertStatus(200);
    }
}

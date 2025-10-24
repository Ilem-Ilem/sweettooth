<?php

namespace Tests\Feature\Livewire\BranchDashboard\Production\KitchenModule;

use App\Livewire\BranchDashboard\Production\KitchenModule\StockMonitor;
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

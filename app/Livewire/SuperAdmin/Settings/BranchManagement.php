<?php

namespace App\Livewire\SuperAdmin\Settings;

use Livewire\Component;
use App\Models\GlobalBranchManagement;

class BranchManagement extends Component
{
    public $warehouseAdd = true;
    public $branchEdit = true;
    public $branchDelete = true;
    public $branchAdminAssign = true;
    public $interBranchTransfer = true;
    public $centralWarehouse = true;
    public $branchHours = true;
    public $saasTenant = true;

    public function mount()
    {
        $settings = GlobalBranchManagement::first();
        
        if ($settings) {
            $this->warehouseAdd = $settings->warehouse_add === 'enabled';
            $this->branchEdit = $settings->branch_edit === 'enabled';
            $this->branchDelete = $settings->branch_delete === 'enabled';
            $this->branchAdminAssign = $settings->branch_admin_assign === 'enabled';
            $this->interBranchTransfer = $settings->inter_branch_transfer === 'enabled';
            $this->centralWarehouse = $settings->central_warehouse === 'enabled';
            $this->branchHours = $settings->branch_hours === 'set';
            $this->saasTenant = $settings->saas_tenant === 'enabled';
        }
    }

    public function save()
    {
        GlobalBranchManagement::updateOrCreate(
            ['id' => 1],
            [
                'warehouse_add' => $this->warehouseAdd ? 'enabled' : 'disabled',
                'branch_edit' => $this->branchEdit ? 'enabled' : 'disabled',
                'branch_delete' => $this->branchDelete ? 'enabled' : 'disabled',
                'branch_admin_assign' => $this->branchAdminAssign ? 'enabled' : 'disabled',
                'inter_branch_transfer' => $this->interBranchTransfer ? 'enabled' : 'disabled',
                'central_warehouse' => $this->centralWarehouse ? 'enabled' : 'disabled',
                'branch_hours' => $this->branchHours ? 'set' : 'disabled',
                'saas_tenant' => $this->saasTenant ? 'enabled' : 'disabled',
            ]
        );

        session()->flash('message', 'Branch Management settings updated successfully.');
    }

    public function render()
    {
        return view('livewire.super-admin.settings.branch-management');
    }
}

<?php

namespace App\Livewire\SuperAdmin\Roles;

use App\Livewire\BaseComponent;
use Livewire\Component;
use Spatie\Permission\Models\Role;

class Index extends BaseComponent
{
    public ?int $quantity = 2;

    public ?string $search = null;
    protected function getModelClass(): string
    {
        return Role::class;
    }

    protected function getAllSelectableIds(): array
    {
        return [];
    }

    public function render()
    {
        return view('livewire.super-admin.roles.index', [
            'headers' => [
                ['index' => 'id', 'label' => '#'],
                ['index' => 'name', 'label' => 'Role Name'],
            ],
            'rows' => Role::paginate(10),
        ]);
    }
}

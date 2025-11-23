<?php

namespace App\Livewire\BranchDashboard\DepartmentModule\Department;
//TODO: create department here, add general for all branches and branch specific via(b_id)
//TODO: verify it is super admin that wants to carry out some functions

use App\Models\Branch;
use Livewire\Component;
use App\Models\Department;
use App\Models\DepartmentCategory;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Computed;
use Livewire\Attributes\{Url, Layout, Title};

#[Layout('components.layouts.app.branch-dashboard')]
#[Title("Modify Departments")]
class CreateOrUpdate extends Component
{
    public ?string $name=',.';
    public ?string $category_id=',.';
    public ?string $description=',.';
    public ?string $selectedDepartmentId= '';
    public bool $isEditing = false;

    public bool $openAuditModal = false;

    public string $branch_id;

    #[Url(keep: true)]
    public string $b_id;

    public function mount(?int $id = null)
    {
        if (!is_null($id)) {
            $this->isEditing = true;

            $this->editDepartment($id);
        }

    }

    #[Computed(seconds: 4200)]
    public function getDepartmentCategories()
    {
        return DepartmentCategory::all();
    }


    #[Computed(seconds: 4200)]
    public function getBranches()
    {
        return Branch::all();
    }

    public function editDepartment($departmentId)
    {
        $department = Department::findOrFail($departmentId);
        $this->isEditing = true;
        $this->selectedDepartmentId = $departmentId;
        $this->name = $department->name;
        $this->branch_id = $department->branch_id;
        $this->category_id = $department->category_id;
        $this->description = $department->description ?? '';
    }

    public function resetDepartmentForm()
    {
        $this->name = '';
        $this->category_id = null;
        $this->description = '';
        $this->selectedDepartmentId = null;
        $this->isEditing = false;
    }

    public function saveDepartment()
    {
        $this->validate([
            'name' => 'required|string|max:255|unique:departments,name,' . $this->selectedDepartmentId,
            'category_id' => 'required|exists:department_categories,id',
            'description' => 'nullable|string',
        ]);

        $branch_id = is_super_admin() ? $this->branch_id : $this->b_id;

        $data = [
            'name' => $this->name,
            'branch_id' => $branch_id,
            'category_id' => $this->category_id,
            'description' => $this->description,
        ];

        dd($data);

        if ($this->isEditing && $this->selectedDepartmentId) {
            Department::findOrFail($this->selectedDepartmentId)->update($data);
            $message = 'Department updated successfully!';
        } else {
            Department::create($data);
            $message = 'Department created successfully!';
        }

        $this->toast()->success($message)->send();
        $this->redirect(route('branch-dashboard.branch.departments.index', request()->query()));
    }



    public function render()
    {

        return view('livewire.branch-dashboard.department-module.department.create');
    }
}

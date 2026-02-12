<?php

require_once __DIR__.'/vendor/autoload.php';

use App\Models\User;
use App\Models\Department;
use App\Models\DepartmentCategory;
use Illuminate\Support\Facades\DB;

// Initialize Laravel application
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Find the user with employee number EMP-CAL001-0016
$user = User::where('employee_number', 'EMP-CAL001-0016')->first();

if (!$user) {
    echo "User with employee number EMP-CAL001-0016 not found.\n";
    exit(1);
}

echo "User Information:\n";
echo "- ID: {$user->id}\n";
echo "- Name: {$user->name}\n";
echo "- Email: {$user->email}\n";
echo "- Employee Number: {$user->employee_number}\n";
echo "- Branch ID: {$user->branch_id}\n";
echo "- Department ID: {$user->department_id}\n";
echo "- User Type: {$user->user_type}\n";
echo "- Employment Status: {$user->employment_status}\n";

// Check department information if assigned
if ($user->department_id) {
    $department = Department::find($user->department_id);
    
    if ($department) {
        echo "\nDepartment Information:\n";
        echo "- Department ID: {$department->id}\n";
        echo "- Department Name: {$department->name}\n";
        echo "- Department Slug: {$department->slug}\n";
        echo "- Category ID: {$department->category_id}\n";
        echo "- Branch ID: {$department->branch_id}\n";
        
        // Check category information
        if ($department->category_id) {
            $category = DepartmentCategory::find($department->category_id);
            
            if ($category) {
                echo "\nCategory Information:\n";
                echo "- Category ID: {$category->id}\n";
                echo "- Category Name: {$category->name}\n";
            } else {
                echo "\nCategory not found for department.\n";
            }
        }
    } else {
        echo "\nDepartment not found for user.\n";
    }
} else {
    echo "\nUser is not assigned to any department.\n";
}

// Check user roles
$roles = $user->roles;
echo "\nUser Roles:\n";
foreach ($roles as $role) {
    echo "- Role Name: {$role->name}, Level: {$role->level}\n";
}

// Check all departments in the same branch
echo "\nDepartments in the same branch:\n";
$departments = Department::where('branch_id', $user->branch_id)->get();
foreach ($departments as $dept) {
    $category = $dept->category;
    echo "- Dept ID: {$dept->id}, Name: {$dept->name}, Slug: {$dept->slug}, Category: " . ($category ? $category->name : 'None') . "\n";
}

// Also check if the branch exists
$branch = \App\Models\Branch::find($user->branch_id);
if ($branch) {
    echo "\nBranch Information:\n";
    echo "- Branch ID: {$branch->id}\n";
    echo "- Branch Name: {$branch->name}\n";
    echo "- Branch Code: {$branch->code}\n";
} else {
    echo "\nBranch not found for user.\n";
}
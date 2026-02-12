<?php

require_once __DIR__.'/vendor/autoload.php';

use App\Models\User;
use App\Models\Department;
use App\Models\DepartmentCategory;

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
echo "- Branch ID: {$user->branch_id}\n";
echo "- Department ID: {$user->department_id}\n";

// Get user's department
$userDepartment = Department::find($user->department_id);
if ($userDepartment) {
    echo "\nUser's Department Information:\n";
    echo "- Department ID: {$userDepartment->id}\n";
    echo "- Department Name: {$userDepartment->name}\n";
    echo "- Department Slug: {$userDepartment->slug}\n";
    echo "- Department Branch ID: " . ($userDepartment->branch_id ?: 'NULL') . "\n";
    echo "- Department Category ID: " . ($userDepartment->category_id ?: 'NULL') . "\n";
    
    if ($userDepartment->category_id) {
        $userCategory = DepartmentCategory::find($userDepartment->category_id);
        if ($userCategory) {
            echo "- User Department Category Name: {$userCategory->name}\n";
        }
    }
} else {
    echo "\nUser's department not found.\n";
}

// Check the 'till' department specifically
$tillDepartment = Department::where('slug', 'till')->first();
if ($tillDepartment) {
    echo "\n'Till' Department Information:\n";
    echo "- Department ID: {$tillDepartment->id}\n";
    echo "- Department Name: {$tillDepartment->name}\n";
    echo "- Department Slug: {$tillDepartment->slug}\n";
    echo "- Department Branch ID: " . ($tillDepartment->branch_id ?: 'NULL') . "\n";
    echo "- Department Category ID: " . ($tillDepartment->category_id ?: 'NULL') . "\n";
    
    if ($tillDepartment->category_id) {
        $tillCategory = DepartmentCategory::find($tillDepartment->category_id);
        if ($tillCategory) {
            echo "- Till Department Category Name: {$tillCategory->name}\n";
        }
    }
    
    // Compare categories
    if ($userDepartment && $userDepartment->category_id && $tillDepartment->category_id) {
        $categoriesMatch = $userDepartment->category_id === $tillDepartment->category_id;
        echo "\nCategory comparison:\n";
        echo "- User department category ID matches Till department category ID: " . ($categoriesMatch ? 'YES' : 'NO') . "\n";
    }
} else {
    echo "\n'Till' department not found.\n";
}

// Check all departments in the Sales category
echo "\nAll departments in Sales category:\n";
$salesDepartments = Department::whereHas('category', function($query) {
    $query->where('name', 'Sales');
})->get();

foreach ($salesDepartments as $dept) {
    echo "- ID: {$dept->id}, Name: {$dept->name}, Slug: {$dept->slug}, Branch ID: " . ($dept->branch_id ?: 'NULL') . "\n";
}
<?php

require_once __DIR__.'/vendor/autoload.php';

use App\Models\User;
use App\Models\Department;
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
echo "- Branch ID: {$user->branch_id}\n";
echo "- Department ID: {$user->department_id}\n";

// Find the department
$department = Department::find($user->department_id);

if (!$department) {
    echo "Department with ID {$user->department_id} not found.\n";
    exit(1);
}

echo "\nDepartment Information BEFORE update:\n";
echo "- ID: {$department->id}\n";
echo "- Name: {$department->name}\n";
echo "- Branch ID: {$department->branch_id}\n";

// Update the department's branch_id to match the user's branch_id
if (empty($department->branch_id)) {
    $department->branch_id = $user->branch_id;
    $department->save();
    
    echo "\nUpdated department's branch_id to match user's branch_id.\n";
} else {
    echo "\nDepartment already has a branch_id assigned.\n";
}

echo "\nDepartment Information AFTER update:\n";
echo "- ID: {$department->id}\n";
echo "- Name: {$department->name}\n";
echo "- Branch ID: {$department->branch_id}\n";

echo "\nFix applied successfully!\n";
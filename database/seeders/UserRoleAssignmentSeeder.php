<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;

class UserRoleAssignmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define key users and their roles
        $userRoles = [
            'admin@sweettooth.local' => ['Super Admin'],
            'amina.okafor.5@sweettooth.com' => ['HR Manager'],
            'kemi.okafor.3@sweettooth.com' => ['Accounting Manager'],
            'blessing.okafor.59@sweettooth.com' => ['Inventory Manager'],
            'chigozie.okoro.66@sweettooth.com' => ['Head of Production'],
            'nneka.okafor.94@sweettooth.com' => ['Sales Manager'],
            'abubakar.mohammed.22@sweettooth.com' => ['Admin'],
            'chigozie.eze.51@sweettooth.com' => ['Kitchen Staff'],
            'ibrahim.bello.104@sweettooth.com' => ['Gelato Production Staff'],
            'abubakar.okafor.24@sweettooth.com' => ['Confectionaries Production Staff'],
            'nneka.chukwu.26@sweettooth.com' => ['Cashier'],
            'ada.eze.93@sweettooth.com' => ['Till Supervisor'],
            'folake.nwankwo.64@sweettooth.com' => ['Corner Store Manager'],
            'kunle.okafor.40@sweettooth.com' => ['Corner Store Staff'],
            'folake.chukwu.103@sweettooth.com' => ['Stock Controller'],
            'hauwa.nwankwo.102@sweettooth.com' => ['Store Keeper'],
            'kemi.mohammed.92@sweettooth.com' => ['HR Officer'],
        ];

        foreach ($userRoles as $email => $roles) {
            $user = User::where('email', $email)->first();
            
            if (!$user) {
                echo "User with email {$email} not found\n";
                continue;
            }

            foreach ($roles as $roleName) {
                $role = Role::where('name', $roleName)->first();
                
                if (!$role) {
                    echo "Role {$roleName} not found\n";
                    continue;
                }

                // Check if user already has this role
                if (!$user->hasRole($roleName)) {
                    $user->assignRole($roleName);
                    echo "Assigned role '{$roleName}' to user {$email}\n";
                } else {
                    echo "User {$email} already has role '{$roleName}'\n";
                }
            }
        }

        // Assign a default role to remaining users without roles
        $usersWithoutRoles = User::doesntHave('roles')->get();
        
        foreach ($usersWithoutRoles as $user) {
            if ($user->email !== 'admin@sweettooth.local' && strpos($user->email, '@sweettooth.com') !== false) {
                $role = Role::where('name', 'Employee')->first();
                if ($role) {
                    $user->assignRole($role);
                    echo "Assigned default role 'Employee' to user {$user->email}\n";
                }
            }
        }
    }
}
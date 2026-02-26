<?php

namespace App\Services;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Support\Collection;

class NotificationRecipientService
{
    /**
     * Fetch users by role names, scoped to a branch, while always including super admins.
     */
    public function usersForRoles(array $roles, ?string $branchId = null): Collection
    {
        $roles = array_values(array_unique(array_filter($roles)));
        $superRoles = config('notifications.roles.super_admin', ['Super Admin']);

        $branchRecipients = collect();
        if ($branchId && $roles) {
            $branchRecipients = User::role($roles)
                ->where('branch_id', $branchId)
                ->get();
        } elseif ($roles) {
            $branchRecipients = User::role($roles)->get();
        }

        $superRecipients = User::role($superRoles)->get();

        return $branchRecipients
            ->merge($superRecipients)
            ->unique('id')
            ->values();
    }

    /**
     * Fetch users by permission name, scoped to a branch, while always including super admins.
     */
    public function usersForPermission(string $permission, ?string $branchId = null): Collection
    {
        $superRoles = config('notifications.roles.super_admin', ['Super Admin']);

        $branchRecipients = $branchId
            ? User::permission($permission)->where('branch_id', $branchId)->get()
            : User::permission($permission)->get();

        $superRecipients = User::role($superRoles)->get();

        return $branchRecipients
            ->merge($superRecipients)
            ->unique('id')
            ->values();
    }

    /**
     * Fetch users by role names, scoped to a branch and department, while always including super admins.
     */
    public function usersForRolesInDepartment(array $roles, ?string $branchId = null, ?int $departmentId = null): Collection
    {
        $roles = array_values(array_unique(array_filter($roles)));
        $superRoles = config('notifications.roles.super_admin', ['Super Admin']);

        $branchRecipients = collect();
        if ($roles) {
            $branchRecipients = User::role($roles)
                ->when($branchId, fn ($query) => $query->where('branch_id', $branchId))
                ->when($departmentId, fn ($query) => $query->where('department_id', $departmentId))
                ->get();
        }

        $superRecipients = User::role($superRoles)->get();

        return $branchRecipients
            ->merge($superRecipients)
            ->unique('id')
            ->values();
    }

    /**
     * Build branch context for notification payloads.
     */
    public function branchContext(?string $branchId): array
    {
        if (!$branchId) {
            return [
                'branch_id' => null,
                'branch_name' => null,
            ];
        }

        $branchName = Branch::whereKey($branchId)->value('name');

        return [
            'branch_id' => $branchId,
            'branch_name' => $branchName,
        ];
    }
}

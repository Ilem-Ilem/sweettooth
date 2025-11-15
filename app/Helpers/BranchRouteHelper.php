<?php

use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

if (! function_exists('branch_route')) {
    /**
     * Generate a route URL that automatically includes and validates the current b_id query parameter.
     * Falls back to session value if not in URL.
     */
    function branch_route(string $name, array $params = [], bool $absolute = true): string
    {
        // Try to get the current b_id from:
        // 1. Manually passed params
        // 2. Request query parameter
        // 3. Session (for super admins)
        $b_id = $params['b_id'] ?? Request::query('b_id') ?? current_branch_id();

        // Validate that it's a proper UUID
        $validator = Validator::make(['b_id' => $b_id], [
            'b_id' => ['nullable', 'uuid'],
        ]);

        if ($validator->fails()) {
            Log::warning('Invalid b_id detected in branchRoute', [
                'input' => $b_id,
                'route' => $name,
            ]);
            // Optionally, remove invalid b_id or throw an exception
            unset($params['b_id']);
        } else {
            $params['b_id'] = $b_id;
        }

        return route($name, $params, $absolute);
    }
}

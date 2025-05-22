<?php

/**
 * Navigation Helper Functions
 */

if (!function_exists('is_active')) {
    /**
     * Check if the current URL path matches the given path
     *
     * @param string $path The path to check against
     * @return string Returns 'active' if paths match, otherwise empty string
     */
    function is_active($path)
    {
        $currentPath = current_url(true)->getPath();

        // Exact match
        if ($currentPath === $path) {
            return 'active';
        }

        // Check if it's a submenu of the path
        if ($path !== '/' && strpos($currentPath, $path) === 0) {
            return 'active';
        }

        return '';
    }
}

if (!function_exists('get_role_badge')) {
    /**
     * Get HTML badge for user role
     *
     * @param string $role The user role
     * @return string HTML badge
     */
    function get_role_badge($role)
    {
        $roleLabels = [
            'admin' => '<span class="badge badge-danger ml-1">Admin</span>',
            'dekan' => '<span class="badge badge-primary ml-1">Dekan</span>',
            'wadek1' => '<span class="badge badge-info ml-1">Wadek 1</span>',
            'wadek2' => '<span class="badge badge-info ml-1">Wadek 2</span>',
            'wadek3' => '<span class="badge badge-info ml-1">Wadek 3</span>',
            'kaprodi' => '<span class="badge badge-success ml-1">Kaprodi</span>',
            'staff' => '<span class="badge badge-secondary ml-1">Staff</span>'
        ];

        return $roleLabels[$role] ?? '<span class="badge badge-secondary ml-1">' . ucfirst($role) . '</span>';
    }
}

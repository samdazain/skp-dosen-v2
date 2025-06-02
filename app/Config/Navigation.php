<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Navigation extends BaseConfig
{
    /**
     * Main navigation items for sidebar
     */
    public $sidebarMenu = [
        [
            'path' => '/dashboard',
            'icon' => 'fas fa-tachometer-alt',
            'label' => 'Dashboard',
            'roles' => ['admin', 'dekan', 'wadek1', 'wadek2', 'wadek3', 'kaprodi', 'staff'] // Available to all roles
        ],
        [
            'path' => '/skp',
            'icon' => 'fas fa-file-alt',
            'label' => 'SKP Master',
            'roles' => ['admin', 'dekan', 'wadek1', 'wadek2', 'wadek3', 'kaprodi', 'staff']
        ],
        [
            'path' => '/lecturers',
            'icon' => 'fas fa-chalkboard-teacher',
            'label' => 'Daftar Dosen',
            'roles' => ['admin', 'dekan', 'wadek1', 'wadek2', 'wadek3', 'kaprodi', 'staff']
        ],
        [
            'path' => '/integrity',
            'icon' => 'fas fa-shield-alt',
            'label' => 'Data Integritas',
            'roles' => ['admin', 'dekan', 'wadek1', 'wadek2', 'wadek3', 'kaprodi', 'staff']
        ],
        [
            'path' => '/discipline',
            'icon' => 'fas fa-tasks',
            'label' => 'Data Disiplin',
            'roles' => ['admin', 'dekan', 'wadek1', 'wadek2', 'wadek3', 'kaprodi', 'staff']
        ],
        [
            'path' => '/commitment',
            'icon' => 'fas fa-handshake',
            'label' => 'Komitmen',
            'roles' => ['admin', 'dekan', 'wadek1', 'wadek2', 'wadek3', 'kaprodi', 'staff']
        ],
        [
            'path' => '/cooperation',
            'icon' => 'fas fa-users',
            'label' => 'Kerja Sama',
            'roles' => ['admin', 'dekan', 'wadek1', 'wadek2', 'wadek3', 'kaprodi', 'staff']
        ],
        [
            'path' => '/orientation',
            'icon' => 'fas fa-concierge-bell',
            'label' => 'Orientasi Pelayanan',
            'roles' => ['admin', 'dekan', 'wadek1', 'wadek2', 'wadek3', 'kaprodi', 'staff']
        ],
        [
            'path' => '/score',
            'icon' => 'fas fa-edit',
            'label' => 'Setting Nilai',
            'roles' => ['admin', 'dekan'] // Restricted: Only admin and dekan
        ],
        [
            'path' => '/user',
            'icon' => 'fas fa-user-cog',
            'label' => 'Manajemen User',
            'roles' => ['admin', 'dekan'] // Restricted: Only admin and dekan
        ],
        [
            'path' => '/settings',
            'icon' => 'fas fa-cog',
            'label' => 'Pengaturan Akun',
            'roles' => ['admin', 'dekan', 'wadek1', 'wadek2', 'wadek3', 'kaprodi', 'staff'] // Available to all roles
        ]
    ];

    /**
     * Get sidebar menu items with active status and role filtering
     */
    public function getSidebarMenu()
    {
        $menu = $this->sidebarMenu;
        $userRole = session()->get('user_role') ?? 'staff';
        $filteredMenu = [];

        // Debug logging
        log_message('debug', 'Navigation - Current user role: ' . $userRole);

        foreach ($menu as $item) {
            // Always include menu item but mark access status
            $item['active'] = is_active($item['path']);
            $item['hasAccess'] = $this->hasRoleAccess($item['roles'], $userRole);

            // Debug logging for restricted items
            if (in_array($item['path'], ['/score', '/user'])) {
                log_message('debug', 'Navigation - Path: ' . $item['path'] . ', User Role: ' . $userRole . ', Has Access: ' . ($item['hasAccess'] ? 'true' : 'false'));
            }

            $filteredMenu[] = $item;
        }

        return $filteredMenu;
    }

    /**
     * Check if user role has access to menu item
     */
    private function hasRoleAccess(array $allowedRoles, string $userRole): bool
    {
        return in_array($userRole, $allowedRoles);
    }

    /**
     * Check if current user can access a specific path
     */
    public function canAccessPath(string $path): bool
    {
        $userRole = session()->get('user_role') ?? 'staff';

        // Normalize the path - remove trailing slashes and ensure leading slash
        $normalizedPath = '/' . trim($path, '/');

        // Debug logging
        log_message('debug', 'Navigation canAccessPath - Original path: ' . $path . ', Normalized: ' . $normalizedPath . ', User role: ' . $userRole);

        foreach ($this->sidebarMenu as $item) {
            $normalizedItemPath = '/' . trim($item['path'], '/');

            if ($normalizedItemPath === $normalizedPath) {
                $hasAccess = $this->hasRoleAccess($item['roles'], $userRole);
                log_message('debug', 'Navigation canAccessPath - Match found for ' . $normalizedPath . ', Access: ' . ($hasAccess ? 'granted' : 'denied'));
                return $hasAccess;
            }
        }

        // If path not found in navigation menu, allow access (for other system pages)
        log_message('debug', 'Navigation canAccessPath - Path not found in menu, allowing access: ' . $normalizedPath);
        return true;
    }

    /**
     * Get user role hierarchy (higher number = more permissions)
     */
    public function getRoleLevel(string $role): int
    {
        $roleLevels = [
            'staff' => 1,
            'kaprodi' => 2,
            'wadek3' => 3,
            'wadek2' => 4,
            'wadek1' => 5,
            'dekan' => 6,
            'admin' => 7
        ];

        return $roleLevels[$role] ?? 0;
    }
}

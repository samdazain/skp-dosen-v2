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
            'roles' => ['admin', 'user', 'viewer'] // Available to all roles
        ],
        [
            'path' => '/skp',
            'icon' => 'fas fa-file-alt',
            'label' => 'SKP Master',
            'roles' => ['admin', 'user']
        ],
        [
            'path' => '/lecturers',
            'icon' => 'fas fa-chalkboard-teacher',
            'label' => 'Daftar Dosen',
            'roles' => ['admin', 'user']
        ],
        [
            'path' => '/integrity',
            'icon' => 'fas fa-shield-alt',
            'label' => 'Data Integritas',
            'roles' => ['admin', 'user']
        ],
        [
            'path' => '/discipline',
            'icon' => 'fas fa-tasks',
            'label' => 'Data Disiplin',
            'roles' => ['admin', 'user']
        ],
        [
            'path' => '/commitment',
            'icon' => 'fas fa-handshake',
            'label' => 'Komitmen',
            'roles' => ['admin', 'user']
        ],
        [
            'path' => '/cooperation',
            'icon' => 'fas fa-users',
            'label' => 'Kerja Sama',
            'roles' => ['admin', 'user']
        ],
        [
            'path' => '/orientation',
            'icon' => 'fas fa-concierge-bell',
            'label' => 'Orientasi Pelayanan',
            'roles' => ['admin', 'user']
        ],
        [
            'path' => '/score',
            'icon' => 'fas fa-edit',
            'label' => 'Setting Nilai',
            'roles' => ['admin'] // Admin only
        ],
        [
            'path' => '/user',
            'icon' => 'fas fa-user-cog',
            'label' => 'Manajemen User',
            'roles' => ['admin'] // Admin only
        ],
        [
            'path' => '/settings',
            'icon' => 'fas fa-cog',
            'label' => 'Pengaturan Akun',
            'roles' => ['admin', 'user', 'viewer'] // Available to all roles
        ]
    ];

    /**
     * Get sidebar menu items with active status and role filtering
     */
    public function getSidebarMenu()
    {
        $menu = $this->sidebarMenu;
        $userRole = session()->get('user_role') ?? 'viewer';
        $filteredMenu = [];

        foreach ($menu as $item) {
            // Check if user role has access to this menu item
            if ($this->hasRoleAccess($item['roles'], $userRole)) {
                $item['active'] = is_active($item['path']);
                $filteredMenu[] = $item;
            }
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
        $userRole = session()->get('user_role') ?? 'viewer';

        foreach ($this->sidebarMenu as $item) {
            if ($item['path'] === $path) {
                return $this->hasRoleAccess($item['roles'], $userRole);
            }
        }

        return false;
    }

    /**
     * Get user role hierarchy (higher number = more permissions)
     */
    public function getRoleLevel(string $role): int
    {
        $roleLevels = [
            'viewer' => 1,
            'user' => 2,
            'admin' => 3
        ];

        return $roleLevels[$role] ?? 0;
    }
}

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
            'label' => 'Dashboard'
        ],
        [
            'path' => '/skp',
            'icon' => 'fas fa-file-alt',
            'label' => 'SKP Master'
        ],
        [
            'path' => '/lecturers',
            'icon' => 'fas fa-chalkboard-teacher',
            'label' => 'Daftar Dosen'
        ],
        [
            'path' => '/integrity',
            'icon' => 'fas fa-shield-alt',
            'label' => 'Data Integritas'
        ],
        [
            'path' => '/discipline',
            'icon' => 'fas fa-tasks',
            'label' => 'Data Disiplin'
        ],
        [
            'path' => '/commitment',
            'icon' => 'fas fa-handshake',
            'label' => 'Komitmen'
        ],
        [
            'path' => '/cooperation',
            'icon' => 'fas fa-users',
            'label' => 'Kerja Sama'
        ],
        [
            'path' => '/orientation',
            'icon' => 'fas fa-concierge-bell',
            'label' => 'Orientasi Pelayanan'
        ],
        [
            'path' => '/score',
            'icon' => 'fas fa-edit',
            'label' => 'Setting Nilai'
        ],
        [
            'path' => '/archive',
            'icon' => 'fas fa-archive',
            'label' => 'Arsip File'
        ],
        [
            'path' => '/history',
            'icon' => 'fas fa-history',
            'label' => 'Riwayat Aktivitas'
        ],
        [
            'path' => '/user',
            'icon' => 'fas fa-user-cog',
            'label' => 'Manajemen User'
        ],
        [
            'path' => '/settings',
            'icon' => 'fas fa-cog',
            'label' => 'Pengaturan Akun'
        ]
    ];

    /**
     * Get sidebar menu items with active status
     */
    public function getSidebarMenu()
    {
        $menu = $this->sidebarMenu;

        foreach ($menu as &$item) {
            $item['active'] = is_active($item['path']);
        }

        return $menu;
    }
}

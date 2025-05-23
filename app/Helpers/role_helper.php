<?php

if (!function_exists('get_role_badge')) {
    /**
     * Return HTML badge for user role
     */
    function get_role_badge($role)
    {
        $badges = [
            'admin' => '<span class="badge badge-danger">Admin</span>',
            'dekan' => '<span class="badge badge-primary">Dekan</span>',
            'wadek1' => '<span class="badge badge-info">Wakil Dekan 1</span>',
            'wadek2' => '<span class="badge badge-info">Wakil Dekan 2</span>',
            'wadek3' => '<span class="badge badge-info">Wakil Dekan 3</span>',
            'kaprodi' => '<span class="badge badge-success">Kaprodi</span>',
            'staff' => '<span class="badge badge-secondary">Staff</span>'
        ];

        return $badges[$role] ?? '<span class="badge badge-light">' . ucfirst($role) . '</span>';
    }
}

if (!function_exists('get_role_list')) {
    /**
     * Return list of available roles with their labels
     */
    function get_role_list()
    {
        return [
            'admin' => 'Admin',
            'dekan' => 'Dekan',
            'wadek1' => 'Wakil Dekan 1',
            'wadek2' => 'Wakil Dekan 2',
            'wadek3' => 'Wakil Dekan 3',
            'kaprodi' => 'Kaprodi',
            'staff' => 'Staff'
        ];
    }
}

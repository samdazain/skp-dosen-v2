<?php

if (!function_exists('getSortUrl')) {
    /**
     * Generate sort URL for table headers
     */
    function getSortUrl($column, $currentSortBy, $currentSortOrder, $search = null, $perPage = 10)
    {
        $newSortOrder = ($currentSortBy === $column && $currentSortOrder === 'asc') ? 'desc' : 'asc';

        $params = [
            'sort_by' => $column,
            'sort_order' => $newSortOrder,
            'per_page' => $perPage
        ];

        if ($search) {
            $params['search'] = $search;
        }

        return base_url('lecturers') . '?' . http_build_query($params);
    }
}

if (!function_exists('getSortIcon')) {
    /**
     * Generate sort icon for table headers
     */
    function getSortIcon($column, $currentSortBy, $currentSortOrder)
    {
        if ($currentSortBy !== $column) {
            return '<i class="fas fa-sort sort-icon"></i>';
        }

        $iconClass = $currentSortOrder === 'asc' ? 'fa-sort-up' : 'fa-sort-down';
        return '<i class="fas ' . $iconClass . ' sort-icon sort-active"></i>';
    }
}

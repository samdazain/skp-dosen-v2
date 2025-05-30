<?php

/**
 * Table Styles Component
 */
?>

<style>
    /* Sortable Headers */
    .sortable {
        cursor: pointer;
        user-select: none;
        transition: background-color 0.3s ease;
    }

    .sortable:hover {
        background-color: rgba(255, 255, 255, 0.1);
    }

    /* Sort Icons */
    .sort-icon {
        font-size: 0.8em;
        opacity: 0.6;
        transition: opacity 0.3s ease, color 0.3s ease;
    }

    .sortable:hover .sort-icon {
        opacity: 1;
    }

    .sortable.sort-asc .sort-icon::before {
        content: "\f0de";
        /* fa-sort-up */
        color: #28a745;
        opacity: 1;
    }

    .sortable.sort-desc .sort-icon::before {
        content: "\f0dd";
        /* fa-sort-down */
        color: #dc3545;
        opacity: 1;
    }

    .sortable.sort-asc .sort-icon,
    .sortable.sort-desc .sort-icon {
        opacity: 1;
    }

    /* Empty State */
    .empty-state {
        padding: 2rem;
    }

    /* Badge Styling */
    .badge-lg {
        font-size: 0.9em;
        padding: 0.5rem 0.75rem;
    }

    /* User Panel */
    .user-panel .info {
        padding: 0;
    }

    /* Responsive Adjustments */
    @media (max-width: 768px) {
        .card-tools {
            flex-direction: column;
            gap: 0.5rem;
        }

        .btn-group {
            margin-right: 0 !important;
            margin-bottom: 0.5rem;
        }
    }
</style>
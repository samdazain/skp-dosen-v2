<?php

/**
 * Search Bar Component
 */
?>

<div class="input-group input-group-sm" style="width: 250px;">
    <input type="text"
        name="table_search"
        class="form-control"
        id="searchInput"
        placeholder="Cari berdasarkan nama..."
        autocomplete="off">
    <div class="input-group-append">
        <button type="button"
            class="btn btn-outline-light"
            data-toggle="tooltip"
            title="Pencarian otomatis saat mengetik">
            <i class="fas fa-search"></i>
        </button>
    </div>
</div>
<?php

/**
 * Discipline Table Scripts
 */
?>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Discipline Table Manager
        const disciplineTable = new DisciplineTableManager();
        disciplineTable.init();
    });

    /**
     * Discipline Table Manager Class
     */
    class DisciplineTableManager {
        constructor() {
            this.table = document.getElementById('disciplineTable');
            this.tbody = this.table?.querySelector('tbody');
            this.sortableHeaders = document.querySelectorAll('.sortable');
            this.searchInput = document.getElementById('searchInput');

            this.currentSort = {
                column: null,
                direction: 'asc'
            };

            this.positionHierarchy = [
                'DEKAN',
                'WAKIL DEKAN I',
                'WAKIL DEKAN II',
                'WAKIL DEKAN III',
                'KOORPRODI IF',
                'KOORPRODI SI',
                'KOORPRODI SD',
                'KOORPRODI BD',
                'KOORPRODI MTI',
                'Ka Lab SCR',
                'Ka Lab PPSTI',
                'Ka Lab SOLUSI',
                'Ka Lab MSI',
                'Ka Lab Sains Data',
                'Ka Lab BISDI',
                'Ka Lab MTI',
                'Ka UPT TIK',
                'Ka UPA PKK',
                'Ka Pengembangan Pembelajaran LPMPP',
                'PPMB',
                'KOORDINATOR PUSAT KARIR DAN TRACER STUDY',
                'LSP UPNVJT',
                'UPT TIK',
                'Dosen Prodi'
            ];

            this.studyProgramOrder = [
                'bisnis_digital',
                'informatika',
                'sistem_informasi',
                'sains_data',
                'magister_teknologi_informasi'
            ];
        }

        /**
         * Initialize the table manager
         */
        init() {
            if (!this.table || !this.tbody) {
                console.warn('Discipline table elements not found');
                return;
            }

            this.initializeSorting();
            this.initializeSearch();
            this.initializeTooltips();
            this.initializeKeyboardShortcuts();
        }

        /**
         * Initialize sorting functionality
         */
        initializeSorting() {
            this.sortableHeaders.forEach(header => {
                header.addEventListener('click', () => {
                    const sortType = header.dataset.sort;
                    this.handleSort(sortType, header);
                });
            });
        }

        /**
         * Initialize search functionality
         */
        initializeSearch() {
            if (!this.searchInput) return;

            this.searchInput.addEventListener('keyup', (e) => {
                this.handleSearch(e.target.value);
            });
        }

        /**
         * Initialize tooltips
         */
        initializeTooltips() {
            if (typeof $ !== 'undefined') {
                $('[data-toggle="tooltip"]').tooltip();
            }
        }

        /**
         * Initialize keyboard shortcuts
         */
        initializeKeyboardShortcuts() {
            document.addEventListener('keydown', (e) => {
                if (e.ctrlKey) {
                    switch (e.key) {
                        case '1':
                            e.preventDefault();
                            document.querySelector('[data-sort="name"]')?.click();
                            break;
                        case '2':
                            e.preventDefault();
                            document.querySelector('[data-sort="study_program"]')?.click();
                            break;
                    }
                }
            });
        }

        /**
         * Handle table sorting
         */
        handleSort(sortType, headerElement) {
            // Determine sort direction
            if (this.currentSort.column === sortType) {
                this.currentSort.direction = this.currentSort.direction === 'asc' ? 'desc' : 'asc';
            } else {
                this.currentSort.direction = 'asc';
            }

            this.currentSort.column = sortType;

            // Update header UI
            this.updateSortHeaders(headerElement, this.currentSort.direction);

            // Get rows and sort them
            const rows = Array.from(this.tbody.querySelectorAll('tr')).filter(row =>
                !row.querySelector('td[colspan]') // Exclude empty state row
            );

            if (rows.length === 0) return;

            // Sort rows based on type
            rows.sort((a, b) => this.compareRows(a, b, sortType));

            // Clear tbody and append sorted rows
            this.tbody.innerHTML = '';
            rows.forEach((row, index) => {
                // Update row numbers
                const numberCell = row.querySelector('td:first-child');
                if (numberCell) {
                    numberCell.textContent = index + 1;
                }
                this.tbody.appendChild(row);
            });

            // Show sort success toast
            this.showSortToast(sortType, this.currentSort.direction);
        }

        /**
         * Compare two rows for sorting
         */
        compareRows(a, b, sortType) {
            let aValue, bValue;

            if (sortType === 'name') {
                // Sort by position hierarchy
                const aPosition = a.dataset.position || '';
                const bPosition = b.dataset.position || '';

                const aIndex = this.positionHierarchy.indexOf(aPosition);
                const bIndex = this.positionHierarchy.indexOf(bPosition);

                // If position not found in hierarchy, put at end
                const aPos = aIndex === -1 ? this.positionHierarchy.length : aIndex;
                const bPos = bIndex === -1 ? this.positionHierarchy.length : bIndex;

                aValue = aPos;
                bValue = bPos;

                // If same position, sort by name
                if (aPos === bPos) {
                    const aName = a.querySelector('td:nth-child(2) strong')?.textContent.trim() || '';
                    const bName = b.querySelector('td:nth-child(2) strong')?.textContent.trim() || '';
                    return aName.localeCompare(bName);
                }
            } else if (sortType === 'study_program') {
                // Sort by study program order
                const aProgram = a.dataset.studyProgram || '';
                const bProgram = b.dataset.studyProgram || '';

                const aIndex = this.studyProgramOrder.indexOf(aProgram);
                const bIndex = this.studyProgramOrder.indexOf(bProgram);

                // If program not found in order, put at end
                aValue = aIndex === -1 ? this.studyProgramOrder.length : aIndex;
                bValue = bIndex === -1 ? this.studyProgramOrder.length : bIndex;
            }

            // Apply sort direction
            if (this.currentSort.direction === 'asc') {
                return aValue - bValue;
            } else {
                return bValue - aValue;
            }
        }

        /**
         * Update sort headers UI
         */
        updateSortHeaders(activeHeader, direction) {
            // Reset all headers
            this.sortableHeaders.forEach(header => {
                header.classList.remove('sort-asc', 'sort-desc');
            });

            // Set active header
            if (direction === 'asc') {
                activeHeader.classList.add('sort-asc');
            } else {
                activeHeader.classList.add('sort-desc');
            }
        }

        /**
         * Handle search functionality
         */
        handleSearch(searchTerm) {
            const term = searchTerm.toLowerCase();
            const tableRows = this.tbody.querySelectorAll('tr');

            tableRows.forEach(row => {
                // Skip rows that don't have lecturer data
                if (row.cells.length < 6) {
                    return;
                }

                const name = row.cells[1].textContent.toLowerCase();
                const position = row.dataset.position?.toLowerCase() || '';
                const studyProgram = row.cells[2].textContent.toLowerCase();

                const isVisible = name.includes(term) ||
                    position.includes(term) ||
                    studyProgram.includes(term);

                row.style.display = isVisible ? '' : 'none';
            });

            // Update table info after search
            this.updateTableInfo();
        }

        /**
         * Update table information display
         */
        updateTableInfo() {
            const visibleRows = this.tbody.querySelectorAll('tr[style=""], tr:not([style])');
            const emptyRows = this.tbody.querySelectorAll('tr td[colspan]');
            const actualRows = visibleRows.length - emptyRows.length;

            const tableInfo = document.querySelector('.card-footer .float-left small');
            if (tableInfo && actualRows >= 0) {
                tableInfo.innerHTML = `
                <i class="fas fa-info-circle mr-1"></i>
                Menampilkan ${actualRows} data dosen.
                Data disiplin dikelola melalui upload Excel.
                Nilai dihitung otomatis berdasarkan konfigurasi scoring.
            `;
            }
        }

        /**
         * Show sort toast notification
         */
        showSortToast(sortType, direction) {
            const sortNames = {
                'name': 'Nama Dosen (berdasarkan jabatan)',
                'study_program': 'Program Studi'
            };

            const directionText = direction === 'asc' ? 'A-Z' : 'Z-A';
            const message = `Tabel diurutkan berdasarkan ${sortNames[sortType]} (${directionText})`;

            if (typeof showToast === 'function') {
                showToast(message, 'info');
            }
        }
    }

    /**
     * Global function for showing toasts
     */
    function showToast(message, type = 'info') {
        if (typeof Swal !== 'undefined') {
            const iconMap = {
                'success': 'success',
                'error': 'error',
                'warning': 'warning',
                'info': 'info'
            };

            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: iconMap[type] || 'info',
                title: message,
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });
        } else {
            // Fallback alert
            console.log(`${type.toUpperCase()}: ${message}`);
        }
    }
</script>
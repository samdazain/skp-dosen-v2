<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Define position hierarchy for sorting
        const positionHierarchy = [
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

        // Study program order for sorting
        const studyProgramOrder = [
            'bisnis_digital',
            'informatika',
            'sistem_informasi',
            'sains_data',
            'magister_teknologi_informasi'
        ];

        // Get table elements
        const table = document.getElementById('integrityTable');
        const tbody = table ? table.querySelector('tbody') : null;
        const sortableHeaders = document.querySelectorAll('.sortable');

        if (!table || !tbody) {
            console.log('Table elements not found');
            return;
        }

        // Initialize sorting
        let currentSort = {
            column: null,
            direction: 'asc'
        };

        // Add click event listeners to sortable headers
        sortableHeaders.forEach(header => {
            header.addEventListener('click', function() {
                const sortType = this.dataset.sort;
                handleSort(sortType, this);
            });
        });

        function handleSort(sortType, headerElement) {
            // Determine sort direction
            if (currentSort.column === sortType) {
                currentSort.direction = currentSort.direction === 'asc' ? 'desc' : 'asc';
            } else {
                currentSort.direction = 'asc';
            }

            currentSort.column = sortType;

            // Update header UI
            updateSortHeaders(headerElement, currentSort.direction);

            // Get rows and sort them
            const rows = Array.from(tbody.querySelectorAll('tr')).filter(row =>
                !row.querySelector('td[colspan]') // Exclude empty state row
            );

            if (rows.length === 0) return;

            // Sort rows based on type
            rows.sort((a, b) => {
                let aValue, bValue;

                if (sortType === 'name') {
                    // Sort by position hierarchy
                    const aPosition = a.dataset.position || '';
                    const bPosition = b.dataset.position || '';

                    const aIndex = positionHierarchy.indexOf(aPosition);
                    const bIndex = positionHierarchy.indexOf(bPosition);

                    // If position not found in hierarchy, put at end
                    const aPos = aIndex === -1 ? positionHierarchy.length : aIndex;
                    const bPos = bIndex === -1 ? positionHierarchy.length : bIndex;

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

                    const aIndex = studyProgramOrder.indexOf(aProgram);
                    const bIndex = studyProgramOrder.indexOf(bProgram);

                    // If program not found in order, put at end
                    aValue = aIndex === -1 ? studyProgramOrder.length : aIndex;
                    bValue = bIndex === -1 ? studyProgramOrder.length : bIndex;
                }

                // Apply sort direction
                if (currentSort.direction === 'asc') {
                    return aValue - bValue;
                } else {
                    return bValue - aValue;
                }
            });

            // Clear tbody and append sorted rows
            tbody.innerHTML = '';
            rows.forEach((row, index) => {
                // Update row numbers
                const numberCell = row.querySelector('td:first-child');
                if (numberCell) {
                    numberCell.textContent = index + 1;
                }
                tbody.appendChild(row);
            });

            // Show sort success toast
            showSortToast(sortType, currentSort.direction);
        }

        function updateSortHeaders(activeHeader, direction) {
            // Reset all headers
            sortableHeaders.forEach(header => {
                header.classList.remove('sort-asc', 'sort-desc');
            });

            // Set active header
            if (direction === 'asc') {
                activeHeader.classList.add('sort-asc');
            } else {
                activeHeader.classList.add('sort-desc');
            }
        }

        function showSortToast(sortType, direction) {
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

        // Search functionality
        const searchInput = document.getElementById('integritySearch');
        if (searchInput) {
            searchInput.addEventListener('keyup', function() {
                const searchTerm = this.value.toLowerCase();
                const tableRows = document.querySelectorAll('#integrityTable tbody tr');

                tableRows.forEach(row => {
                    if (row.cells.length > 1) {
                        const name = row.cells[1].textContent.toLowerCase();
                        const nip = row.cells[2].textContent.toLowerCase();
                        const position = row.dataset.position?.toLowerCase() || '';

                        if (name.includes(searchTerm) ||
                            nip.includes(searchTerm) ||
                            position.includes(searchTerm)) {
                            row.style.display = '';
                        } else {
                            row.style.display = 'none';
                        }
                    }
                });

                // Update table info after search
                updateTableInfo();
            });
        }

        function updateTableInfo() {
            const visibleRows = document.querySelectorAll('#integrityTable tbody tr[style=""], #integrityTable tbody tr:not([style])');
            const emptyRows = document.querySelectorAll('#integrityTable tbody tr td[colspan]');
            const actualRows = visibleRows.length - emptyRows.length;

            const tableInfo = document.querySelector('.card-footer');
            if (tableInfo && actualRows >= 0) {
                tableInfo.innerHTML = `
                    <small class="text-muted">
                        <i class="fas fa-info-circle mr-1"></i>
                        Menampilkan ${actualRows} dosen
                    </small>
                `;
            }
        }

        // Initialize tooltips
        $('[data-toggle="tooltip"]').tooltip();

        // Auto-refresh data every 5 minutes
        setInterval(() => {
            if (typeof refreshPageData === 'function') {
                refreshPageData();
            }
        }, 300000); // 5 minutes

        // Add keyboard shortcuts for sorting
        document.addEventListener('keydown', function(e) {
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
    });

    // Global function for showing toasts
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
            alert(message);
        }
    }
</script>
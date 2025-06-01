<script>
    $(document).ready(function() {
        // Initialize tooltips if available
        if (typeof $().tooltip === 'function') {
            $('[data-toggle="tooltip"]').tooltip();
        }

        // Enhanced search functionality
        $('#searchInput').on('keyup', function() {
            const value = $(this).val().toLowerCase();
            $('table tbody tr').filter(function() {
                const isVisible = $(this).text().toLowerCase().indexOf(value) > -1;
                $(this).toggle(isVisible);
                return isVisible;
            });

            // Update row numbers after filtering
            updateRowNumbers();
        });

        // Function to update row numbers after filtering
        function updateRowNumbers() {
            $('table tbody tr:visible').each(function(index) {
                $(this).find('td:first').text(index + 1);
            });
        }

        // Auto-refresh functionality
        function autoRefresh() {
            // Optional: Auto-refresh every 5 minutes
            setTimeout(function() {
                if (confirm('Refresh data orientasi pelayanan untuk memperbarui skor terbaru?')) {
                    refreshOrientationData();
                }
            }, 300000); // 5 minutes
        }

        // Initialize auto-refresh (uncomment if needed)
        // autoRefresh();
    });

    /**
     * Clear search input and show all rows
     */
    function clearSearch() {
        $('#searchInput').val('');
        $('table tbody tr').show();
        $('#no-results-row').remove();
    }

    // Global function for refreshing orientation data
    function refreshOrientationData() {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Memuat Data...',
                text: 'Sedang menyinkronkan data orientasi pelayanan dosen dan menghitung ulang skor',
                allowOutsideClick: false,
                showConfirmButton: false,
                willOpen: () => {
                    Swal.showLoading();
                }
            });
        }

        // Auto-refresh page after 1 second
        setTimeout(() => {
            window.location.reload();
        }, 1000);
    }

    /**
     * Show information modal about readonly data
     */
    function showOrientationInfo() {
        $('#infoModal').modal('show');
    }

    /**
     * Show export options modal
     */
    function showExportOptions() {
        $('#exportModal').modal('show');
    }

    /**
     * Export functionality helpers
     */
    function exportToExcel() {
        window.location.href = '<?= base_url('orientation/export-excel') ?>' + '?' + new URLSearchParams(window.location.search);
    }

    function exportToPdf() {
        window.location.href = '<?= base_url('orientation/export-pdf') ?>' + '?' + new URLSearchParams(window.location.search);
    }

    /**
     * Print functionality for readonly data
     */
    function printOrientationData() {
        const printContent = `
        <div class="print-header" style="text-align: center; margin-bottom: 30px;">
            <h2>Data Orientasi Pelayanan Dosen</h2>
            <p>Semester: <?= $currentSemester['year'] ?>/<?= $currentSemester['term'] === '1' ? 'Ganjil' : 'Genap' ?></p>
            <p>Dicetak pada: ${new Date().toLocaleDateString('id-ID', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            })}</p>
            <hr>
        </div>
        ${$('.table-responsive').html()}
        <div class="print-footer" style="margin-top: 30px; text-align: center; font-size: 12px; color: #666;">
            <p>Data ini bersifat readonly dan dikelola melalui sistem angket terpisah</p>
        </div>
    `;

        const printWindow = window.open('', '_blank');
        printWindow.document.write(`
        <html>
            <head>
                <title>Data Orientasi Pelayanan Dosen</title>
                <style>
                    body { 
                        font-family: Arial, sans-serif; 
                        margin: 20px; 
                        line-height: 1.4;
                    }
                    .print-header { 
                        text-align: center; 
                        margin-bottom: 30px; 
                        border-bottom: 2px solid #333;
                        padding-bottom: 20px;
                    }
                    table { 
                        width: 100%; 
                        border-collapse: collapse; 
                        margin: 20px 0;
                        font-size: 12px;
                    }
                    th, td { 
                        border: 1px solid #ddd; 
                        padding: 6px; 
                        text-align: left; 
                    }
                    th { 
                        background-color: #f5f5f5; 
                        font-weight: bold;
                        text-align: center;
                    }
                    .badge { 
                        padding: 2px 6px; 
                        border-radius: 3px; 
                        font-size: 10px;
                        border: 1px solid #ccc;
                    }
                    .text-center { text-align: center; }
                    .font-weight-bold { font-weight: bold; }
                    .print-footer {
                        margin-top: 30px;
                        text-align: center;
                        font-size: 11px;
                        color: #666;
                        border-top: 1px solid #ddd;
                        padding-top: 15px;
                    }
                    @media print {
                        body { margin: 15px; }
                        .print-header { page-break-after: avoid; }
                    }
                </style>
            </head>
            <body>
                ${printContent}
            </body>
        </html>
    `);
        printWindow.document.close();
        printWindow.print();
    }

    /**
     * Utility functions for better UX
     */
    function showSuccess(title, text) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'success',
                title: title,
                text: text,
                timer: 3000,
                showConfirmButton: false
            });
        } else {
            alert(title + ': ' + text);
        }
    }

    function showInfo(title, text) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'info',
                title: title,
                text: text
            });
        } else {
            alert(title + ': ' + text);
        }
    }

    // Initialize page-specific functionality
    $(document).ready(function() {
        // Add tooltips for readonly indicators
        $('.readonly-score-display').each(function() {
            $(this).attr('title', 'Data nilai angket bersifat readonly dan tidak dapat diubah')
                .attr('data-toggle', 'tooltip')
                .attr('data-placement', 'top');
        });

        // Refresh tooltips
        if (typeof $().tooltip === 'function') {
            $('[data-toggle="tooltip"]').tooltip();
        }

        // Auto-refresh notification (optional)
        <?php if (isset($calculationResult) && ($calculationResult['added'] > 0 || $calculationResult['updated'] > 0)): ?>
            setTimeout(() => {
                showSuccess('Data Diperbarui',
                    'Data orientasi pelayanan telah disinkronkan dan skor dihitung ulang secara otomatis.'
                );
            }, 1000);
        <?php endif; ?>
    });
</script>

<style>
    /* Enhanced styles for readonly interface */
    .readonly-score-display {
        transition: all 0.3s ease;
    }

    .readonly-score-display:hover {
        transform: scale(1.05);
    }

    .readonly-score-display .badge {
        transition: all 0.3s ease;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .readonly-score-display:hover .badge {
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
    }

    /* Search enhancements */
    #searchInput {
        border-width: 2px;
        transition: all 0.3s ease;
    }

    #searchInput:focus {
        border-color: #007bff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, .25);
    }

    /* Print-specific styles */
    @media print {

        .btn,
        .card-tools,
        .card-header .card-tools,
        .alert,
        .breadcrumb,
        #searchInput,
        .card-footer {
            display: none !important;
        }

        .card {
            border: none !important;
            box-shadow: none !important;
        }

        .table {
            font-size: 11px;
        }

        .readonly-score-display .badge {
            border: 1px solid #000 !important;
            background-color: #f8f9fa !important;
            color: #000 !important;
        }
    }

    /* Loading overlay styles */
    .loading-spinner {
        display: inline-block;
        width: 20px;
        height: 20px;
        border: 3px solid rgba(255, 255, 255, .3);
        border-radius: 50%;
        border-top-color: #fff;
        animation: spin 1s ease-in-out infinite;
    }

    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }

    /* Table enhancements */
    .table tbody tr:hover {
        background-color: rgba(0, 123, 255, 0.05);
    }

    .table .badge {
        font-size: 0.8rem;
        padding: 0.3rem 0.5rem;
    }

    /* No results styling */
    #no-results-row td {
        background-color: #f8f9fa;
        border-left: 4px solid #17a2b8;
    }
</style>
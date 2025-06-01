<script>
    $(document).ready(function() {
        // Initialize any DataTable features if needed
        if (typeof $.fn.DataTable !== 'undefined') {
            // Optional: Enable advanced table features
            // $('#skpTable').DataTable({
            //     "paging": false,
            //     "lengthChange": false,
            //     "searching": false, // We use custom search
            //     "ordering": true,
            //     "info": false,
            //     "autoWidth": false,
            //     "responsive": true,
            // });
        }

        // Enhanced search functionality
        $('#searchInput').on('keyup', function() {
            const value = $(this).val().toLowerCase();
            let visibleCount = 0;

            $('table tbody tr').filter(function() {
                const isVisible = $(this).text().toLowerCase().indexOf(value) > -1;
                $(this).toggle(isVisible);
                if (isVisible) visibleCount++;
                return isVisible;
            });

            // Update row numbers after filtering
            updateRowNumbers();

            // Show/hide "no results" message
            if (visibleCount === 0 && value.trim() !== '') {
                showNoResultsMessage();
            } else {
                hideNoResultsMessage();
            }
        });

        // Function to update row numbers after filtering
        function updateRowNumbers() {
            $('table tbody tr:visible').each(function(index) {
                if (!$(this).hasClass('no-results-row')) {
                    $(this).find('td:first').text(index + 1);
                }
            });
        }

        // Show no results message
        function showNoResultsMessage() {
            if ($('.no-results-row').length === 0) {
                const colCount = $('table thead tr th').length;
                const noResultsRow = `
                <tr class="no-results-row">
                    <td colspan="${colCount}" class="text-center py-4">
                        <i class="fas fa-search fa-2x text-muted mb-2"></i>
                        <p class="text-muted mb-0">Tidak ada data yang sesuai dengan pencarian</p>
                    </td>
                </tr>
            `;
                $('table tbody').append(noResultsRow);
            }
        }

        // Hide no results message
        function hideNoResultsMessage() {
            $('.no-results-row').remove();
        }

        // Auto-refresh functionality
        function autoRefresh() {
            // Optional: Auto-refresh every 10 minutes
            setTimeout(function() {
                if (confirm('Refresh data SKP untuk memperbarui skor terbaru dari semua komponen?')) {
                    refreshSKPData();
                }
            }, 600000); // 10 minutes
        }

        // Initialize auto-refresh (uncomment if needed)
        // autoRefresh();

        // Initialize Select2 for filters if available
        if (typeof $.fn.select2 !== 'undefined') {
            $('.select2').select2({
                theme: 'bootstrap4',
                width: '100%'
            });
        }

        // Filter form auto-submit
        $('#position, #study_program, #skp_category').on('change', function() {
            $('#filterForm').submit();
        });

        // Add loading states to buttons
        $('.btn[href*="export"]').on('click', function() {
            const $btn = $(this);
            const originalText = $btn.html();

            $btn.html('<i class="fas fa-spinner fa-spin mr-1"></i> Memproses...')
                .prop('disabled', true);

            // Re-enable after 3 seconds (adjust based on actual export time)
            setTimeout(() => {
                $btn.html(originalText).prop('disabled', false);
            }, 3000);
        });

        // Semester selector functionality
        $('#semester_select').on('change', function() {
            const semesterId = $(this).val();
            if (semesterId && confirm(
                    'Ganti ke semester yang dipilih? Data yang ditampilkan akan berubah.')) {
                $(this).closest('form').submit();
            }
        });

        // Enhanced tooltips for SKP scores
        $('[data-toggle="tooltip"]').tooltip();

        // Add hover effects for table rows
        $('table tbody tr').hover(
            function() {
                $(this).addClass('table-active');
            },
            function() {
                $(this).removeClass('table-active');
            }
        );

        // Smooth scroll to statistics when clicking summary cards
        $('.small-box').on('click', function() {
            $('html, body').animate({
                scrollTop: $('#componentChart').offset().top - 100
            }, 500);
        });

        // Print functionality
        $('#printSKPData').on('click', function() {
            window.print();
        });

        // Keyboard shortcuts
        $(document).on('keydown', function(e) {
            // Ctrl + F to focus search
            if (e.ctrlKey && e.keyCode === 70) {
                e.preventDefault();
                $('#searchInput').focus();
            }

            // Ctrl + R to refresh
            if (e.ctrlKey && e.keyCode === 82) {
                e.preventDefault();
                refreshSKPData();
            }

            // Escape to clear search
            if (e.keyCode === 27) {
                $('#searchInput').val('').trigger('keyup');
            }
        });

        // Check for updates notification
        function checkForUpdates() {
            // This could be implemented to check if data has been updated
            // and show a notification to refresh
            $.ajax({
                url: '<?= base_url('skp/check-updates') ?>',
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.has_updates) {
                        showUpdateNotification();
                    }
                },
                error: function() {
                    // Silently fail for update checks
                }
            });
        }

        // Show update notification
        function showUpdateNotification() {
            if ($('#updateNotification').length === 0) {
                const notification = `
                <div id="updateNotification" class="alert alert-info alert-dismissible fade show position-fixed" 
                     style="top: 20px; right: 20px; z-index: 9999; max-width: 300px;">
                    <i class="fas fa-info-circle mr-1"></i>
                    Data SKP telah diperbarui. 
                    <button type="button" class="btn btn-sm btn-info ml-2" onclick="refreshSKPData()">
                        Refresh
                    </button>
                    <button type="button" class="close" data-dismiss="alert">
                        <span>&times;</span>
                    </button>
                </div>
            `;
                $('body').append(notification);

                // Auto-hide after 10 seconds
                setTimeout(() => {
                    $('#updateNotification').fadeOut();
                }, 10000);
            }
        }

        // Initialize update checking (uncomment if needed)
        // setInterval(checkForUpdates, 300000); // Check every 5 minutes

        // Statistics card interactions
        $('.card .card-header').on('click', function() {
            const card = $(this).closest('.card');
            const body = card.find('.card-body');

            if (body.is(':visible')) {
                body.slideUp();
                $(this).find('i').removeClass('fa-minus').addClass('fa-plus');
            } else {
                body.slideDown();
                $(this).find('i').removeClass('fa-plus').addClass('fa-minus');
            }
        });
    });

    // Global function for refreshing SKP data
    function refreshSKPData() {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Memuat Data...',
                text: 'Sedang menyinkronkan data SKP dan menghitung ulang skor dari semua komponen penilaian',
                allowOutsideClick: false,
                showConfirmButton: false,
                willOpen: () => {
                    Swal.showLoading();
                }
            });

            // Add progress indication
            let progress = 0;
            const progressInterval = setInterval(() => {
                progress += 10;
                if (progress <= 90) {
                    Swal.update({
                        text: `Memproses data... ${progress}%`
                    });
                }
            }, 200);

            // Clear interval and refresh after 2 seconds
            setTimeout(() => {
                clearInterval(progressInterval);
                window.location.reload();
            }, 2000);
        } else {
            // Fallback without SweetAlert
            const $refreshBtn = $('button[onclick="refreshSKPData()"]');
            const originalText = $refreshBtn.html();

            $refreshBtn.html('<i class="fas fa-spinner fa-spin mr-1"></i> Memuat...')
                .prop('disabled', true);

            setTimeout(() => {
                window.location.reload();
            }, 1500);
        }
    }

    // Export functionality helpers
    function exportToExcel() {
        const currentParams = new URLSearchParams(window.location.search);
        window.location.href = '<?= base_url('skp/export-excel') ?>' + '?' + currentParams.toString();
    }

    function exportToPdf() {
        const currentParams = new URLSearchParams(window.location.search);
        window.location.href = '<?= base_url('skp/export-pdf') ?>' + '?' + currentParams.toString();
    }

    // Manual recalculate scores
    function recalculateScores() {
        if (confirm('Yakin ingin menghitung ulang semua skor SKP? Proses ini mungkin memakan waktu beberapa saat.')) {
            window.location.href = '<?= base_url('skp/recalculate-scores') ?>';
        }
    }

    // Helper function to format numbers
    function formatNumber(num, decimals = 1) {
        return parseFloat(num).toFixed(decimals);
    }

    // Helper function to get badge class based on score
    function getBadgeClass(score) {
        if (score >= 88) return 'badge-success';
        if (score >= 76) return 'badge-primary';
        if (score >= 61) return 'badge-warning';
        return 'badge-danger';
    }

    // Helper function to get status text based on score
    function getStatusText(score) {
        if (score >= 88) return 'Sangat Baik';
        if (score >= 76) return 'Baik';
        if (score >= 61) return 'Cukup';
        return 'Kurang';
    }

    // Performance monitoring (for development)
    if (typeof console !== 'undefined' && console.time) {
        console.time('SKP Page Load');
        $(window).on('load', function() {
            console.timeEnd('SKP Page Load');
            console.log('SKP data count:', $('table tbody tr').length - $('.no-results-row').length);
        });
    }
</script>

<!-- Print styles -->
<style media="print">
    .no-print,
    .card-tools,
    .btn,
    .alert,
    .breadcrumb {
        display: none !important;
    }

    .card {
        border: none !important;
        box-shadow: none !important;
    }

    .table {
        font-size: 12px;
    }

    .badge {
        background: #000 !important;
        color: #fff !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }

    @page {
        margin: 1cm;
    }

    .print-header {
        text-align: center;
        margin-bottom: 20px;
    }

    .print-header h2 {
        margin: 0;
        font-size: 18px;
    }

    .print-footer {
        position: fixed;
        bottom: 0;
        width: 100%;
        text-align: center;
        font-size: 10px;
        border-top: 1px solid #000;
        padding-top: 5px;
    }
</style>

<!-- Add print header for when printing -->
<div class="print-header no-print d-none">
    <h2>Data Master SKP Dosen Fakultas</h2>
    <p>Semester <?= $currentSemester['year'] ?>/<?= $currentSemester['term'] === '1' ? 'Ganjil' : 'Genap' ?></p>
    <p>Dicetak pada: <?= date('d/m/Y H:i:s') ?></p>
</div>

<div class="print-footer d-none">
    Sistem Penilaian Kinerja (SKP) Dosen - Universitas Pembangunan Nasional "Veteran" Jawa Timur
</div>
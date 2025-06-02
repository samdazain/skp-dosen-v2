<script>
    $(document).ready(function() {
        // Add CSRF token to all AJAX requests
        $.ajaxSetup({
            beforeSend: function(xhr, settings) {
                if (!/^(GET|HEAD|OPTIONS|TRACE)$/i.test(settings.type) && !this.crossDomain) {
                    xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");
                }
            }
        });

        // Handle dropdown change with visual feedback
        $('.cooperation-level-dropdown').on('change', function() {
            const $select = $(this);
            const newValue = $select.val();

            // Update dropdown styling immediately for visual feedback
            updateDropdownStyle($select, newValue);
        });
    });

    function confirmCooperationChange(lecturerId, level) {
        const levelLabels = {
            'not_cooperative': 'Tidak Kooperatif',
            'fair': 'Cukup Kooperatif',
            'cooperative': 'Kooperatif',
            'very_cooperative': 'Sangat Kooperatif'
        };

        const levelText = levelLabels[level] || level;
        const lecturerName = $(`tr:has([data-lecturer-id="${lecturerId}"])`).find('strong').text();

        Swal.fire({
            title: 'Konfirmasi Perubahan',
            text: `Apakah Anda yakin ingin mengubah level kerjasama "${lecturerName}" menjadi ${levelText}?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Ubah!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                updateCooperationLevel(lecturerId, level);
            } else {
                // Reset the dropdown to previous state
                revertCooperationDropdown(lecturerId);
            }
        });
    }

    function updateCooperationLevel(lecturerId, level) {
        // Show loading
        Swal.fire({
            title: 'Memperbarui...',
            text: 'Sedang memperbarui level kerjasama dan menghitung ulang nilai berdasarkan database',
            allowOutsideClick: false,
            showConfirmButton: false,
            willOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: '<?= base_url('cooperation/update-level') ?>',
            method: 'POST',
            data: {
                lecturer_id: lecturerId,
                level: level
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: response.message + ` (Nilai baru: ${response.new_score})`,
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        // Update the score and status in the table
                        updateTableRow(lecturerId, response.new_score, level);
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: response.message || 'Terjadi kesalahan saat memperbarui level kerjasama'
                    });
                    // Reset dropdown
                    revertCooperationDropdown(lecturerId);
                }
            },
            error: function(xhr, status, error) {
                Swal.close();
                let errorMessage = 'Terjadi kesalahan sistem. Silakan coba lagi.';

                // Try to get specific error message from response
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                } else if (xhr.responseText) {
                    try {
                        const response = JSON.parse(xhr.responseText);
                        if (response.message) {
                            errorMessage = response.message;
                        }
                    } catch (e) {
                        console.error('Non-JSON error response:', xhr.responseText);
                    }
                }

                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: errorMessage,
                    footer: '<small>Periksa pengaturan skor di menu Settings jika masalah berlanjut</small>'
                });
                // Reset dropdown
                revertCooperationDropdown(lecturerId);
            }
        });
    }

    function revertCooperationDropdown(lecturerId) {
        // Get the dropdown for this lecturer
        const $dropdown = $(`.cooperation-level-dropdown[data-lecturer-id="${lecturerId}"]`);

        if ($dropdown.length) {
            // Get the original value from the selected option or from data attribute
            const originalValue = $dropdown.data('original-value') || $dropdown.find('option:selected').val();

            // Reset the dropdown
            $dropdown.val(originalValue);

            // Update styling
            updateDropdownStyle($dropdown, originalValue);
        }
    }

    function updateTableRow(lecturerId, newScore, level) {
        // Update score
        $(`#score_${lecturerId}`).text(newScore);

        // Update status badge based on new score
        const $statusCell = $(`#status_${lecturerId}`);
        let statusLabel, badgeClass, scoreClass;

        if (newScore >= 90) {
            statusLabel = 'Sangat Baik';
            badgeClass = 'badge-success';
            scoreClass = 'text-success';
        } else if (newScore >= 76) {
            statusLabel = 'Baik';
            badgeClass = 'badge-primary';
            scoreClass = 'text-primary';
        } else if (newScore >= 61) {
            statusLabel = 'Cukup';
            badgeClass = 'badge-warning';
            scoreClass = 'text-warning';
        } else {
            statusLabel = 'Kurang';
            badgeClass = 'badge-danger';
            scoreClass = 'text-danger';
        }

        // Update score color
        $(`#score_${lecturerId}`).removeClass('text-success text-primary text-warning text-danger').addClass(scoreClass);

        // Update status badge
        $statusCell.html(`<span class="badge ${badgeClass}">${statusLabel}</span>`);

        // Store the current value as original for future reversion
        const $dropdown = $(`.cooperation-level-dropdown[data-lecturer-id="${lecturerId}"]`);
        $dropdown.data('original-value', level);

        // Update dropdown styling
        updateDropdownStyle($dropdown, level);
    }

    function updateDropdownStyle($select, value) {
        // Remove all color classes
        $select.removeClass('border-danger border-warning border-info border-success text-danger text-warning text-info text-success');

        // Apply color based on cooperation level
        switch (value) {
            case 'not_cooperative':
                $select.addClass('border-danger text-danger');
                break;
            case 'fair':
                $select.addClass('border-warning text-warning');
                break;
            case 'cooperative':
                $select.addClass('border-info text-info');
                break;
            case 'very_cooperative':
                $select.addClass('border-success text-success');
                break;
        }
    }

    // Search functionality
    $('#searchInput').on('keyup', function() {
        const value = $(this).val().toLowerCase();
        $('table tbody tr').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        });
    });

    // Function to refresh cooperation data manually
    function refreshCooperationData() {
        Swal.fire({
            title: 'Memuat Data...',
            text: 'Sedang menyinkronkan data kerjasama dosen dan menghitung ulang skor',
            allowOutsideClick: false,
            showConfirmButton: false,
            willOpen: () => {
                Swal.showLoading();
            }
        });

        setTimeout(() => {
            window.location.reload();
        }, 1000);
    }
</script>
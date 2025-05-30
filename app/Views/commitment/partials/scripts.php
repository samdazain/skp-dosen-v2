<script>
    // Real-time score update functions with automatic recalculation

    function confirmCompetencyChange(lecturerId, status) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Konfirmasi Perubahan',
                text: `Ubah status kompetensi menjadi ${status ? 'Aktif' : 'Tidak Aktif'}?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Ubah!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    updateCompetencyAjax(lecturerId, status);
                } else {
                    // Revert radio button selection if cancelled
                    revertRadioSelection('competency_' + lecturerId, !status);
                }
            });
        } else {
            // Fallback for browsers without SweetAlert
            if (confirm(`Ubah status kompetensi menjadi ${status ? 'Aktif' : 'Tidak Aktif'}?`)) {
                updateCompetencyAjax(lecturerId, status);
            } else {
                revertRadioSelection('competency_' + lecturerId, !status);
            }
        }
    }

    function confirmTriDharmaChange(lecturerId, status) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Konfirmasi Perubahan',
                text: `Ubah status Tri Dharma menjadi ${status ? 'Lulus' : 'Tidak Lulus'}?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Ubah!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    updateTriDharmaAjax(lecturerId, status);
                } else {
                    // Revert radio button selection if cancelled
                    revertRadioSelection('tri_dharma_' + lecturerId, !status);
                }
            });
        } else {
            // Fallback for browsers without SweetAlert
            if (confirm(`Ubah status Tri Dharma menjadi ${status ? 'Lulus' : 'Tidak Lulus'}?`)) {
                updateTriDharmaAjax(lecturerId, status);
            } else {
                revertRadioSelection('tri_dharma_' + lecturerId, !status);
            }
        }
    }

    function updateCompetencyAjax(lecturerId, status) {
        // Show loading indicator
        showLoadingIndicator(`Memperbarui kompetensi...`);

        $.ajax({
            url: '<?= base_url('commitment/update-competency') ?>',
            type: 'POST',
            data: {
                lecturer_id: lecturerId,
                status: status ? 'true' : 'false',
                '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
            },
            dataType: 'json',
            success: function(response) {
                hideLoadingIndicator();

                if (response.success) {
                    // Update the score display immediately
                    updateScoreDisplay(lecturerId, response.new_score);

                    // Show success message
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: 'Berhasil!',
                            text: response.message + ` Skor baru: ${response.new_score}`,
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    }

                    // Update statistics if needed
                    updateStatisticsDisplay();

                } else {
                    // Revert radio button selection on failure
                    revertRadioSelection('competency_' + lecturerId, !status);

                    if (typeof Swal !== 'undefined') {
                        Swal.fire('Error!', response.message, 'error');
                    } else {
                        alert('Error: ' + response.message);
                    }
                }
            },
            error: function(xhr, status, error) {
                hideLoadingIndicator();
                console.error('AJAX Error:', error);

                // Revert radio button selection on error
                revertRadioSelection('competency_' + lecturerId, !status);

                if (typeof Swal !== 'undefined') {
                    Swal.fire('Error!', 'Terjadi kesalahan saat memperbarui data', 'error');
                } else {
                    alert('Terjadi kesalahan saat memperbarui data');
                }
            }
        });
    }

    function updateTriDharmaAjax(lecturerId, status) {
        // Show loading indicator
        showLoadingIndicator(`Memperbarui Tri Dharma...`);

        $.ajax({
            url: '<?= base_url('commitment/update-tri-dharma') ?>',
            type: 'POST',
            data: {
                lecturer_id: lecturerId,
                status: status ? 'true' : 'false',
                '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
            },
            dataType: 'json',
            success: function(response) {
                hideLoadingIndicator();

                if (response.success) {
                    // Update the score display immediately
                    updateScoreDisplay(lecturerId, response.new_score);

                    // Show success message
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: 'Berhasil!',
                            text: response.message + ` Skor baru: ${response.new_score}`,
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    }

                    // Update statistics if needed
                    updateStatisticsDisplay();

                } else {
                    // Revert radio button selection on failure
                    revertRadioSelection('tri_dharma_' + lecturerId, !status);

                    if (typeof Swal !== 'undefined') {
                        Swal.fire('Error!', response.message, 'error');
                    } else {
                        alert('Error: ' + response.message);
                    }
                }
            },
            error: function(xhr, status, error) {
                hideLoadingIndicator();
                console.error('AJAX Error:', error);

                // Revert radio button selection on error
                revertRadioSelection('tri_dharma_' + lecturerId, !status);

                if (typeof Swal !== 'undefined') {
                    Swal.fire('Error!', 'Terjadi kesalahan saat memperbarui data', 'error');
                } else {
                    alert('Terjadi kesalahan saat memperbarui data');
                }
            }
        });
    }

    function updateScoreDisplay(lecturerId, newScore) {
        // Find the row for this lecturer
        const row = $(`input[name^="competency_${lecturerId}"]`).closest('tr');

        if (row.length) {
            // Update score column (ensure we have a valid score)
            const scoreCell = row.find('td').eq(6); // 7th column (0-indexed)
            const displayScore = newScore > 0 ? newScore : 70; // Minimum fallback score

            scoreCell.text(displayScore);

            // Update score color class
            scoreCell.removeClass('text-success text-primary text-warning text-danger');
            if (displayScore >= 90) {
                scoreCell.addClass('text-success');
            } else if (displayScore >= 76) {
                scoreCell.addClass('text-primary');
            } else if (displayScore >= 61) {
                scoreCell.addClass('text-warning');
            } else {
                scoreCell.addClass('text-danger');
            }

            // Update status badge
            const statusCell = row.find('td').eq(7); // 8th column (0-indexed)
            const statusBadge = statusCell.find('.badge');

            statusBadge.removeClass('badge-success badge-primary badge-warning badge-danger');
            if (displayScore >= 90) {
                statusBadge.addClass('badge-success').text('Sangat Baik');
            } else if (displayScore >= 76) {
                statusBadge.addClass('badge-primary').text('Baik');
            } else if (displayScore >= 61) {
                statusBadge.addClass('badge-warning').text('Cukup');
            } else {
                statusBadge.addClass('badge-danger').text('Kurang');
            }

            // Add brief highlight effect
            row.addClass('table-warning');
            setTimeout(() => {
                row.removeClass('table-warning');
            }, 1500);
        }
    }

    function revertRadioSelection(groupName, correctValue) {
        const radios = $(`input[name="${groupName}"]`);
        radios.each(function() {
            if ((correctValue && this.value === 'yes') ||
                (correctValue && this.value === 'pass') ||
                (!correctValue && this.value === 'no') ||
                (!correctValue && this.value === 'fail')) {
                this.checked = true;
                $(this).parent().addClass('active');
            } else {
                this.checked = false;
                $(this).parent().removeClass('active');
            }
        });
    }

    function showLoadingIndicator(message = 'Memproses...') {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: message,
                allowOutsideClick: false,
                showConfirmButton: false,
                willOpen: () => {
                    Swal.showLoading();
                }
            });
        }
    }

    function hideLoadingIndicator() {
        if (typeof Swal !== 'undefined') {
            Swal.close();
        }
    }

    function updateStatisticsDisplay() {
        // Optionally reload statistics section via AJAX
        // This can be implemented if you want to update statistics in real-time
        setTimeout(() => {
            location.reload(); // Simple approach - reload page to refresh statistics
        }, 1000);
    }

    // Search functionality
    $(document).ready(function() {
        $('#searchInput').on('keyup', function() {
            const value = $(this).val().toLowerCase();
            $('table tbody tr').filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
            });
        });
    });
</script>
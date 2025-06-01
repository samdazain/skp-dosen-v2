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
    });

    function confirmCompetencyChange(lecturerId, isActive) {
        const statusText = isActive ? 'Aktif' : 'Tidak Aktif';
        const lecturerName = $(`tr:has([name="competency_${lecturerId}"])`).find('strong').text();

        Swal.fire({
            title: 'Konfirmasi Perubahan',
            text: `Apakah Anda yakin ingin mengubah status kompetensi "${lecturerName}" menjadi ${statusText}?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Ubah!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                updateCompetency(lecturerId, isActive);
            } else {
                // Reset the radio button to previous state
                revertCompetencyRadio(lecturerId, !isActive);
            }
        });
    }

    function confirmTriDharmaChange(lecturerId, isPassing) {
        const statusText = isPassing ? 'Lulus' : 'Tidak Lulus';
        const lecturerName = $(`tr:has([name="tri_dharma_${lecturerId}"])`).find('strong').text();

        Swal.fire({
            title: 'Konfirmasi Perubahan',
            text: `Apakah Anda yakin ingin mengubah status Tri Dharma "${lecturerName}" menjadi ${statusText}?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Ubah!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                updateTriDharma(lecturerId, isPassing);
            } else {
                // Reset the radio button to previous state
                revertTriDharmaRadio(lecturerId, !isPassing);
            }
        });
    }

    function updateCompetency(lecturerId, status) {
        // Show loading
        Swal.fire({
            title: 'Memperbarui...',
            text: 'Sedang memperbarui status kompetensi dan menghitung ulang nilai berdasarkan database',
            allowOutsideClick: false,
            showConfirmButton: false,
            willOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: '<?= base_url('commitment/update-competency') ?>',
            method: 'POST',
            data: {
                lecturer_id: lecturerId,
                status: status
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
                        // Auto-refresh page to show updated scores
                        window.location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: response.message || 'Terjadi kesalahan saat memperbarui status kompetensi'
                    });
                    // Reset radio button
                    revertCompetencyRadio(lecturerId, !status);
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
                        // If response is not JSON, show generic error
                        console.error('Non-JSON error response:', xhr.responseText);
                    }
                }

                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: errorMessage,
                    footer: '<small>Periksa pengaturan skor di menu Settings jika masalah berlanjut</small>'
                });
                // Reset radio button
                revertCompetencyRadio(lecturerId, !status);
            }
        });
    }

    function updateTriDharma(lecturerId, status) {
        // Show loading
        Swal.fire({
            title: 'Memperbarui...',
            text: 'Sedang memperbarui status Tri Dharma dan menghitung ulang nilai berdasarkan database',
            allowOutsideClick: false,
            showConfirmButton: false,
            willOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: '<?= base_url('commitment/update-tridharma') ?>',
            method: 'POST',
            data: {
                lecturer_id: lecturerId,
                status: status
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
                        // Auto-refresh page to show updated scores
                        window.location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: response.message || 'Terjadi kesalahan saat memperbarui status Tri Dharma'
                    });
                    // Reset radio button
                    revertTriDharmaRadio(lecturerId, !status);
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
                        // If response is not JSON, show generic error
                        console.error('Non-JSON error response:', xhr.responseText);
                    }
                }

                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: errorMessage,
                    footer: '<small>Periksa pengaturan skor di menu Settings jika masalah berlanjut</small>'
                });
                // Reset radio button
                revertTriDharmaRadio(lecturerId, !status);
            }
        });
    }

    function revertCompetencyRadio(lecturerId, wasActive) {
        if (wasActive) {
            $(`input[name="competency_${lecturerId}"][value="yes"]`).prop('checked', true).closest('label').addClass('active');
            $(`input[name="competency_${lecturerId}"][value="no"]`).prop('checked', false).closest('label').removeClass('active');
        } else {
            $(`input[name="competency_${lecturerId}"][value="no"]`).prop('checked', true).closest('label').addClass('active');
            $(`input[name="competency_${lecturerId}"][value="yes"]`).prop('checked', false).closest('label').removeClass('active');
        }
    }

    function revertTriDharmaRadio(lecturerId, wasPassing) {
        if (wasPassing) {
            $(`input[name="tri_dharma_${lecturerId}"][value="pass"]`).prop('checked', true).closest('label').addClass('active');
            $(`input[name="tri_dharma_${lecturerId}"][value="fail"]`).prop('checked', false).closest('label').removeClass('active');
        } else {
            $(`input[name="tri_dharma_${lecturerId}"][value="fail"]`).prop('checked', true).closest('label').addClass('active');
            $(`input[name="tri_dharma_${lecturerId}"][value="pass"]`).prop('checked', false).closest('label').removeClass('active');
        }
    }

    // Search functionality
    $('#searchInput').on('keyup', function() {
        const value = $(this).val().toLowerCase();
        $('table tbody tr').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        });
    });

    // Function to refresh commitment data manually
    function refreshCommitmentData() {
        Swal.fire({
            title: 'Memuat Data...',
            text: 'Sedang menyinkronkan data komitmen dosen dan menghitung ulang skor',
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
/**
 * Semester Selector JavaScript
 * Handles semester selection with SweetAlert confirmation modal
 */
document.addEventListener('DOMContentLoaded', function () {
    const semesterSelector = document.querySelector('.semester-selector');

    if (!semesterSelector) {
        console.log('Semester selector not found on this page');
        return;
    }

    // Check if SweetAlert2 is available
    if (typeof Swal === 'undefined') {
        console.error('SweetAlert2 is not loaded. Please include SweetAlert2 library.');
        // Fallback to simple confirm dialogs
        window.Swal = {
            fire: function (options) {
                if (typeof options === 'string') {
                    alert(options);
                } else if (options.title && options.text) {
                    return Promise.resolve({ isConfirmed: confirm(options.title + '\n' + options.text) });
                } else {
                    alert(options.title || 'Notification');
                }
                return Promise.resolve({ isConfirmed: false });
            },
            showLoading: function () { },
        };
    }

    // Get configuration from data attributes
    const baseUrl = semesterSelector.dataset.baseUrl;
    const activeSemesterId = semesterSelector.dataset.activeSemesterId;
    const csrfToken = semesterSelector.dataset.csrfToken;
    const csrfName = semesterSelector.dataset.csrfName;

    // Get additional data attributes
    const currentMonth = semesterSelector.dataset.currentMonth;
    const currentYear = semesterSelector.dataset.currentYear;

    console.log('Semester selector loaded');
    console.log('Base URL:', baseUrl);
    console.log('Active semester ID:', activeSemesterId);
    console.log('Current month:', currentMonth);
    console.log('Current year:', currentYear);

    // Log the semester selection logic
    if (currentMonth) {
        const monthInt = parseInt(currentMonth);
        let expectedTerm, expectedYear;

        if (monthInt >= 7) {
            expectedTerm = '2';
            expectedYear = currentYear;
            console.log('Expected semester: Genap (term 2) for year', expectedYear, '(month >= 7)');
        } else if (monthInt >= 2) {
            expectedTerm = '1';
            expectedYear = currentYear;
            console.log('Expected semester: Ganjil (term 1) for year', expectedYear, '(month >= 2)');
        } else {
            expectedTerm = '2';
            expectedYear = parseInt(currentYear) - 1;
            console.log('Expected semester: Genap (term 2) for year', expectedYear, '(January)');
        }
    }

    // Handle semester option clicks
    const semesterOptions = document.querySelectorAll('.semester-option');
    semesterOptions.forEach(option => {
        option.addEventListener('click', function (e) {
            e.preventDefault();

            const semesterId = this.dataset.semesterId;
            const semesterText = this.dataset.semesterText;

            console.log('Semester option clicked:', semesterId, semesterText);

            // Don't show modal if selecting the same semester
            if (semesterId == activeSemesterId) {
                console.log('Same semester selected, skipping');
                // Close dropdown
                if (typeof $ !== 'undefined') {
                    $('.dropdown-toggle').dropdown('hide');
                }
                // Show info message
                showToast('Semester ini sudah dipilih sebagai semester aktif.', 'info');
                return;
            }

            // Close dropdown first
            if (typeof $ !== 'undefined') {
                $('.dropdown-toggle').dropdown('hide');
            }

            // Show SweetAlert confirmation modal
            Swal.fire({
                title: 'Konfirmasi Perubahan Semester',
                html: `
                    <div class="text-center mb-3">
                        <i class="fas fa-calendar-alt text-warning" style="font-size: 3rem;"></i>
                    </div>
                    <p class="mb-3">Anda akan mengubah semester aktif menjadi:</p>
                    <div class="alert alert-info">
                        <strong>${semesterText}</strong>
                    </div>
                    <p class="text-muted small">
                        <i class="fas fa-info-circle mr-1"></i>
                        Perubahan ini akan mempengaruhi data yang ditampilkan di seluruh sistem.
                    </p>
                `,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ffc107',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fas fa-check mr-1"></i> Ya, Ubah Semester',
                cancelButtonText: '<i class="fas fa-times mr-1"></i> Batal',
                reverseButtons: true,
                customClass: {
                    popup: 'swal2-popup-custom',
                    confirmButton: 'btn btn-warning',
                    cancelButton: 'btn btn-secondary'
                },
                buttonsStyling: false,
                allowOutsideClick: false,
                allowEscapeKey: false
            }).then((result) => {
                if (result.isConfirmed) {
                    changeSemester(semesterId, semesterText);
                }
            });
        });
    });

    function changeSemester(semesterId, semesterText) {
        // Show loading state
        Swal.fire({
            title: 'Mengubah Semester...',
            text: 'Mohon tunggu sebentar',
            icon: 'info',
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        const currentUrl = window.location.href;

        // Make AJAX request to change semester
        fetch(baseUrl + 'semester/change', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                semester_id: semesterId,
                redirect: currentUrl
            })
        })
            .then(response => {
                console.log('Response status:', response.status);
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('Response data:', data);

                if (data.status === 'success') {
                    // Update dropdown button text
                    updateDropdownUI(semesterId, data.formattedSemester);

                    // Wait 2 seconds before showing success message to let loading complete
                    setTimeout(() => {
                        // Show success message
                        showToast(data.message || 'Semester berhasil diubah', 'success');

                        // Update active semester ID for future comparisons
                        semesterSelector.dataset.activeSemesterId = semesterId;

                        // Refresh data based on new semester
                        setTimeout(() => {
                            refreshPageData(semesterId);
                        }, 2000);
                    }, 2000);
                } else {
                    throw new Error(data.message || 'Gagal mengubah semester');
                }
            })
            .catch(error => {
                console.error('Error changing semester:', error);

                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Mengubah Semester',
                    text: error.message || 'Terjadi kesalahan saat mengubah semester',
                    confirmButtonColor: '#dc3545',
                    confirmButtonText: 'Tutup'
                });
            });
    }

    function updateDropdownUI(newActiveSemesterId, formattedSemester) {
        // Update dropdown button text
        const dropdownButton = semesterSelector.querySelector('.dropdown-toggle .semester-text');
        if (dropdownButton && formattedSemester) {
            dropdownButton.textContent = formattedSemester;
        }

        // Update active state in dropdown menu
        semesterSelector.querySelectorAll('.semester-option').forEach(menuItem => {
            const itemId = menuItem.dataset.semesterId;
            const icon = menuItem.querySelector('i');
            const badge = menuItem.querySelector('.badge');

            if (itemId === newActiveSemesterId) {
                // Set as active
                menuItem.classList.add('active', 'bg-primary', 'text-white');
                if (icon) {
                    icon.className = 'fas fa-check-circle text-success fa-lg';
                }
                // Add active badge if not present
                if (!badge) {
                    const newBadge = document.createElement('span');
                    newBadge.className = 'badge badge-success ml-2';
                    newBadge.textContent = 'Aktif';
                    menuItem.appendChild(newBadge);
                }
            } else {
                // Remove active state
                menuItem.classList.remove('active', 'bg-primary', 'text-white');
                if (icon) {
                    icon.className = 'far fa-circle text-muted fa-lg';
                }
                // Remove active badge
                if (badge) {
                    badge.remove();
                }
            }
        });
    }
});

/**
 * Refresh page data based on selected semester
 * Each page should implement this function to update its content
 */
function refreshPageData(semesterId) {
    // Check if dataTable exists (for pages with data tables)
    if (typeof dataTable !== 'undefined') {
        // If it's a DataTable page, just reload the table
        dataTable.ajax.reload();

        // Show additional info toast
        setTimeout(() => {
            Swal.fire({
                icon: 'info',
                title: 'Data Dimuat Ulang',
                text: 'Data tabel telah diperbarui sesuai semester yang dipilih',
                timer: 2000,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        }, 1000);
    } else {
        // For other pages, reload the entire page
        setTimeout(() => {
            location.reload();
        }, 1000);
    }
}

/**
 * Show a toast notification using SweetAlert
 */
function showToast(message, type = 'info') {
    console.log('Showing toast:', type, message);

    // Check if SweetAlert2 is available
    if (typeof Swal === 'undefined') {
        // Fallback to simple alert
        alert(message);
        return;
    }

    // Map types to SweetAlert icons
    const iconMap = {
        'info': 'info',
        'success': 'success',
        'error': 'error',
        'warning': 'warning'
    };

    Swal.fire({
        icon: iconMap[type] || 'info',
        title: message,
        timer: 3000,
        showConfirmButton: false,
        toast: true,
        position: 'top-end'
    });
}
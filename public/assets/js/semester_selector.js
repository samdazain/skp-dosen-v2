/**
 * Semester Selector JavaScript
 * Handles AJAX semester changes without full page reload
 */
document.addEventListener('DOMContentLoaded', function () {
    // Get semester selector dropdown
    const semesterDropdown = document.querySelector('.semester-selector');

    if (semesterDropdown) {
        // Add click event to semester dropdown items
        semesterDropdown.querySelectorAll('.dropdown-item').forEach(item => {
            item.addEventListener('click', function (e) {
                e.preventDefault();

                const semesterId = this.dataset.semesterId;
                const semesterText = this.dataset.semesterText;
                const currentUrl = window.location.href;

                // Make AJAX request to change semester
                fetch(baseUrl + '/semester/change', {
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
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            // Update dropdown button text
                            const dropdownButton = semesterDropdown.querySelector('.dropdown-toggle');
                            dropdownButton.textContent = data.formattedSemester;

                            // Update active state in dropdown menu
                            semesterDropdown.querySelectorAll('.dropdown-item').forEach(menuItem => {
                                if (menuItem.dataset.semesterId === semesterId) {
                                    menuItem.classList.add('active');

                                    // Add check icon if not present
                                    if (!menuItem.querySelector('.fa-check')) {
                                        const icon = document.createElement('i');
                                        icon.className = 'fas fa-check ml-2';
                                        menuItem.appendChild(icon);
                                    }
                                } else {
                                    menuItem.classList.remove('active');

                                    // Remove check icon if present
                                    const checkIcon = menuItem.querySelector('.fa-check');
                                    if (checkIcon) {
                                        checkIcon.remove();
                                    }
                                }
                            });

                            // Hide dropdown
                            $('.dropdown-toggle').dropdown('hide');

                            // Refresh data based on new semester
                            refreshPageData(semesterId);
                        } else {
                            console.error('Error changing semester:', data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
            });
        });
    }
});

/**
 * Refresh page data based on selected semester
 * Each page should implement this function to update its content
 */
function refreshPageData(semesterId) {
    // Default implementation reloads the page
    // Individual pages can override this function for custom behavior

    // Check if dataTable exists (for pages with data tables)
    if (typeof dataTable !== 'undefined') {
        // If it's a DataTable page, just reload the table
        dataTable.ajax.reload();

        // Show success message
        showToast('Semester berhasil diubah', 'success');
    } else {
        // For other pages, reload the entire page
        location.reload();
    }
}

/**
 * Show a toast notification
 */
function showToast(message, type = 'info') {
    // Check if toastr library is available
    if (typeof toastr !== 'undefined') {
        toastr[type](message);
    } else {
        // Fallback alert for pages without toastr
        if (type === 'error') {
            alert('Error: ' + message);
        } else {
            alert(message);
        }
    }
}
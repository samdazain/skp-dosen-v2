function confirmDelete(id) {
    // Set the delete URL dynamically
    document.getElementById('confirmDeleteBtn').href = `${baseUrl}/lecturers/delete/${id}`;
    // Show the modal
    $('#deleteModal').modal('show');
}

// Search functionality
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('searchInput');

    searchInput.addEventListener('keyup', function () {
        const searchTerm = this.value.toLowerCase();
        const tableRows = document.querySelectorAll('tbody tr');

        tableRows.forEach(row => {
            const name = row.cells[1].textContent.toLowerCase();
            const nip = row.cells[2].textContent.toLowerCase();
            const program = row.cells[3].textContent.toLowerCase();
            const position = row.cells[4].textContent.toLowerCase();

            if (name.includes(searchTerm) || nip.includes(searchTerm) ||
                program.includes(searchTerm) || position.includes(searchTerm)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
});
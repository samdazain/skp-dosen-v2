document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('searchInput');

    searchInput.addEventListener('keyup', function () {
        const searchTerm = this.value.toLowerCase();
        const tableRows = document.querySelectorAll('tbody tr');

        tableRows.forEach(row => {
            const name = row.cells[1].textContent.toLowerCase();
            const nip = row.cells[2].textContent.toLowerCase();
            const program = row.cells[3].textContent.toLowerCase();

            if (name.includes(searchTerm) || nip.includes(searchTerm) || program.includes(
                searchTerm)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
});
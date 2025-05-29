$(document).ready(function () {
    initializeLecturerTable();
});

function initializeLecturerTable() {
    initializeTooltips();
    initializePerPageSelector();
    initializeSortableHeaders();
}

function initializeTooltips() {
    $('[data-toggle="tooltip"]').tooltip();
}

function initializePerPageSelector() {
    $('#perPageSelect').on('change', function () {
        const url = new URL(window.location);
        url.searchParams.set('per_page', this.value);
        url.searchParams.delete('page');
        window.location.href = url.toString();
    });
}

function initializeSortableHeaders() {
    $('.sortable-header').hover(
        function () {
            $(this).addClass('bg-light');
        },
        function () {
            $(this).removeClass('bg-light');
        }
    );
}

function confirmDelete(id, name) {
    Swal.fire({
        title: 'Konfirmasi Hapus',
        html: createDeleteConfirmationHtml(name),
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="fas fa-trash mr-1"></i> Ya, Hapus!',
        cancelButtonText: '<i class="fas fa-times mr-1"></i> Batal',
        reverseButtons: true,
        customClass: {
            popup: 'animated bounceIn'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            showDeleteProgress();
            redirectToDelete(id);
        }
    });
}

function createDeleteConfirmationHtml(name) {
    return `
        <div class="text-center">
            <i class="fas fa-exclamation-triangle text-warning fa-3x mb-3"></i>
            <p>Apakah Anda yakin ingin menghapus data dosen:</p>
            <h5 class="text-danger font-weight-bold">${name}</h5>
            <small class="text-muted">Tindakan ini tidak dapat dibatalkan!</small>
        </div>
    `;
}

function showDeleteProgress() {
    Swal.fire({
        title: 'Menghapus Data...',
        html: 'Sedang memproses permintaan Anda',
        allowOutsideClick: false,
        allowEscapeKey: false,
        showConfirmButton: false,
        customClass: {
            popup: 'animated pulse'
        },
        didOpen: () => {
            Swal.showLoading();
        }
    });
}

function redirectToDelete(id) {
    window.location.href = `<?= base_url('lecturers/delete/') ?>${id}`;
}

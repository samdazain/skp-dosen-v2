<div class="btn-group" role="group">
    <a href="<?= base_url('lecturers/edit/' . $lecturer['id']) ?>"
        class="btn btn-sm btn-warning" title="Edit Data" data-toggle="tooltip">
        <i class="fas fa-edit"></i>
    </a>
    <button type="button" class="btn btn-sm btn-danger" title="Hapus Data" data-toggle="tooltip"
        onclick="confirmDelete('<?= $lecturer['id'] ?>', '<?= esc($lecturer['name']) ?>')">
        <i class="fas fa-trash"></i>
    </button>
</div>
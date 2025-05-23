document.addEventListener('DOMContentLoaded', function () {
    // User listing page functionality
    $('#deleteModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var id = button.data('id');
        var name = button.data('name');

        var modal = $(this);
        modal.find('#delete-item-name').text(name);
        modal.find('#delete-form').attr('action', baseUrl + '/user/delete/' + id);
    });
});
$(document).on('click', '.modal-link', function (e) {
    e.preventDefault();
    var url = $(this).attr('href');
    var modal = $('#exampleModal');
    modal.find('#modalContent').load(url, function () {
        modal.modal('show');
    });
});
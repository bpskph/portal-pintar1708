$(document).ready(function () {
    $(document.body).on('change', '#zooms-jenis_surat', function () {
        var val = $('#zooms-jenis_surat').val();
        if (val == 0) {
            $('#suratrepo').show();
            $('#suratrepoeks').hide();
        }
        else {
            $('#suratrepo').hide();
            $('#suratrepoeks').show();
        }
    });
});
$(document).ready(function () {
    $(document.body).on('change', '#suratrepo-jenis', function () {
        var val = $('#suratrepo-jenis').val();
        if (val == 0 ) {
            $('#biasa').show();
            $('#notadinas').hide();
            $('#keterangan').hide();
            $('#bast').hide();
            $('#pihak').hide();
            $('#ttdby').show();
            $('#biasa_file').show();
            $('#notadinas_file').hide();
            $('#keterangan_file').hide();
            $('#bast_file').hide();
        }
        else if (val == 1){
            $('#biasa').hide();
            $('#notadinas').show();
            $('#keterangan').hide();
            $('#bast').hide();
            $('#pihak').hide();
            $('#ttdby').show();
            $('#biasa_file').hide();
            $('#notadinas_file').show();
            $('#keterangan_file').hide();
            $('#bast_file').hide();
        }
        else if (val == 2){
            $('#biasa').hide();
            $('#notadinas').hide();
            $('#keterangan').show();
            $('#bast').hide();
            $('#pihak').hide();
            $('#ttdby').show();
            $('#biasa_file').hide();
            $('#notadinas_file').hide();
            $('#keterangan_file').show();
            $('#bast_file').hide();
        }
        else if (val == 3){
            $('#biasa').hide();
            $('#notadinas').hide();
            $('#keterangan').hide();
            $('#bast').show();           
            $('#pihak').show();
            $('#ttdby').hide();
            $('#biasa_file').hide();
            $('#notadinas_file').hide();
            $('#keterangan_file').hide();
            $('#bast_file').show();
        }
        else if (val == 4){
            $('#biasa').hide();
            $('#notadinas').hide();
            $('#keterangan').hide();
            $('#bast').hide();           
            $('#pihak').hide();
            $('#ttdby').show();
            $('#biasa_file').hide();
            $('#notadinas_file').hide();
            $('#_file').hide();
            $('#bast_file').hide();
        }
    });
});

$(document).ready(function () {
    $(document.body).on('change', '#suratrepo-lampiran', function () {
        var val = $('#suratrepo-lampiran').val();
        if (val === '' || val === '-') {
            $('#isilampiran').hide();
        } else {
            $('#isilampiran').show();
        }    
    });
});

if (actionId === 'update') {
    // Set the input value to the value of the waktuselesai attribute
    flatpickr('#suratrepo-tanggal_suratrepo', {
        enableTime: false,
        dateFormat: "Y-m-d",
        maxDate: "today", // Set the minimum date to today
        onReady: function(selectedDates, dateStr, instance) {
            var formattedDate = instance.formatDate(selectedDates[0], "Y-m-d");
            instance.input.value = formattedDate;
        }
    });
} else {
    flatpickr("#suratrepo-tanggal_suratrepo", {
        enableTime: false,
        dateFormat: "Y-m-d",
        maxDate: "today", // Set the minimum date to today
        defaultDate: new Date(),
        onReady: function(selectedDates, dateStr, instance) {
            var formattedDate = instance.formatDate(selectedDates[0], "Y-m-d");
            instance.input.value = formattedDate;
        }
    });
}
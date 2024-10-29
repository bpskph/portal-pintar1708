$(document).ready(function () {
    $(document.body).on('change', '#suratrepoeks-jenis', function () {
        var val = $('#suratrepoeks-jenis').val();
        if (val == 0) {
            $('#biasa').show();
            $('#lembur').hide();
            $('#keterangan').hide();
            $('#bast').hide();
            $('#biasa_file').show();
            $('#lembur_file').hide();
            $('#keterangan_file').hide();
            $('#bast_file').hide();
        } else if (val == 1) {
            $('#biasa').hide();
            $('#lembur').show();
            $('#keterangan').hide();
            $('#bast').hide();
            $('#biasa_file').hide();
            $('#lembur_file').show();
            $('#keterangan_file').hide();
            $('#bast_file').hide();
        } else if (val == 2) {
            $('#biasa').hide();
            $('#lembur').hide();
            $('#keterangan').show();
            $('#bast').hide();
            $('#biasa_file').hide();
            $('#lembur_file').hide();
            $('#keterangan_file').show();
            $('#bast_file').hide();
        } else if (val == 3) {
            $('#biasa').hide();
            $('#lembur').hide();
            $('#keterangan').hide();
            $('#bast').show();
            $('#biasa_file').hide();
            $('#lembur_file').hide();
            $('#keterangan_file').hide();
            $('#bast_file').show();
        } else {
            $('#biasa').hide();
            $('#lembur').hide();
            $('#keterangan').hide();
            $('#bast').hide();
            $('#biasa_file').hide();
            $('#lembur_file').hide();
            $('#keterangan_file').hide();
            $('#bast_file').hide();
        }
    });
});

$(document).ready(function () {
    $(document.body).on('change', '#suratrepoeks-lampiran', function () {
        var val = $('#suratrepoeks-lampiran').val();
        if (val === '' || val === '-') {
            $('#isilampiran').hide();
        } else {
            $('#isilampiran').show();
        }
    });
});

if (actionId === 'update') {
    // Set the input value to the value of the waktuselesai attribute
    flatpickr('#suratrepoeks-tanggal_suratrepoeks', {
        enableTime: false,
        dateFormat: "Y-m-d",
        maxDate: "today", // Set the minimum date to today
        onReady: function (selectedDates, dateStr, instance) {
            var formattedDate = instance.formatDate(selectedDates[0], "Y-m-d");
            instance.input.value = formattedDate;
        }
    });
} else {
    flatpickr("#suratrepoeks-tanggal_suratrepoeks", {
        enableTime: false,
        dateFormat: "Y-m-d",
        defaultDate: new Date(),
        maxDate: "today", // Set the minimum date to today
        onReady: function (selectedDates, dateStr, instance) {
            var formattedDate = instance.formatDate(selectedDates[0], "Y-m-d");
            instance.input.value = formattedDate;
        }
    });
}
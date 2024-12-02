// untuk pilih member tim sebagai peserta agenda
$(document).ready(function () {
    $('#team-checkboxes input[type="checkbox"]').change(function () {
        var selectedTeams = $('#team-checkboxes input[type="checkbox"]:checked').map(function () {
            return this.value;
        }).get();
        
        // Check if "all" (value 0) is selected
        if (selectedTeams.includes("0")) {
            selectedTeams = ["all"]; // Send "all" as the value
        }

        $.ajax({
            url: window.location.href + "/../../agenda/getlistpeserta",
            type: 'POST',
            data: {
                teams: selectedTeams
            },
            dataType: 'json',
            success: function (response) {
                // Update the chosen members
                var chosenMembers = response.members;
                $('#agenda-peserta').val(chosenMembers).trigger('change');
            }
        });
    });
});

if (actionId === 'create') {
    var d = document.getElementById("team-checkboxes");
    d.className += " row";
}
flatpickr('#calendar-tomorrow', {
    "minDate": new Date().fp_incr(1),
    "enableTime": true
});

//untuk pilih pelaksana (external atau internal)
$('#pilihpelaksana-switch').on('change', function () {
    if ($(this).prop('checked')) {
        $('#pelaksana-external').hide();
        $('#pelaksana-internal').show();
    } else {
        $('#pelaksana-internal').hide();
        $('#pelaksana-external').show();
    }
});

// buat input waktu
// console.log(actionId);
if (actionId === 'update') {
    // Set the input value to the value of the waktuselesai attribute
    flatpickr('#agenda-waktumulai', {
        enableTime: true,
        dateFormat: "Y-m-d H:i",
        onReady: function (selectedDates, dateStr, instance) {
            var formattedDate = instance.formatDate(selectedDates[0], "Y-m-d H:i") + " WIB";
            instance.input.value = formattedDate;
        }
    });
    flatpickr('#agenda-waktuselesai', {
        enableTime: true,
        dateFormat: "Y-m-d H:i",
        onReady: function (selectedDates, dateStr, instance) {
            var formattedDate = instance.formatDate(selectedDates[0], "Y-m-d H:i") + " WIB";
            instance.input.value = formattedDate;
        }
    });
} else {
    flatpickr("#agenda-waktumulai", {
        enableTime: true,
        dateFormat: "Y-m-d H:i",
        defaultDate: new Date(),
        onReady: function (selectedDates, dateStr, instance) {
            var formattedDate = instance.formatDate(selectedDates[0], "Y-m-d H:i") + " WIB";
            instance.input.value = formattedDate;
        }
    });
    flatpickr("#agenda-waktuselesai", {
        enableTime: true,
        dateFormat: "Y-m-d H:i",
        defaultDate: new Date(),
        onReady: function (selectedDates, dateStr, instance) {
            var formattedDate = instance.formatDate(selectedDates[0], "Y-m-d H:i") + " WIB";
            instance.input.value = formattedDate;
        }
    });
}

$(document).ready(function () {
    $(document.body).on('change', '#agenda-metode', function () {
        var val = $('#agenda-metode').val();
        if (val == 0) {
            $('#petunjuk-zoom').show();
        }
        else {
            $('#petunjuk-zoom').hide();
        }
    });
});
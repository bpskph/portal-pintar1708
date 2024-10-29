flatpickr('#dl-tanggal_mulai', {
    enableTime: false,
    dateFormat: "Y-m-d",
    onReady: function (selectedDates, dateStr, instance) {
        var formattedDate = instance.formatDate(selectedDates[0], "Y-m-d");
        instance.input.value = formattedDate;
    }
}); 
flatpickr('#dl-tanggal_selesai', {
    enableTime: false,
    dateFormat: "Y-m-d",
    onReady: function (selectedDates, dateStr, instance) {
        var formattedDate = instance.formatDate(selectedDates[0], "Y-m-d");
        instance.input.value = formattedDate;
    }
}); 
flatpickr('#sk-tanggal_sk', {
    enableTime: false,
    dateFormat: "Y-m-d",
    onReady: function (selectedDates, dateStr, instance) {
        var formattedDate = instance.formatDate(selectedDates[0], "Y-m-d");
        instance.input.value = formattedDate;
    }
}); 
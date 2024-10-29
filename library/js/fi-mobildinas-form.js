
if (actionId === 'update') {
    // Set the input value to the value of the selesai attribute
    flatpickr('#mobildinas-mulai', {
        enableTime: true,
        dateFormat: "Y-m-d H:i",
        onReady: function (selectedDates, dateStr, instance) {
            var formattedDate = instance.formatDate(selectedDates[0], "Y-m-d H:i") + " WIB";
            instance.input.value = formattedDate;
        }
    });
    flatpickr('#mobildinas-selesai', {
        enableTime: true,
        dateFormat: "Y-m-d H:i",
        onReady: function (selectedDates, dateStr, instance) {
            var formattedDate = instance.formatDate(selectedDates[0], "Y-m-d H:i") + " WIB";
            instance.input.value = formattedDate;
        }
    });
    console.log(actionId);
} else {
    flatpickr("#mobildinas-mulai", {
        enableTime: true,
        dateFormat: "Y-m-d H:i",
        defaultDate: new Date(),
        onReady: function (selectedDates, dateStr, instance) {
            var formattedDate = instance.formatDate(selectedDates[0], "Y-m-d H:i") + " WIB";
            instance.input.value = formattedDate;
        }
    });
    flatpickr("#mobildinas-selesai", {
        enableTime: true,
        dateFormat: "Y-m-d H:i",
        defaultDate: new Date(),
        onReady: function (selectedDates, dateStr, instance) {
            var formattedDate = instance.formatDate(selectedDates[0], "Y-m-d H:i") + " WIB";
            instance.input.value = formattedDate;
        }
    });
}
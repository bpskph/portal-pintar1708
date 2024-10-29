// Get the action ID from the view
var actionId = '<?php echo Yii::$app->controller->action->id; ?>';
if (actionId === 'update') {
    // Set the input value to the value of the waktuselesai attribute
    flatpickr('#agendapimpinan-waktumulai', {
        enableTime: true,
        dateFormat: "Y-m-d H:i",
        onReady: function (selectedDates, dateStr, instance) {
            var formattedDate = instance.formatDate(selectedDates[0], "Y-m-d H:i") + " WIB";
            instance.input.value = formattedDate;
        }
    });
    flatpickr('#agendapimpinan-waktuselesai', {
        enableTime: true,
        dateFormat: "Y-m-d H:i",
        onReady: function (selectedDates, dateStr, instance) {
            var formattedDate = instance.formatDate(selectedDates[0], "Y-m-d H:i") + " WIB";
            instance.input.value = formattedDate;
        }
    });
} else {
    flatpickr("#agendapimpinan-waktumulai", {
        enableTime: true,
        dateFormat: "Y-m-d H:i",
        defaultDate: new Date(),
        onReady: function (selectedDates, dateStr, instance) {
            var formattedDate = instance.formatDate(selectedDates[0], "Y-m-d H:i") + " WIB";
            instance.input.value = formattedDate;
        }
    });
    flatpickr("#agendapimpinan-waktuselesai", {
        enableTime: true,
        dateFormat: "Y-m-d H:i",
        defaultDate: new Date(),
        onReady: function (selectedDates, dateStr, instance) {
            var formattedDate = instance.formatDate(selectedDates[0], "Y-m-d H:i") + " WIB";
            instance.input.value = formattedDate;
        }
    });
}
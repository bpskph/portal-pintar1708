// document.addEventListener('DOMContentLoaded', function () {
//     var calendarEl = document.getElementById('calendar');
//     var calendar = new FullCalendar.Calendar(calendarEl, {
//         initialView: 'multiMonthYear',
//         locale: 'id', // Set your desired locale
//         events: eventsKalender,
//         selectable: true,
//     });
//     calendar.render();
// });
document.addEventListener('DOMContentLoaded', function () {
    var calendarEl = document.getElementById('calendar');
    var eventDetails = document.getElementById('event-details');

    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'id',
        events: eventsKalender,
        selectable: true,
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay,multiMonthYear'
        },
        views: {
            multiMonthYear: {
                type: 'multiMonthYear',
                duration: { months: 12 }
            }
        },
        eventClick: function(info) {
            eventDetails.classList.remove('show');

            setTimeout(function() {
                document.getElementById('project').innerText = info.event.extendedProps.project || 'N/A';
                document.getElementById('kegiatan').innerText = info.event.title || 'N/A';
                document.getElementById('leader').innerText = info.event.extendedProps.leader || 'N/A';
                document.getElementById('reporter').innerText = info.event.extendedProps.reporter || 'N/A';
                document.getElementById('waktu').innerText = info.event.extendedProps.waktu || 'N/A';
                document.getElementById('detail').innerHTML = info.event.extendedProps.detail || 'N/A';

                eventDetails.classList.add('show');
            }, 500);
        }
    });

    calendar.render();
});

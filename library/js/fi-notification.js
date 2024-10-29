function handleMarkAsReadAndView(event, element) {
    event.preventDefault(); // Prevent the default link behavior

    var markAsReadUrl = $(element).data('mark-as-read-url');
    var viewUrl = $(element).data('view-url');

    $.ajax({
        url: markAsReadUrl,
        type: 'POST',
        data: {
            _csrf: yii.getCsrfToken() // Include CSRF token if needed
        },
        success: function(response) {
            // Redirect to the view URL after marking as read
            window.location.href = viewUrl;
        },
        error: function(xhr, status, error) {
            // Handle any errors
            console.log('AJAX error:', status, error);
        }
    });
}
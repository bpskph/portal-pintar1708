$(function () {
    $('.link-click').on('click contextmenu', function (e) {
        e.preventDefault(); // prevent default link behavior
        // var linkId = $(this).closest('td').data('col-seq') - 1; // get the row column sequence
        var linkId = $(this).data('link-id'); // get the link ID from data attribute
        var url = baseUrl + '/linkmat/updateviews?id=' + linkId; // replace with your controller and action URL
        var csrfToken = $('meta[name="csrf-token"]').attr("content"); // get the CSRF token from meta tags
        $.ajax({
            url: url,
            type: 'POST',
            data: { id: linkId, _csrf: csrfToken },
            success: function (data) {
                // update the views column on the clicked row
                var viewsCell = $(e.target).closest('tr').find('.views-column');
                var viewsValue = parseInt(viewsCell.text()) + 1;
                viewsCell.text(viewsValue);
                // open the link in a new tab
                if (e.which === 1) { // left click
                    window.open($(e.target).attr('href'), '_blank');
                } else if (e.which === 3) { // right click
                    window.location.href = $(e.target).attr('href');
                }
            },
            error: function (xhr, status, error) {
                console.error("Error Data:", xhr.responseText); // Log the error data
                console.error("Status: " + status);
                console.error("Error: " + error);
            }
        });
    });
});
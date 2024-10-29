div = document.getElementById('linkappsearch-keyword');
div.classList.add('row');
div.classList.add('justify-content-center');
// const checkboxes = document.querySelectorAll('.bs5-form-check');
// for (let i = 0; i < checkboxes.length; i++) {
//     checkboxes[i].classList.add('me-1', 'mr-1', 'bs-checkbox', 'col-md-2');
// }

$(document).ready(function () {
    $('.bs5-form-check.form-check input[type="checkbox"]').each(function () {
        var container = $(this).closest('.bs5-form-check.form-check');
        if (container) {
            container.addClass('me-1 mr-1 bs-checkbox col-md-2');
        }
    });
});

$(document).on('click', '.link-click', function (e) {
    e.preventDefault();
    var linkId = $(this).data('link-id'); // Corrected to use data() method
    var url = baseUrl + '/linkapp/updateviews?id=' + linkId;
    var csrfToken = $('meta[name="csrf-token"]').attr("content");

    $.ajax({
        url: url,
        type: 'POST',
        data: { id: linkId, _csrf: csrfToken },
        success: function () {
            // update the views column in the card's footer
            var card = document.getElementById(linkId).getAttribute("href");
            // open the link in a new tab
            if (e.which === 1) { // left click
                window.open(card, '_blank');
            } else if (e.which === 3) { // right click
                window.open(card, '_blank');
            }
        },
        error: function (xhr, status, error) {
            console.error(xhr.responseText);
            console.error("Status: " + status);
            console.error("Error: " + error);
        }
    });
});

$(function () {
    $('.link-click-image').on('click contextmenu', function (e) {
        e.preventDefault(); // prevent default link behavior
        var linkId = $(this).data('link-id'); // get the link ID from data attribute
        var url = baseUrl + '/linkapp/updateviews?id=' + linkId; // replace with your controller and action URL
        var csrfToken = $('meta[name="csrf-token"]').attr("content"); // get the CSRF token from meta tags
        $.ajax({
            url: url,
            type: 'POST',
            data: { id: linkId, _csrf: csrfToken },
            success: function () {
                // update the views column in the card's footer
                var card = document.getElementById(linkId).getAttribute("href");
                // open the link in a new tab
                if (e.which === 1) { // left click
                    // console.log(card);
                    window.open(card, '_blank');
                } else if (e.which === 3) { // right click
                    window.open(card, '_blank');
                }
            },
            error: function (xhr, status, error) {
                console.error(xhr.responseText);
                console.error("Status: " + status);
                console.error("Error: " + error);
            }
        });
    });
});
$(function() {
    $('.link-click').on('click contextmenu', function(e) {
        e.preventDefault(); // prevent default link behavior
        var linkId = document.getElementById("ambilID").innerHTML;
        console.log(linkId);
        var url = baseUrl + '/linkmat/updateviews?id=' + linkId; // replace with your controller and action URL
        $.ajax({
            url: url,
            type: 'POST',
            success: function(data) {
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
            }
        });
    });
});
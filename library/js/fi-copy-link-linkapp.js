$(document).on('click', '.copy-link-button', function (event) {
    var content = $(this).data('content');
    // Create a temporary textarea to copy the content to
    var tempTextArea = $('<textarea>');
    tempTextArea.val(content);
    // Append the textarea to the body and select its content
    $('body').append(tempTextArea);
    tempTextArea.select();
    // Copy the content to the clipboard
    document.execCommand('copy');
    // Remove the temporary textarea
    tempTextArea.remove();
    // Show a notification or perform any other action to indicate successful copy
    // alert('Link telah disalin');
    customAlert('Link telah disalin (copied)', '2000');
    // Prevent the link from navigating
    event.preventDefault();
});

function customAlert(msg, duration) {
    var styler = document.createElement("div");
    styler.setAttribute("style", "border-radius: 10px; border: 2px solid silver; width: auto; height: auto; position: fixed; top: 200px; left: 50%; transform: translateX(-50%); background-color: #EFFBFB; color: #798eb3; padding: 10px; text-align: center;");
    styler.innerHTML = "<h4 style='margin-bottom: 0rem'>" + msg + "</h4>";
    setTimeout(function () {
        styler.parentNode.removeChild(styler);
    }, duration);
    document.body.appendChild(styler);
}

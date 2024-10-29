function resizePdfIframe() {
    var windowHeight = $(window).height();
    var pdfIframeOffset = $('#pdf-container').offset().top;
    var pdfIframeHeight = windowHeight - pdfIframeOffset - 20; // subtract 20 for margin
    $('#pdf-iframe').height(pdfIframeHeight);
}
$(window).resize(function () {
    resizePdfIframe();
});
$(document).ready(function () {
    resizePdfIframe();
});
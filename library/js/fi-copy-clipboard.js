function copyToClipboard(text) {
    var tempInput = document.createElement('input');
    tempInput.style = 'position: absolute; left: -1000px; top: -1000px';
    tempInput.value = text;
    document.body.appendChild(tempInput);
    tempInput.select();
    document.execCommand('copy');
    document.body.removeChild(tempInput);
    // alert('Copied to clipboard');
    customAlert('Nomor surat berhasil disalin (copied)', '2000');
}
function customAlert(msg, duration) {
    var styler = document.createElement("div");
    styler.setAttribute("style", "border-radius: 10px; border: 2px solid silver; width: auto; height: auto; position: fixed; top: 200px; left: 50%; transform: translateX(-50%); background-color: #EFFBFB; color: #798eb3; padding: 10px; text-align: center;");
    styler.innerHTML = "<h4 style='margin-bottom: 0rem'>" + msg + "</h4>";
    setTimeout(function() {
        styler.parentNode.removeChild(styler);
    }, duration);
    document.body.appendChild(styler);
}
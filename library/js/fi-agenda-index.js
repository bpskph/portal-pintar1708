document.addEventListener('DOMContentLoaded', function () {
    // Check if the modal should be displayed based on localStorage flag
    if (!localStorage.getItem('modalShown')) {
        // Show the modal
        var myModal = new bootstrap.Modal(document.getElementById('kliksaya'));
        myModal.show();

        // Set the flag in localStorage to indicate that the modal has been shown
        localStorage.setItem('modalShown', 'true');
    }
});
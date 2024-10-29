// console.log(controllerId);

document.addEventListener('DOMContentLoaded', function () {
    // Get the toast element by ID
    var myToast = document.getElementById('myToast');

    if (myToast !== null && myToast !== undefined) {

        // Use setTimeout to hide the toast after 3000 milliseconds (3 seconds)
        if (controllerId == 'suratrepo' || controllerId == 'suratrepoeks') {

            setTimeout(function () {
                // Add the 'show' class to trigger the fade-out effect
                myToast.classList.add('show');

                // Add the 'fade' class to initiate the fade-out transition
                myToast.classList.add('fade');

                // Remove the 'show' class after the transition is complete
                myToast.addEventListener('transitionend', function () {
                    myToast.classList.remove('show');
                });

                // Trigger the click event on the close button
                var closeButton = myToast.querySelector('.btn-close');
                closeButton.click();
            }, 60000);
        }
        else {
            setTimeout(function () {
                // Add the 'show' class to trigger the fade-out effect
                myToast.classList.add('show');

                // Add the 'fade' class to initiate the fade-out transition
                myToast.classList.add('fade');

                // Remove the 'show' class after the transition is complete
                myToast.addEventListener('transitionend', function () {
                    myToast.classList.remove('show');
                });

                // Trigger the click event on the close button
                var closeButton = myToast.querySelector('.btn-close');
                closeButton.click();
            }, 6000);
        }
    }
});
$(document).on('click', '.modal-link', function (e) {
    e.preventDefault();
    var url = $(this).attr('href');
    var modal = $('#exampleModal');
    modal.find('#modalContent').load(url, function () {
        modal.modal('show');
    });
});
    
            


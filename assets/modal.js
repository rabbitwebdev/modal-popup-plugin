jQuery(document).ready(function($) {
    if (localStorage.getItem("modalDismissed") === "true") return;

    const modal = $('#custom-modal');
    const trigger = popupOptions.trigger;
    const delay = popupOptions.delay * 1000;
    const scrollPercent = popupOptions.scroll;

    function showModal() {
        if (!modal.is(":visible")) modal.fadeIn();
    }

    $('.close-button').on('click', function() {
        modal.fadeOut();
        localStorage.setItem("modalDismissed", "true");
    });

    switch (trigger) {
        case 'time':
            setTimeout(showModal, delay);
            break;
        case 'scroll':
            $(window).on('scroll', function() {
                let scrollTop = $(window).scrollTop();
                let docHeight = $(document).height() - $(window).height();
                if ((scrollTop / docHeight) * 100 >= scrollPercent) {
                    showModal();
                }
            });
            break;
        case 'click':
            $(document).on('click', '.show-popup', showModal);
            break;
        case 'exit':
            $(document).on('mouseout', function(e) {
                if (e.clientY < 10) showModal();
            });
            break;
    }
});

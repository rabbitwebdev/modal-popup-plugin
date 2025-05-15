jQuery(document).ready(function($) {
    function setCookie(name, value, days) {
        const d = new Date();
        d.setTime(d.getTime() + (days*24*60*60*1000));
        const expires = "expires=" + d.toUTCString();
        document.cookie = name + "=" + value + ";" + expires + ";path=/";
    }

    function getCookie(name) {
        const cname = name + "=";
        const decodedCookie = decodeURIComponent(document.cookie);
        const ca = decodedCookie.split(';');
        for (let i = 0; i < ca.length; i++) {
            let c = ca[i];
            while (c.charAt(0) === ' ') c = c.substring(1);
            if (c.indexOf(cname) === 0) return c.substring(cname.length, c.length);
        }
        return "";
    }

    if (getCookie("modalDismissed") === "true") return;

    const modal = $('#custom-modal');
    const trigger = popupOptions.trigger;
    const delay = popupOptions.delay * 1000;
    const scrollPercent = popupOptions.scroll;

    function showModal() {
        if (!modal.is(":visible")) modal.fadeIn();
    }

    $('.close-button').on('click', function() {
        modal.fadeOut();
        setCookie("modalDismissed", "true", 2); // Cookie expires in 2 days
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
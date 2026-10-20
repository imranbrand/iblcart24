document.addEventListener('DOMContentLoaded', function () {

    const sidebar = document.getElementById('adminSidebar');

    const overlay = document.getElementById('adminOverlay');

    const menuButton = document.getElementById('adminMenuButton');


    function openSidebar() {

        if (!sidebar || !overlay) {
            return;
        }

        sidebar.classList.add('show');

        overlay.classList.add('show');

        document.body.style.overflow = 'hidden';

    }


    function closeSidebar() {

        if (!sidebar || !overlay) {
            return;
        }

        sidebar.classList.remove('show');

        overlay.classList.remove('show');

        document.body.style.overflow = '';

    }


    if (menuButton) {

        menuButton.addEventListener(
            'click',
            openSidebar
        );

    }


    if (overlay) {

        overlay.addEventListener(
            'click',
            closeSidebar
        );

    }


    document.querySelectorAll(
        '.admin-sidebar a'
    ).forEach(function (link) {

        link.addEventListener(
            'click',
            function () {

                if (
                    window.innerWidth <= 991
                ) {

                    closeSidebar();

                }

            }
        );

    });


    window.addEventListener(
        'resize',
        function () {

            if (
                window.innerWidth > 991
            ) {

                closeSidebar();

            }

        }
    );

});

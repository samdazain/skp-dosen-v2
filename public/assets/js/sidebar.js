$(document).ready(function () {
    // Check if device is mobile
    const isMobile = () => window.innerWidth <= 768;

    // Set active class based on current URL
    const currentPath = window.location.pathname;

    // Flag to check if any link has been marked as active
    let hasActiveLink = false;

    // First pass - try to match exact path
    $('.nav-sidebar .nav-link').each(function () {
        const href = $(this).attr('href');
        if (href === currentPath) {
            $(this).addClass('active');
            hasActiveLink = true;
        }
    });

    // Second pass - if no active link and we're at root or /dashboard, activate dashboard
    if (!hasActiveLink && (currentPath === '/' || currentPath === '/dashboard' || currentPath.startsWith('/dashboard/'))) {
        $('.nav-sidebar .nav-link[href="/dashboard"]').addClass('active');
    }

    // If still no active link, set dashboard as default
    if (!$('.nav-sidebar .nav-link.active').length) {
        $('.nav-sidebar .nav-link[href="/dashboard"]').addClass('active');
    }

    // Sidebar toggle on button click
    $('[data-widget="pushmenu"]').on('click', function (e) {
        e.preventDefault();
        if (isMobile()) {
            $('body').toggleClass('sidebar-open');
        } else {
            $('body').toggleClass('sidebar-collapse');
        }
    });

    // Close sidebar when backdrop is clicked
    $('.sidebar-backdrop').on('click', function () {
        $('body').removeClass('sidebar-open');
    });

    // Close sidebar after navigation on mobile
    $('.nav-sidebar .nav-link').on('click', function () {
        if (isMobile()) {
            setTimeout(() => {
                $('body').removeClass('sidebar-open');
            }, 100);
        }
    });

    // Reset sidebar state when resizing
    $(window).on('resize', function () {
        if (isMobile()) {
            $('body').removeClass('sidebar-collapse');
        }
    });

    // Logout button click handler
    $('#logoutBtn').on('click', function (e) {
        e.preventDefault();
        $('#logoutModal').modal('show');
    });

    // Hover functionality for collapsed sidebar
    if (!isMobile()) {
        let hoverTimeout;

        $('.main-sidebar').on('mouseenter', function () {
            clearTimeout(hoverTimeout);
            if ($('body').hasClass('sidebar-collapse')) {
                $('body').addClass('sidebar-hover');
            }
        });

        $('.main-sidebar').on('mouseleave', function () {
            if ($('body').hasClass('sidebar-collapse')) {
                hoverTimeout = setTimeout(() => {
                    $('body').removeClass('sidebar-hover');
                }, 300); // Delay to make transition smoother
            }
        });
    }
});
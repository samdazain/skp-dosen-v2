$(document).ready(function () {
    // Check if device is mobile
    const isMobile = () => window.innerWidth <= 768;

    // Set active class based on current URL
    const currentPath = window.location.pathname;

    // Flag to check if any link has been marked as active
    let hasActiveLink = false;

    // Improved path matching logic
    $('.nav-sidebar .nav-link').each(function () {
        const href = $(this).attr('href');

        // Skip empty or # hrefs
        if (!href || href === '#') return;

        // Remove trailing slashes for consistent comparison
        const normalizedHref = href.replace(/\/$/, '');
        const normalizedPath = currentPath.replace(/\/$/, '');

        // Check if the current path starts with the menu item's path (for nested routes)
        // But make sure we're matching complete path segments to avoid partial matches
        if (
            // Exact match
            normalizedPath === normalizedHref ||
            // Path starts with href and is followed by a slash or nothing
            (normalizedPath.startsWith(normalizedHref + '/') && normalizedHref !== '/') ||
            // Special case for section roots (like /lecturers, /discipline, etc.)
            (normalizedPath.split('/')[1] === normalizedHref.split('/')[1] && normalizedHref !== '/dashboard')
        ) {
            $(this).addClass('active');
            hasActiveLink = true;

            // If this is a submenu item, also activate parent
            const parentNav = $(this).closest('.has-treeview');
            if (parentNav.length) {
                parentNav.addClass('menu-open');
                parentNav.find('> .nav-link').addClass('active');
            }
        }
    });

    // Only set dashboard as active if we're actually on the dashboard or root
    if (!hasActiveLink && (currentPath === '/' || currentPath === '/dashboard')) {
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
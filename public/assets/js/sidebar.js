$(document).ready(function () {
    // Check if device is mobile
    const isMobile = () => window.innerWidth <= 768;

    // Sidebar state management
    let isHovering = false;
    let hoverTimer = null;
    let isTransitioning = false;

    // Restore sidebar state on page load
    const savedSidebarState = localStorage.getItem('sidebarState');
    const savedActiveNavItem = localStorage.getItem('activeNavItem');

    if (savedSidebarState === 'expanded' && !isMobile()) {
        $('body').removeClass('sidebar-collapse');
    }

    // Set active class based on current URL
    const currentPath = window.location.pathname;
    let hasActiveLink = false;

    // Improved path matching logic with smooth activation animation
    $('.nav-sidebar .nav-link').each(function () {
        const href = $(this).attr('href');

        // Skip empty or # hrefs
        if (!href || href === '#') return;

        // Remove trailing slashes for consistent comparison
        const normalizedHref = href.replace(/\/$/, '');
        const normalizedPath = currentPath.replace(/\/$/, '');

        // Check if the current path matches
        if (
            normalizedPath === normalizedHref ||
            (normalizedPath.startsWith(normalizedHref + '/') && normalizedHref !== '/') ||
            (normalizedPath.split('/')[1] === normalizedHref.split('/')[1] && normalizedHref !== '/dashboard')
        ) {
            $(this).addClass('active');
            hasActiveLink = true;

            // Add activation animation with delay
            setTimeout(() => {
                $(this).addClass('activated');
            }, 150);

            // If this is a submenu item, also activate parent
            const parentNav = $(this).closest('.has-treeview');
            if (parentNav.length) {
                parentNav.addClass('menu-open');
                parentNav.find('> .nav-link').addClass('active');
            }
        }
    });

    // Set dashboard as active if no other link is active
    if (!hasActiveLink && (currentPath === '/' || currentPath === '/dashboard')) {
        const dashboardLink = $('.nav-sidebar .nav-link[href="/dashboard"]');
        dashboardLink.addClass('active');
        setTimeout(() => {
            dashboardLink.addClass('activated');
        }, 150);
    }

    // Enhanced sidebar toggle with smooth animations
    $('[data-widget="pushmenu"]').on('click', function (e) {
        e.preventDefault();

        if (isMobile()) {
            toggleMobileSidebar();
        } else {
            toggleDesktopSidebar();
        }

        // Add ripple effect to toggle button
        createRippleEffect($(this), e);
    });

    // Mobile sidebar toggle
    function toggleMobileSidebar() {
        const body = $('body');
        const sidebar = $('.modern-sidebar');

        if (body.hasClass('sidebar-open')) {
            body.removeClass('sidebar-open');
            sidebar.css('transform', 'translateX(-100%)');
        } else {
            body.addClass('sidebar-open');
            sidebar.css('transform', 'translateX(0)');
        }
    }

    // Desktop sidebar toggle
    function toggleDesktopSidebar() {
        const body = $('body');

        if (isTransitioning) return;
        isTransitioning = true;

        body.toggleClass('sidebar-collapse');

        // Animate nav items when expanding
        if (!body.hasClass('sidebar-collapse')) {
            $('.modern-nav-item').each(function (index) {
                $(this).css({
                    'animation-delay': (index * 0.05) + 's',
                    'animation': 'slideInLeft 1s ease forwards'
                });
            });
        }

        setTimeout(() => {
            isTransitioning = false;
        }, 300);
    }

    // Enhanced nav link interactions
    $('.modern-nav-link').on('click', function (e) {
        const href = $(this).attr('href');
        const isDisabled = $(this).hasClass('nav-disabled');

        // Don't prevent default for actual navigation
        if (href && href !== '#' && !isDisabled) {
            // Save sidebar state and active nav item
            if (!isMobile() && !$('body').hasClass('sidebar-collapse')) {
                localStorage.setItem('sidebarState', 'expanded');
            } else {
                localStorage.removeItem('sidebarState');
            }

            // Save the href of clicked nav item for focus restoration
            localStorage.setItem('activeNavItem', href);

            // Add click animation
            $(this).addClass('nav-clicked');
            setTimeout(() => {
                $(this).removeClass('nav-clicked');
            }, 200);

            // Create ripple effect
            createRippleEffect($(this), e);

            // Close mobile sidebar after navigation
            if (isMobile()) {
                setTimeout(() => {
                    $('body').removeClass('sidebar-open');
                    $('.modern-sidebar').css('transform', 'translateX(-100%)');
                }, 150);
            }
        } else if (isDisabled) {
            // Prevent navigation for disabled items
            e.preventDefault();

            // Show access denied feedback
            showAccessDeniedFeedback($(this));
        }
    });

    // Enhanced hover effects for nav items (desktop only)
    if (!isMobile()) {
        $('.modern-nav-link').on('mouseenter', function () {
            const isDisabled = $(this).hasClass('nav-disabled');

            if (isDisabled) {
                $(this).addClass('nav-disabled-hover');
                showDisabledTooltip($(this));
            } else if (!$(this).hasClass('active')) {
                $(this).addClass('nav-hovered');

                // Animate icon
                const icon = $(this).find('.nav-icon');
                icon.addClass('icon-hover-effect');
            }
        }).on('mouseleave', function () {
            $(this).removeClass('nav-hovered nav-disabled-hover');
            hideDisabledTooltip();

            // Reset icon animation
            const icon = $(this).find('.nav-icon');
            icon.removeClass('icon-hover-effect');
        });
    }

    // Function to show access denied feedback
    function showAccessDeniedFeedback($element) {
        const feedback = $('<div class="access-denied-feedback">Akses Terbatas</div>');
        $element.append(feedback);

        setTimeout(() => {
            feedback.addClass('show');
        }, 10);

        setTimeout(() => {
            feedback.removeClass('show');
            setTimeout(() => {
                feedback.remove();
            }, 300);
        }, 2000);
    }

    // Function to show disabled tooltip
    function showDisabledTooltip($element) {
        if ($('.disabled-tooltip').length > 0) return;

        const tooltip = $('<div class="disabled-tooltip">Akses tidak tersedia untuk role Anda</div>');
        const rect = $element[0].getBoundingClientRect();

        tooltip.css({
            position: 'fixed',
            top: rect.top + rect.height / 2 - 15,
            left: rect.right + 10,
            zIndex: 10000
        });

        $('body').append(tooltip);

        setTimeout(() => {
            tooltip.addClass('show');
        }, 10);
    }

    // Function to hide disabled tooltip
    function hideDisabledTooltip() {
        $('.disabled-tooltip').removeClass('show');
        setTimeout(() => {
            $('.disabled-tooltip').remove();
        }, 300);
    }

    // Restore focus to previously active nav item
    if (savedActiveNavItem) {
        setTimeout(() => {
            const targetNavItem = $(`.modern-nav-link[href="${savedActiveNavItem}"]`);
            if (targetNavItem.length) {
                targetNavItem.focus();
            }
            // Clear the saved item after restoration
            localStorage.removeItem('activeNavItem');
        }, 100);
    }

    // Enhanced hover effects for nav items (desktop only)
    if (!isMobile()) {
        $('.modern-nav-link').on('mouseenter', function () {
            if (!$(this).hasClass('active')) {
                $(this).addClass('nav-hovered');

                // Animate icon
                const icon = $(this).find('.nav-icon');
                icon.addClass('icon-hover-effect');
            }
        }).on('mouseleave', function () {
            $(this).removeClass('nav-hovered');

            // Reset icon animation
            const icon = $(this).find('.nav-icon');
            icon.removeClass('icon-hover-effect');
        });
    }

    // Close sidebar when backdrop is clicked (mobile)
    $(document).on('click', function (e) {
        if (isMobile() && $('body').hasClass('sidebar-open')) {
            const sidebar = $('.modern-sidebar');
            const target = $(e.target);

            if (!sidebar.is(target) && sidebar.has(target).length === 0 && !target.closest('[data-widget="pushmenu"]').length) {
                $('body').removeClass('sidebar-open');
                sidebar.css('transform', 'translateX(-100%)');
            }
        }
    });

    // Reset sidebar state when resizing
    $(window).on('resize', debounce(function () {
        if (isMobile()) {
            $('body').removeClass('sidebar-collapse');
        } else {
            $('body').removeClass('sidebar-open');
            $('.modern-sidebar').css('transform', '');
        }
    }, 150));

    // Enhanced hover functionality for collapsed sidebar (desktop only)
    if (!isMobile()) {
        const sidebar = $('.main-sidebar');

        sidebar.on('mouseenter', function () {
            if (isTransitioning) return;

            clearTimeout(hoverTimer);
            isHovering = true;

            if ($('body').hasClass('sidebar-collapse')) {
                $('body').addClass('sidebar-hover').removeClass('sidebar-collapse');

                // Animate nav items on hover expand with stagger
                $('.modern-nav-item').each(function (index) {
                    const item = $(this);
                    setTimeout(() => {
                        item.addClass('hover-expanded');
                    }, index * 30);
                });
            }
        });

        sidebar.on('mouseleave', function () {
            isHovering = false;

            if ($('body').hasClass('sidebar-hover')) {
                hoverTimer = setTimeout(() => {
                    if (!isHovering && !isTransitioning) {
                        $('body').removeClass('sidebar-hover').addClass('sidebar-collapse');
                        $('.modern-nav-item').removeClass('hover-expanded');
                    }
                }, 10);
            }
        });
    }

    // User panel hover effect
    $('.user-panel').on('mouseenter', function () {
        $(this).addClass('user-panel-hover');
    }).on('mouseleave', function () {
        $(this).removeClass('user-panel-hover');
    });

    // Brand link hover effect
    $('.modern-brand').on('mouseenter', function () {
        $(this).addClass('brand-hover');
    }).on('mouseleave', function () {
        $(this).removeClass('brand-hover');
    });

    // Logout button click handler with enhanced confirmation
    $('#logoutBtn, .logout-item .nav-link').on('click', function (e) {
        e.preventDefault();

        // Add click animation
        $(this).addClass('logout-clicked');

        // Show logout confirmation with custom styling
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Konfirmasi Logout',
                text: 'Apakah Anda yakin ingin keluar dari sistem?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fas fa-sign-out-alt mr-1"></i> Ya, Logout',
                cancelButtonText: '<i class="fas fa-times mr-1"></i> Batal',
                background: '#ffffff',
                backdrop: 'rgba(0,0,0,0.6)',
                customClass: {
                    popup: 'modern-popup',
                    confirmButton: 'modern-confirm-btn',
                    cancelButton: 'modern-cancel-btn'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    // Add loading state
                    Swal.fire({
                        title: 'Logging out...',
                        text: 'Sedang memproses logout',
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        background: '#ffffff',
                        customClass: {
                            popup: 'modern-popup'
                        },
                        willOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // Redirect to logout after animation
                    setTimeout(() => {
                        window.location.href = '/logout';
                    }, 1000);
                }
            });
        } else {
            // Fallback confirmation
            if (confirm('Apakah Anda yakin ingin keluar dari sistem?')) {
                window.location.href = '/logout';
            }
        }

        setTimeout(() => {
            $(this).removeClass('logout-clicked');
        }, 300);
    });

    // Utility function to create ripple effect
    function createRippleEffect(element, event) {
        const ripple = $('<span class="ripple-effect"></span>');
        const rect = element[0].getBoundingClientRect();
        const size = Math.max(rect.width, rect.height);
        const x = event.clientX - rect.left - size / 2;
        const y = event.clientY - rect.top - size / 2;

        ripple.css({
            width: size + 'px',
            height: size + 'px',
            left: x + 'px',
            top: y + 'px'
        });

        element.css('position', 'relative').append(ripple);

        setTimeout(() => {
            ripple.remove();
        }, 600);
    }

    // Debounce function for performance
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    // Initialize tooltips for collapsed sidebar
    function initializeTooltips() {
        if ($('body').hasClass('sidebar-collapse')) {
            $('.modern-nav-link').each(function () {
                const text = $(this).find('.nav-text').text().trim();
                $(this).attr('title', text)
                    .tooltip({
                        placement: 'right',
                        boundary: 'window',
                        trigger: 'hover'
                    });
            });
        } else {
            $('.modern-nav-link').tooltip('dispose').removeAttr('title');
        }
    }

    // Initialize tooltips on page load
    initializeTooltips();

    // Re-initialize tooltips when sidebar state changes
    $('[data-widget="pushmenu"]').on('click', function () {
        setTimeout(initializeTooltips, 350);
    });

    // Add smooth scroll for internal links
    $('a[href^="#"]').on('click', function (e) {
        const href = $(this).attr('href');
        if (href && href.length > 1) {
            const target = $(href);
            if (target.length) {
                e.preventDefault();
                $('html, body').animate({
                    scrollTop: target.offset().top - 100
                }, 600, 'easeInOutCubic');
            }
        }
    });

    // Performance optimization: Use requestAnimationFrame for smooth animations
    function smoothAnimation(callback) {
        requestAnimationFrame(callback);
    }

    // Add loading animation when navigating
    $('.modern-nav-link:not([href="#"])').on('click', function () {
        if (!$(this).hasClass('active')) {
            const loadingOverlay = $('<div class="nav-loading-overlay"><div class="nav-loading-spinner"></div></div>');
            $(this).append(loadingOverlay);

            setTimeout(() => {
                loadingOverlay.remove();
            }, 1000);
        }
    });

    // Keyboard navigation support
    $('.modern-nav-link').on('keydown', function (e) {
        if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            $(this).click();
        }
    });

    // Add focus management for better accessibility
    $('.modern-nav-link').on('focus', function () {
        $(this).addClass('nav-focused');
    }).on('blur', function () {
        $(this).removeClass('nav-focused');
    });

    // Initialize disabled state for restricted menu items
    initializeDisabledMenuItems();

    function initializeDisabledMenuItems() {
        // Mark menu items as disabled based on data attribute from server
        $('.modern-nav-link').each(function () {
            const hasAccess = $(this).data('has-access');

            if (hasAccess === false || hasAccess === 'false') {
                $(this).addClass('nav-disabled')
                    .attr('tabindex', '-1')
                    .css('cursor', 'not-allowed');
            }
        });
    }
});

// Inject additional CSS for disabled menu items and enhanced styling
$('<style>')
    .prop('type', 'text/css')
    .html(`
        .modern-nav-link.nav-disabled {
            opacity: 0.5;
            cursor: not-allowed !important;
            pointer-events: auto;
        }
        
        .modern-nav-link.nav-disabled .nav-text,
        .modern-nav-link.nav-disabled .nav-icon {
            color: #6c757d !important;
        }
        
        .modern-nav-link.nav-disabled-hover {
            background-color: rgba(220, 53, 69, 0.1) !important;
            border-left: 3px solid #dc3545;
        }
        
        .access-denied-feedback {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            background: #dc3545;
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 500;
            opacity: 0;
            transition: opacity 0.3s ease;
            z-index: 1000;
        }
        
        .access-denied-feedback.show {
            opacity: 1;
        }
        
        .disabled-tooltip {
            background: #343a40;
            color: white;
            padding: 8px 12px;
            border-radius: 6px;
            font-size: 0.8rem;
            font-weight: 500;
            opacity: 0;
            transform: translateX(-10px);
            transition: all 0.3s ease;
            pointer-events: none;
            white-space: nowrap;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }
        
        .disabled-tooltip.show {
            opacity: 1;
            transform: translateX(0);
        }
        
        .disabled-tooltip::before {
            content: '';
            position: absolute;
            left: -6px;
            top: 50%;
            transform: translateY(-50%);
            border: 6px solid transparent;
            border-right-color: #343a40;
        }
    `)
    .appendTo('head');
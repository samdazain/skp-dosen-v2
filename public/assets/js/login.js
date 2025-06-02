document.addEventListener('DOMContentLoaded', function () {
    // Auto-focus on first input
    $('#nip').focus();

    // Enhanced input animations
    $('.input-field').each(function () {
        const $input = $(this);
        const $container = $input.closest('.input-container');

        // Check if input has value on load
        if ($input.val().trim() !== '') {
            $container.addClass('focused');
        }

        // Focus events
        $input.on('focus', function () {
            $container.addClass('focused');
            createRippleEffect($container[0]);
        });

        // Blur events
        $input.on('blur', function () {
            if ($input.val().trim() === '') {
                $container.removeClass('focused');
            }
        });

        // Input events for real-time validation
        $input.on('input', function () {
            validateInput($input);
        });
    });

    // Form submission with enhanced loading state
    $('form').on('submit', function (e) {
        const $form = $(this);
        const $submitBtn = $('.login-btn');

        // Basic validation check
        const nipValue = $('#nip').val().trim();
        const passwordValue = $('#password').val().trim();

        if (!nipValue || !passwordValue) {
            e.preventDefault();
            showNotification('Mohon lengkapi semua field terlebih dahulu', 'error');
            return false;
        }

        // Add loading state
        $submitBtn.addClass('loading').prop('disabled', true);

        // Animate other elements
        $('.input-container').each(function (index) {
            setTimeout(() => {
                $(this).addClass('submitting');
            }, index * 100);
        });

        // Show loading notification
        showNotification('Sedang memverifikasi kredensial Anda...', 'info');
    });

    // Enhanced password toggle with animation
    window.togglePassword = function () {
        const $passwordField = $('#password');
        const $eyeIcon = $('#password-eye');
        const $toggle = $('.password-toggle');

        if ($passwordField.attr('type') === 'password') {
            $passwordField.attr('type', 'text');
            $eyeIcon.removeClass('fa-eye').addClass('fa-eye-slash');
            $toggle.addClass('active');
        } else {
            $passwordField.attr('type', 'password');
            $eyeIcon.removeClass('fa-eye-slash').addClass('fa-eye');
            $toggle.removeClass('active');
        }

        // Add click animation
        $toggle.addClass('clicked');
        setTimeout(() => {
            $toggle.removeClass('clicked');
        }, 200);
    };

    // Input validation function
    function validateInput($input) {
        const value = $input.val().trim();
        const $container = $input.closest('.input-container');
        const inputType = $input.attr('name');

        // Remove previous validation classes
        $container.removeClass('error success');

        if (value === '') {
            return false; // Required fields must have values
        }

        let isValid = true;

        switch (inputType) {
            case 'nip':
                // Basic NIP validation (should be numeric and reasonable length)
                if (!/^\d{10,20}$/.test(value)) {
                    isValid = false;
                    showFieldError($container, 'NIP harus berupa angka (10-20 digit)');
                }
                break;

            case 'password':
                // Basic password validation
                if (value.length < 3) {
                    isValid = false;
                    showFieldError($container, 'Password terlalu pendek');
                }
                break;
        }

        if (isValid) {
            $container.addClass('success');
            hideFieldError($container);
        }

        return isValid;
    }

    // Check if both fields have values (simplified)
    function bothFieldsHaveValues() {
        const nipValue = $('#nip').val().trim();
        const passwordValue = $('#password').val().trim();
        return nipValue.length > 0 && passwordValue.length > 0;
    }

    // Show field-specific error
    function showFieldError($container, message) {
        $container.addClass('error');

        // Remove existing error message
        $container.find('.field-error').remove();

        // Add error message
        const $errorMsg = $('<div class="field-error">' + message + '</div>');
        $container.append($errorMsg);

        // Animate error message
        setTimeout(() => {
            $errorMsg.addClass('show');
        }, 10);
    }

    // Hide field error
    function hideFieldError($container) {
        const $errorMsg = $container.find('.field-error');
        $errorMsg.removeClass('show');
        setTimeout(() => {
            $errorMsg.remove();
        }, 300);
    }

    // Create ripple effect on input focus
    function createRippleEffect(element) {
        const ripple = document.createElement('div');
        ripple.className = 'input-ripple';
        element.appendChild(ripple);

        setTimeout(() => {
            ripple.remove();
        }, 600);
    }

    // Show notification
    function showNotification(message, type = 'info') {
        // Remove existing notifications
        $('.notification').remove();

        const $notification = $(`
            <div class="notification notification-${type}">
                <i class="fas ${getNotificationIcon(type)}"></i>
                <span>${message}</span>
            </div>
        `);

        $('body').append($notification);

        // Show notification
        setTimeout(() => {
            $notification.addClass('show');
        }, 10);

        // Auto-hide after 5 seconds
        setTimeout(() => {
            $notification.removeClass('show');
            setTimeout(() => {
                $notification.remove();
            }, 300);
        }, 5000);
    }

    // Get notification icon based on type
    function getNotificationIcon(type) {
        switch (type) {
            case 'success': return 'fa-check-circle';
            case 'error': return 'fa-exclamation-circle';
            case 'warning': return 'fa-exclamation-triangle';
            default: return 'fa-info-circle';
        }
    }

    // SIMPLIFIED ENTER KEY HANDLING
    $('.input-field').on('keydown', function (e) {
        if (e.key === 'Enter') {
            const $currentInput = $(this);
            const currentFieldName = $currentInput.attr('name');

            if (currentFieldName === 'nip') {
                // If on NIP field, move to password field
                e.preventDefault();
                $('#password').focus();
            } else if (currentFieldName === 'password') {
                // If on password field, allow default form submission
                // Do not prevent default - let the form submit naturally
                if (!bothFieldsHaveValues()) {
                    e.preventDefault();
                    showNotification('Mohon lengkapi semua field terlebih dahulu', 'warning');
                }
            }
        }
    });

    // Tab navigation enhancement
    $(document).on('keydown', function (e) {
        if (e.key === 'Tab') {
            const $focused = $(':focus');
            if ($focused.hasClass('input-field')) {
                const $container = $focused.closest('.input-container');
                $container.addClass('tab-focused');
                setTimeout(() => {
                    $container.removeClass('tab-focused');
                }, 200);
            }
        }
    });

    // Enhanced error message handling
    $('.alert').each(function () {
        const $alert = $(this);

        // Add close button
        $alert.append('<button type="button" class="alert-close"><i class="fas fa-times"></i></button>');

        // Close alert on click
        $alert.find('.alert-close').on('click', function () {
            $alert.addClass('hiding');
            setTimeout(() => {
                $alert.remove();
            }, 300);
        });

        // Auto-hide after 10 seconds
        setTimeout(() => {
            if ($alert.length && !$alert.hasClass('hiding')) {
                $alert.addClass('auto-hiding');
                setTimeout(() => {
                    $alert.remove();
                }, 300);
            }
        }, 10000);
    });

    // Progressive loading for form elements
    $('.input-group').each(function (index) {
        $(this).css('animation-delay', (index * 0.1) + 's');
        $(this).addClass('animate-in');
    });

    $('.login-btn').css('animation-delay', '0.3s').addClass('animate-in');

    // Accessibility improvements
    $('.input-field').on('keydown', function (e) {
        // Announce focus to screen readers
        if (e.key === 'Tab') {
            const fieldName = $(this).attr('name');
            const friendlyName = fieldName === 'nip' ? 'NIP' : 'Password';

            // This would be announced by screen readers
            $(this).attr('aria-label', `${friendlyName} field focused`);
        }
    });

    // Performance optimization: Debounce input validation
    let validationTimeout;
    $('.input-field').on('input', function () {
        const $input = $(this);
        clearTimeout(validationTimeout);
        validationTimeout = setTimeout(() => {
            validateInput($input);
        }, 300);
    });
});

// Additional CSS for enhanced interactions (injected via JavaScript)
$('<style>')
    .prop('type', 'text/css')
    .html(`
        .field-error {
            position: absolute;
            bottom: -22px;
            left: 20px;
            color: #dc3545;
            font-size: 0.8rem;
            font-weight: 500;
            opacity: 0;
            transform: translateY(-5px);
            transition: all 0.3s ease;
        }
        
        .field-error.show {
            opacity: 1;
            transform: translateY(0);
        }
        
        .input-container.error {
            border-color: #dc3545 !important;
            box-shadow: 0 0 0 3px rgba(220, 53, 69, 0.1) !important;
        }
        
        .input-container.success {
            border-color: #28a745 !important;
        }
        
        .input-container.submitting {
            opacity: 0.7;
            transform: scale(0.98);
        }
        
        .input-ripple {
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(40, 167, 69, 0.3);
            transform: translate(-50%, -50%);
            animation: ripple 0.6s ease-out;
            pointer-events: none;
        }
        
        @keyframes ripple {
            0% {
                width: 0;
                height: 0;
                opacity: 1;
            }
            100% {
                width: 100px;
                height: 100px;
                opacity: 0;
            }
        }
        
        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            background: white;
            padding: 16px 20px;
            border-radius: 12px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
            display: flex;
            align-items: center;
            gap: 12px;
            transform: translateX(100%);
            transition: transform 0.3s ease;
            z-index: 10000;
            max-width: 300px;
            font-weight: 500;
        }
        
        .notification.show {
            transform: translateX(0);
        }
        
        .notification-success {
            border-left: 4px solid #28a745;
            color: #28a745;
        }
        
        .notification-error {
            border-left: 4px solid #dc3545;
            color: #dc3545;
        }
        
        .notification-info {
            border-left: 4px solid #17a2b8;
            color: #17a2b8;
        }
        
        .password-toggle.clicked {
            transform: translateY(-50%) scale(0.9);
        }
        
        .password-toggle.active {
            color: #28a745;
        }
        
        .input-container.tab-focused {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(40, 167, 69, 0.15);
        }
        
        .alert-close {
            background: none;
            border: none;
            color: inherit;
            cursor: pointer;
            margin-left: auto;
            padding: 4px;
            border-radius: 4px;
            transition: background-color 0.2s ease;
        }
        
        .alert-close:hover {
            background: rgba(0, 0, 0, 0.1);
        }
        
        .alert.hiding,
        .alert.auto-hiding {
            opacity: 0;
            transform: translateY(-10px);
            transition: all 0.3s ease;
        }
        
        .animate-in {
            animation: slideInUp 0.6s ease-out both;
        }
        
        @keyframes slideInUp {
            0% {
                opacity: 0;
                transform: translateY(20px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }
    `)
    .appendTo('head');
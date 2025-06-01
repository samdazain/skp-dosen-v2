<?php

/**
 * Scripts Partial
 * Contains JavaScript functionality for score management
 */
?>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize tooltips
        $('[data-toggle="tooltip"]').tooltip();

        // Range type selector in add range modal
        const rangeType = document.getElementById('rangeType');
        const rangeStartGroup = document.getElementById('rangeStartGroup');
        const rangeEndGroup = document.getElementById('rangeEndGroup');
        const labelGroup = document.getElementById('labelGroup');
        const rangeStart = document.getElementById('rangeStart');
        const rangeEnd = document.getElementById('rangeEnd');
        const rangeLabel = document.getElementById('rangeLabel');

        /**
         * Update form fields based on selected range type
         */
        function updateRangeFields() {
            const type = rangeType.value;

            // Reset required attributes
            rangeStart.removeAttribute('required');
            rangeEnd.removeAttribute('required');
            rangeLabel.removeAttribute('required');

            // Get current category to determine if integers should be used
            const category = document.getElementById('addRangeCategory').value;
            const useIntegers = ['integrity', 'discipline'].includes(category);

            // Update step values based on category
            const stepValue = useIntegers ? '1' : '0.01';
            if (rangeStart) rangeStart.step = stepValue;
            if (rangeEnd) rangeEnd.step = stepValue;

            switch (type) {
                case 'range':
                    rangeStartGroup.style.display = 'block';
                    rangeEndGroup.style.display = 'block';
                    labelGroup.style.display = 'none';
                    rangeStart.setAttribute('required', 'required');
                    rangeEnd.setAttribute('required', 'required');

                    // Update placeholders
                    rangeStart.placeholder = useIntegers ? 'Contoh: 5' : 'Contoh: 2.5';
                    rangeEnd.placeholder = useIntegers ? 'Contoh: 10' : 'Contoh: 3.5';
                    break;

                case 'above':
                    rangeStartGroup.style.display = 'block';
                    rangeEndGroup.style.display = 'none';
                    labelGroup.style.display = 'none';
                    rangeStart.setAttribute('required', 'required');
                    rangeStart.placeholder = useIntegers ? 'Contoh: 10' : 'Contoh: 3.5';
                    break;

                case 'below':
                    rangeStartGroup.style.display = 'block';
                    rangeEndGroup.style.display = 'none';
                    labelGroup.style.display = 'none';
                    rangeStart.setAttribute('required', 'required');
                    rangeStart.placeholder = useIntegers ? 'Contoh: 5' : 'Contoh: 2.5';
                    break;

                case 'exact':
                    rangeStartGroup.style.display = 'block';
                    rangeEndGroup.style.display = 'none';
                    labelGroup.style.display = 'none';
                    rangeStart.setAttribute('required', 'required');
                    rangeStart.placeholder = useIntegers ? 'Contoh: 0' : 'Contoh: 3.0';
                    break;

                case 'fixed':
                case 'boolean':
                    rangeStartGroup.style.display = 'none';
                    rangeEndGroup.style.display = 'none';
                    labelGroup.style.display = 'block';
                    rangeLabel.setAttribute('required', 'required');
                    break;

                default:
                    rangeStartGroup.style.display = 'none';
                    rangeEndGroup.style.display = 'none';
                    labelGroup.style.display = 'none';
            }
        }

        /**
         * Show preview of score impact
         */
        function showScorePreview(rangeId, newScore) {
            // Ensure score is displayed as integer
            const intScore = parseInt(newScore) || 0;

            // Add visual indicator
            const scoreInput = document.querySelector(`[data-range-id="${rangeId}"]`);
            if (scoreInput) {
                const preview = document.createElement('div');
                preview.className = 'score-preview alert alert-info alert-sm mt-1';
                preview.innerHTML = `<small><i class="fas fa-info-circle"></i> Skor baru: ${intScore} poin</small>`;

                // Remove existing preview
                const existingPreview = scoreInput.parentNode.querySelector('.score-preview');
                if (existingPreview) {
                    existingPreview.remove();
                }

                // Add new preview
                scoreInput.parentNode.appendChild(preview);

                // Auto remove after 3 seconds
                setTimeout(() => {
                    if (preview.parentNode) {
                        preview.remove();
                    }
                }, 3000);
            }
        }

        /**
         * Form validation and submission
         */
        const scoreForms = document.querySelectorAll('.score-range-form');
        scoreForms.forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault(); // Prevent default form submission

                // Validate form data
                if (!validateFormData(this)) {
                    return false;
                }

                // Add loading state
                const submitBtn = this.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;
                const originalDisabled = submitBtn.disabled;

                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Menyimpan...';
                submitBtn.disabled = true;

                // Get form data
                const formData = new FormData(this);

                // Add CSRF token
                formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');

                // Submit form via fetch
                fetch(this.action, {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.text();
                    })
                    .then(html => {
                        // Check if response contains success/error messages
                        if (html.includes('alert-success') || html.includes('Berhasil')) {
                            showToast('Perubahan berhasil disimpan', 'success');

                            // Optional: refresh the page after a delay
                            setTimeout(() => {
                                window.location.reload();
                            }, 1500);
                        } else if (html.includes('alert-danger') || html.includes('Gagal')) {
                            showToast('Gagal menyimpan perubahan', 'error');
                        } else {
                            showToast('Perubahan berhasil disimpan', 'success');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showToast('Terjadi kesalahan saat menyimpan', 'error');
                    })
                    .finally(() => {
                        // Restore button state
                        submitBtn.innerHTML = originalText;
                        submitBtn.disabled = originalDisabled;
                    });
            });
        });

        /**
         * Validate form data before submission
         */
        function validateFormData(form) {
            const scoreInputs = form.querySelectorAll('.score-input');
            let isValid = true;

            scoreInputs.forEach(input => {
                const value = parseInt(input.value);

                if (isNaN(value) || value < 0 || value > 100) {
                    input.classList.add('is-invalid');
                    isValid = false;

                    // Remove invalid class after user fixes it
                    input.addEventListener('input', function() {
                        this.classList.remove('is-invalid');
                    }, {
                        once: true
                    });
                } else {
                    input.classList.remove('is-invalid');
                }
            });

            if (!isValid) {
                showToast('Mohon periksa input yang tidak valid', 'warning');
            }

            return isValid;
        }

        /**
         * Real-time score calculation preview
         */
        function setupScoreCalculator() {
            const scoreInputs = document.querySelectorAll('.score-input');
            scoreInputs.forEach(input => {
                // Ensure integer input
                input.addEventListener('input', function() {
                    // Force integer value
                    let intValue = parseInt(this.value) || 0;

                    // Clamp value between 0 and 100
                    intValue = Math.max(0, Math.min(100, intValue));

                    if (this.value !== intValue.toString()) {
                        this.value = intValue;
                    }

                    const rangeId = this.dataset.rangeId;

                    // Visual feedback for score change
                    this.style.borderColor = '#ffc107';
                    this.style.boxShadow = '0 0 5px rgba(255, 193, 7, 0.5)';

                    // Show preview of what this score will affect
                    showScorePreview(rangeId, intValue);

                    // Reset border after delay
                    setTimeout(() => {
                        this.style.borderColor = '';
                        this.style.boxShadow = '';
                    }, 2000);
                });

                // Handle paste events to ensure integers
                input.addEventListener('paste', function(e) {
                    setTimeout(() => {
                        let intValue = parseInt(this.value) || 0;
                        intValue = Math.max(0, Math.min(100, intValue));
                        this.value = intValue;
                    }, 10);
                });

                // Handle blur to ensure final value is integer
                input.addEventListener('blur', function() {
                    let intValue = parseInt(this.value) || 0;
                    intValue = Math.max(0, Math.min(100, intValue));
                    this.value = intValue;
                });
            });
        }

        /**
         * Setup score input in add range modal
         */
        function setupModalScoreInput() {
            const scoreInput = document.getElementById('score');
            if (scoreInput) {
                scoreInput.addEventListener('input', function() {
                    // Force integer value
                    const intValue = parseInt(this.value) || 0;
                    if (this.value !== intValue.toString()) {
                        this.value = intValue;
                    }
                });

                scoreInput.addEventListener('blur', function() {
                    const intValue = parseInt(this.value) || 0;
                    this.value = Math.max(0, Math.min(100, intValue)); // Ensure 0-100 range
                });
            }
        }

        /**
         * Category change handler for add range modal
         */
        const addRangeButtons = document.querySelectorAll('.add-range');
        addRangeButtons.forEach(button => {
            button.addEventListener('click', function() {
                const category = this.dataset.category;
                const subcategory = this.dataset.subcategory;

                document.getElementById('addRangeCategory').value = category;
                document.getElementById('addRangeSubcategory').value = subcategory;

                // Reset form
                document.getElementById('addRangeForm').reset();

                // Update fields based on category
                updateRangeFields();

                // Add category-specific help text
                addCategoryHelp(category);
            });
        });

        /**
         * Add help text based on category
         */
        function addCategoryHelp(category) {
            const helpTexts = {
                'integrity': 'Gunakan bilangan bulat (contoh: 5, 10, 15)',
                'discipline': 'Gunakan bilangan bulat (contoh: 0, 3, 8)',
                'commitment': 'Gunakan label boolean (Ada/Tidak, Lulus/Tidak Lulus)',
                'cooperation': 'Gunakan label tetap (Tidak Kooperatif, Kooperatif, dll)',
                'orientation': 'Gunakan nilai desimal (contoh: 2.5, 3.0, 3.5)'
            };

            const helpText = helpTexts[category] || 'Sesuaikan dengan jenis data';

            // Add or update help text
            let helpElement = document.getElementById('category-help');
            if (!helpElement) {
                helpElement = document.createElement('div');
                helpElement.id = 'category-help';
                helpElement.className = 'alert alert-info mt-2';
                document.querySelector('#addRangeModal .modal-body').appendChild(helpElement);
            }

            helpElement.innerHTML =
                `<small><i class="fas fa-lightbulb"></i> <strong>Tips:</strong> ${helpText}</small>`;
        }

        // Initialize enhancements
        if (rangeType) {
            rangeType.addEventListener('change', updateRangeFields);
            updateRangeFields(); // Initial setup
        }

        setupScoreCalculator();
        setupModalScoreInput();

        /**
         * Handle delete range button clicks
         */
        const deleteRangeButtons = document.querySelectorAll('.delete-range');
        deleteRangeButtons.forEach(button => {
            button.addEventListener('click', function() {
                const rangeId = this.dataset.rangeId;
                document.getElementById('deleteRangeId').value = rangeId;
            });
        });

        /**
         * Keyboard shortcuts
         */
        document.addEventListener('keydown', function(e) {
            // Ctrl+S to save current form
            if (e.ctrlKey && e.key === 's') {
                e.preventDefault();
                const activeTab = document.querySelector('.tab-pane.active');
                if (activeTab) {
                    const form = activeTab.querySelector('.score-range-form');
                    if (form) {
                        form.querySelector('button[type="submit"]').click();
                    }
                }
            }
        });

        /**
         * Dynamic range validation
         */
        function validateRanges() {
            const tables = document.querySelectorAll('.table tbody');
            tables.forEach(tbody => {
                const rows = tbody.querySelectorAll('tr[data-range-id]');
                // Add validation logic here if needed
            });
        }

        // Initialize validation
        validateRanges();
    });

    /**
     * Show success toast notification
     */
    function showToast(message, type = 'success') {
        if (typeof Swal !== 'undefined') {
            const iconMap = {
                'success': 'success',
                'error': 'error',
                'warning': 'warning',
                'info': 'info'
            };

            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: iconMap[type] || 'info',
                title: message,
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });
        } else {
            // Fallback alert
            alert(message);
        }
    }

    /**
     * Format number as integer for display
     */
    function formatScore(score) {
        return parseInt(score) || 0;
    }
</script>
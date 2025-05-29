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

            switch (type) {
                case 'range':
                    rangeStartGroup.style.display = 'block';
                    rangeEndGroup.style.display = 'block';
                    labelGroup.style.display = 'none';
                    rangeStart.setAttribute('required', 'required');
                    rangeEnd.setAttribute('required', 'required');
                    break;

                case 'above':
                    rangeStartGroup.style.display = 'block';
                    rangeEndGroup.style.display = 'none';
                    labelGroup.style.display = 'none';
                    rangeStart.setAttribute('required', 'required');
                    break;

                case 'below':
                    rangeStartGroup.style.display = 'none';
                    rangeEndGroup.style.display = 'block';
                    labelGroup.style.display = 'none';
                    rangeEnd.setAttribute('required', 'required');
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

        if (rangeType) {
            rangeType.addEventListener('change', updateRangeFields);
            updateRangeFields(); // Initial setup
        }

        /**
         * Handle add range button clicks
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
                updateRangeFields();
            });
        });

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
         * Form validation and submission
         */
        const scoreForms = document.querySelectorAll('.score-range-form');
        scoreForms.forEach(form => {
            form.addEventListener('submit', function(e) {
                // Add loading state
                const submitBtn = this.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;

                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Menyimpan...';
                submitBtn.disabled = true;

                // Re-enable after 3 seconds as fallback
                setTimeout(() => {
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                }, 3000);
            });
        });

        /**
         * Auto-save functionality for individual ranges
         */
        const rangeInputs = document.querySelectorAll('.range-row input');
        rangeInputs.forEach(input => {
            let timeout;
            input.addEventListener('input', function() {
                clearTimeout(timeout);
                timeout = setTimeout(() => {
                    // Add visual feedback for auto-save
                    this.classList.add('border-warning');
                    setTimeout(() => {
                        this.classList.remove('border-warning');
                        this.classList.add('border-success');
                        setTimeout(() => {
                            this.classList.remove('border-success');
                        }, 1000);
                    }, 500);
                }, 1000);
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
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: type,
                title: message,
                showConfirmButton: false,
                timer: 3000
            });
        } else {
            // Fallback alert
            alert(message);
        }
    }
</script>
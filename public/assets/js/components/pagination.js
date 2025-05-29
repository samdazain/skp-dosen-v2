/**
 * Reusable Pagination Component JavaScript
 */
class PaginationComponent {
    constructor(container = document) {
        this.container = container;
        this.init();
    }

    init() {
        this.initializeTooltips();
        this.initializePerPageSelector();
    }

    initializeTooltips() {
        const tooltips = this.container.querySelectorAll('[data-toggle="tooltip"]');
        tooltips.forEach(tooltip => {
            if (typeof bootstrap !== 'undefined') {
                new bootstrap.Tooltip(tooltip);
            } else if (typeof $ !== 'undefined') {
                $(tooltip).tooltip();
            }
        });
    }

    initializePerPageSelector() {
        const selectors = this.container.querySelectorAll('.pagination-per-page-select');
        selectors.forEach(selector => {
            selector.addEventListener('change', (e) => {
                this.handlePerPageChange(e.target.value);
            });
        });
    }

    handlePerPageChange(perPage) {
        const url = new URL(window.location);
        url.searchParams.set('per_page', perPage);
        url.searchParams.delete('page'); // Reset to page 1
        window.location.href = url.toString();
    }

    static autoInit() {
        // Auto-initialize pagination components on page load
        document.addEventListener('DOMContentLoaded', () => {
            new PaginationComponent();
        });
    }
}

// Auto-initialize if not manually initialized
if (typeof window !== 'undefined') {
    PaginationComponent.autoInit();
}

// Export for manual initialization
if (typeof module !== 'undefined' && module.exports) {
    module.exports = PaginationComponent;
}

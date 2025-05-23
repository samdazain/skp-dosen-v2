document.addEventListener('DOMContentLoaded', function () {
    // Initialize any dashboard charts or visualizations
    initializeCharts();

    // Set up activity log interactions
    setupActivityLog();

    // Dashboard cards hover effects
    setupCardHoverEffects();
});

/**
 * Initialize dashboard charts if they exist
 */
function initializeCharts() {
    // Check if we have any chart containers
    const chartContainers = document.querySelectorAll('.chart-container');
    if (!chartContainers.length) return;

    // Example: initialize a simple chart if Chart.js is available
    if (typeof Chart !== 'undefined') {
        chartContainers.forEach(container => {
            const canvas = container.querySelector('canvas');
            if (!canvas) return;

            // Get chart data from data attributes or other sources
            const chartType = container.dataset.chartType || 'bar';

            // Set some default chart options
            try {
                new Chart(canvas.getContext('2d'), {
                    type: chartType,
                    data: {
                        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                        datasets: [{
                            label: container.dataset.label || 'Data',
                            data: JSON.parse(container.dataset.values || '[0,0,0,0,0,0]'),
                            backgroundColor: container.dataset.backgroundColor || 'rgba(54, 162, 235, 0.2)',
                            borderColor: container.dataset.borderColor || 'rgba(54, 162, 235, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false
                    }
                });
            } catch (error) {
                console.warn('Error initializing chart:', error);
            }
        });
    }
}

/**
 * Set up activity log functionality 
 */
function setupActivityLog() {
    const activityLog = document.querySelector('.activity-timeline');
    if (!activityLog) return;

    // Example: Implement "show more" functionality if there's a button
    const showMoreBtn = document.querySelector('.show-more-activities');
    if (showMoreBtn) {
        showMoreBtn.addEventListener('click', function () {
            const hiddenItems = activityLog.querySelectorAll('.timeline-item.d-none');

            // Show up to 3 more items
            for (let i = 0; i < Math.min(hiddenItems.length, 3); i++) {
                hiddenItems[i].classList.remove('d-none');
            }

            // Hide the button if all items are visible
            if (hiddenItems.length <= 3) {
                this.classList.add('d-none');
            }
        });
    }
}

/**
 * Add hover effects to dashboard cards
 */
function setupCardHoverEffects() {
    // Add subtle hover effects to action cards
    const actionCards = document.querySelectorAll('.action-card');

    actionCards.forEach(card => {
        card.addEventListener('mouseenter', function () {
            this.querySelector('.card-body').classList.add('bg-light');

            // Animate the icon slightly
            const icon = this.querySelector('.action-icon');
            if (icon) {
                icon.style.transform = 'scale(1.1)';
                icon.style.transition = 'transform 0.3s ease';
            }
        });

        card.addEventListener('mouseleave', function () {
            this.querySelector('.card-body').classList.remove('bg-light');

            // Reset the icon
            const icon = this.querySelector('.action-icon');
            if (icon) {
                icon.style.transform = 'scale(1)';
            }
        });
    });

    // Make info boxes interactive if needed
    const infoBoxes = document.querySelectorAll('.info-box');
    infoBoxes.forEach(box => {
        if (box.dataset.url) {
            box.style.cursor = 'pointer';
            box.addEventListener('click', function () {
                window.location.href = this.dataset.url;
            });
        }
    });
}

/**
 * Refresh dashboard data (could be called by a refresh button)
 */
function refreshDashboardData() {
    // Example implementation - in a real app this would use fetch() to get new data
    console.log('Refreshing dashboard data...');

    // Show loading indicators
    document.querySelectorAll('.refresh-indicator').forEach(el => {
        el.classList.remove('d-none');
    });

    // Simulate an API call with a timeout
    setTimeout(() => {
        // Hide loading indicators
        document.querySelectorAll('.refresh-indicator').forEach(el => {
            el.classList.add('d-none');
        });

        console.log('Dashboard data refreshed');
    }, 1000);
}
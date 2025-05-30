<?php
$semesterModel = new \App\Models\SemesterModel();
$currentSemester = $semesterModel->getCurrentSemester();

// Get current date info for context
$currentMonth = date('n');
$currentMonthName = date('F');
$currentYear = date('Y');

// Determine expected semester based on current date
$expectedTerm = '';
$expectedYear = '';
if ($currentMonth >= 7) {
    $expectedTerm = 'Genap';
    $expectedYear = $currentYear;
} elseif ($currentMonth >= 2) {
    $expectedTerm = 'Ganjil';
    $expectedYear = $currentYear;
} else {
    $expectedTerm = 'Genap';
    $expectedYear = $currentYear - 1;
}
?>

<div class="semester-info-container mb-3">
    <?php if ($currentSemester): ?>
        <div class="card border-0 shadow-sm semester-info-card">
            <div class="card-body p-3">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <div class="semester-icon-wrapper">
                            <div class="semester-icon bg-gradient-primary">
                                <i class="fas fa-calendar-check text-white"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="semester-details">
                            <h6 class="semester-title mb-1">
                                <span class="text-muted">Semester Aktif:</span>
                                <strong class="text-primary ml-1">
                                    <?= $semesterModel->formatSemester($currentSemester) ?>
                                </strong>
                            </h6>
                            <div class="semester-meta">
                                <small class="text-muted d-flex align-items-center">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Data yang ditampilkan berdasarkan semester yang sedang aktif
                                </small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Additional semester context info -->
                <div class="semester-context mt-2">
                    <div class="row">
                        <div class="col-md-6">
                            <small class="text-muted">
                                <i class="fas fa-clock mr-1"></i>
                                <strong>Bulan Saat Ini:</strong> <?= $currentMonthName ?> <?= $currentYear ?>
                            </small>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted">
                                <i class="fas fa-lightbulb mr-1"></i>
                                <strong>Semester yang Direkomendasikan:</strong> <?= $expectedTerm ?> <?= $expectedYear ?>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="card border-warning shadow-sm semester-warning-card">
            <div class="card-body p-3">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <div class="warning-icon-wrapper">
                            <div class="warning-icon bg-gradient-warning">
                                <i class="fas fa-exclamation-triangle text-white"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="warning-details">
                            <h6 class="warning-title mb-1 text-warning">
                                <strong>Semester Belum Dipilih</strong>
                            </h6>
                            <p class="warning-text mb-2 text-muted">
                                Tidak ada semester aktif yang dipilih. Gunakan semester selector di navbar untuk memilih
                                semester.
                            </p>
                            <div class="suggested-semester">
                                <small class="text-info">
                                    <i class="fas fa-calendar-plus mr-1"></i>
                                    <strong>Saran:</strong> Pilih <strong><?= $expectedTerm ?> <?= $expectedYear ?></strong>
                                    untuk bulan <?= $currentMonthName ?>
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<style>
    .semester-info-container {
        position: relative;
    }

    .semester-info-card {
        border-left: 4px solid #007bff;
        transition: all 0.3s ease;
    }

    .semester-info-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    }

    .semester-warning-card {
        border-left: 4px solid #ffc107;
        transition: all 0.3s ease;
    }

    .semester-warning-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    }

    .semester-icon-wrapper,
    .warning-icon-wrapper {
        position: relative;
    }

    .semester-icon,
    .warning-icon {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        position: relative;
        overflow: hidden;
    }

    .semester-icon::before,
    .warning-icon::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(45deg, rgba(255, 255, 255, 0.1), rgba(255, 255, 255, 0.3));
        border-radius: 50%;
    }

    .bg-gradient-primary {
        background: linear-gradient(135deg, #007bff, #0056b3) !important;
    }

    .bg-gradient-warning {
        background: linear-gradient(135deg, #ffc107, #e0a800) !important;
    }

    .semester-title,
    .warning-title {
        font-size: 1rem;
        line-height: 1.3;
    }

    .semester-meta {
        margin-top: 0.25rem;
    }

    .semester-context {
        border-top: 1px solid #f8f9fa;
        padding-top: 0.75rem;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .semester-context .row {
            flex-direction: column;
        }

        .semester-context .col-md-6 {
            margin-bottom: 0.25rem;
        }
    }

    /* Animation for semester change */
    .semester-info-card.updating {
        opacity: 0.7;
        transform: scale(0.98);
    }

    .semester-info-card.updated {
        animation: semesterUpdated 0.6s ease;
    }

    @keyframes semesterUpdated {
        0% {
            transform: scale(0.98);
            opacity: 0.7;
        }

        50% {
            transform: scale(1.02);
            opacity: 1;
            box-shadow: 0 0 20px rgba(0, 123, 255, 0.3);
        }

        100% {
            transform: scale(1);
            opacity: 1;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Add refresh functionality
        const refreshButton = document.querySelector('a[href*="semester/current"]');
        if (refreshButton) {
            refreshButton.addEventListener('click', function(e) {
                e.preventDefault();

                const card = document.querySelector('.semester-info-card');
                if (card) {
                    card.classList.add('updating');
                }

                // Simulate refresh
                setTimeout(() => {
                    if (card) {
                        card.classList.remove('updating');
                        card.classList.add('updated');

                        // Remove animation class after animation completes
                        setTimeout(() => {
                            card.classList.remove('updated');
                        }, 600);
                    }

                    if (typeof showToast === 'function') {
                        showToast('Data semester telah diperbarui', 'success');
                    }
                }, 1000);
            });
        }

        // Initialize tooltips
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>
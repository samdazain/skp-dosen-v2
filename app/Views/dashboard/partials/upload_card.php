<?php

/**
 * @var CodeIgniter\View\View $this
 */

// Generate a consistent form ID based on the input ID to avoid random IDs
$formId = 'uploadForm_' . ($input_id ?? 'fileInput');
?>

<div class="col-md-4">
    <div class="card <?= $card_class ?? 'card-primary' ?>">
        <div class="card-header">
            <h3 class="card-title">
                <i class="<?= $icon ?? 'fas fa-upload' ?> mr-2"></i><?= $title ?? 'Upload Data' ?>
            </h3>
        </div>
        <div class="card-body">
            <form action="<?= base_url($upload_url ?? '#') ?>" method="post" enctype="multipart/form-data"
                id="<?= $formId ?>">
                <?= csrf_field() ?>

                <div class="upload-indicator text-center mb-3 file-upload-trigger"
                    id="trigger_<?= $input_id ?? 'fileInput' ?>">
                    <div class="upload-icon-container position-relative">
                        <i class="fas fa-file-excel fa-3x text-success"></i>
                        <i
                            class="fas fa-arrow-circle-up position-absolute <?= $arrow_color ?? 'text-primary' ?> upload-arrow"></i>
                    </div>
                    <p class="mt-2 text-muted file-instruction">Klik untuk memilih file Excel</p>
                    <p class="mt-2 file-name-display d-none"></p>
                </div>

                <input type="file" class="d-none file-input" id="<?= $input_id ?? 'fileInput' ?>"
                    name="<?= $input_name ?? 'file' ?>" accept=".xlsx,.xls,.csv">

                <div class="text-center">
                    <button type="submit" class="btn <?= $button_class ?? 'btn-primary' ?> btn-block upload-btn"
                        disabled>
                        <i class="fas fa-upload mr-2"></i> Upload
                    </button>
                </div>
            </form>
        </div>
        <div class="card-footer text-center">
            <a href="<?= $download_url ?? '#' ?>" class="text-muted">
                <i class="fas fa-download mr-1"></i> Download Uploaded File
            </a>
        </div>
    </div>
</div>

<!-- Move script to the end of the document and make it self-contained -->
<script>
    // Use an immediately invoked function expression to avoid global scope pollution
    (function() {
        // This function will run once the DOM is fully loaded
        function initUploadCard() {
            // Use a consistent form ID based on input ID
            const formId = '<?= $formId ?>';
            const form = document.getElementById(formId);

            // Defensive programming: Check if form exists
            if (!form) {
                console.error(`Form with ID ${formId} not found.`);
                return; // Exit if form not found
            }

            // Get elements
            const triggerId = 'trigger_<?= $input_id ?? 'fileInput' ?>';
            const trigger = document.getElementById(triggerId);

            // More defensive checks
            if (!trigger) {
                console.error(`Trigger with ID ${triggerId} not found.`);
                return;
            }

            const fileInput = form.querySelector('.file-input');
            const fileInstruction = form.querySelector('.file-instruction');
            const fileNameDisplay = form.querySelector('.file-name-display');
            const uploadBtn = form.querySelector('.upload-btn');

            // Verify all required elements exist
            if (!fileInput || !fileInstruction || !fileNameDisplay || !uploadBtn) {
                console.error('One or more required elements not found in form:', formId);
                return;
            }

            // Add click event to the upload trigger area
            trigger.addEventListener('click', function() {
                fileInput.click();
            });

            // Show file name when a file is selected
            fileInput.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    const fileName = this.files[0].name;
                    fileInstruction.classList.add('d-none');
                    fileNameDisplay.textContent = fileName;
                    fileNameDisplay.classList.remove('d-none');
                    uploadBtn.removeAttribute('disabled');

                    // Add a visual indicator that file is selected
                    trigger.classList.add('file-selected');
                } else {
                    resetFileDisplay();
                }
            });

            // Reset the file display when needed
            function resetFileDisplay() {
                fileInstruction.classList.remove('d-none');
                fileNameDisplay.classList.add('d-none');
                fileNameDisplay.textContent = '';
                uploadBtn.setAttribute('disabled', 'disabled');
                trigger.classList.remove('file-selected');
            }

            // Add a reset capability
            fileNameDisplay.addEventListener('click', function(e) {
                e.stopPropagation(); // Prevent triggering the parent click event
                fileInput.value = '';
                resetFileDisplay();
            });
        }

        // Check if document is already loaded
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initUploadCard);
        } else {
            // Document already loaded, run initialization immediately
            initUploadCard();
        }
    })();
</script>
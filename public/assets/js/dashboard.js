document.addEventListener('DOMContentLoaded', function () {
    // Display filename when a file is selected
    document.querySelectorAll('.custom-file-input').forEach(function (input) {
        input.addEventListener('change', function (e) {
            if (e.target.files.length > 0) {
                const fileName = e.target.files[0].name;
                const label = e.target.nextElementSibling;
                label.innerHTML = fileName;
            }
        });
    });

    // Make upload indicators clickable to trigger file input
    document.querySelectorAll('.upload-indicator').forEach(function (indicator, index) {
        indicator.addEventListener('click', function () {
            // Find the closest file input within the same card
            const card = indicator.closest('.card');
            if (card) {
                const fileInput = card.querySelector('.custom-file-input');
                if (fileInput) {
                    fileInput.click();
                }
            }
        });
    });
});
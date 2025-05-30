<?php

/**
 * Search Bar Component
 * 
 * @var string $placeholder Search placeholder text
 * @var string $inputId Input element ID
 * @var string $size Input size ('sm', 'md', 'lg')
 * @var string $width Input width (CSS value)
 */

$placeholder = $placeholder ?? 'Cari...';
$inputId = $inputId ?? 'searchInput';
$size = $size ?? 'sm';
$width = $width ?? '250px';
$sizeClass = $size !== 'md' ? 'input-group-' . $size : '';
?>

<div class="input-group <?= $sizeClass ?> ml-3 pt-1 float-right" style="width: <?= $width ?>;">
    <input type="text"
        name="table_search"
        class="form-control float-right"
        id="<?= $inputId ?>"
        placeholder="<?= esc($placeholder) ?>"
        autocomplete="off">
    <div class="input-group-append">
        <button type="button" class="btn btn-default" title="Cari">
            <i class="fas fa-search"></i>
        </button>
    </div>
</div>

<script>
    // Initialize search functionality
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('<?= $inputId ?>');

        if (searchInput) {
            // Add clear button functionality
            const clearBtn = document.createElement('button');
            clearBtn.type = 'button';
            clearBtn.className = 'btn btn-outline-secondary btn-sm ml-1';
            clearBtn.innerHTML = '<i class="fas fa-times"></i>';
            clearBtn.title = 'Hapus pencarian';
            clearBtn.style.display = 'none';

            // Insert clear button after input group
            searchInput.closest('.input-group').insertAdjacentElement('afterend', clearBtn);

            // Show/hide clear button based on input
            searchInput.addEventListener('input', function() {
                if (this.value.length > 0) {
                    clearBtn.style.display = 'inline-block';
                } else {
                    clearBtn.style.display = 'none';
                }
            });

            // Clear search functionality
            clearBtn.addEventListener('click', function() {
                searchInput.value = '';
                searchInput.dispatchEvent(new Event('input'));
                searchInput.focus();
                this.style.display = 'none';
            });

            // Add search icon animation
            searchInput.addEventListener('focus', function() {
                this.closest('.input-group').classList.add('focus');
            });

            searchInput.addEventListener('blur', function() {
                this.closest('.input-group').classList.remove('focus');
            });
        }
    });
</script>

<style>
    .input-group.focus {
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        border-radius: 0.25rem;
    }

    .input-group.focus .form-control,
    .input-group.focus .btn {
        border-color: #80bdff;
    }
</style>
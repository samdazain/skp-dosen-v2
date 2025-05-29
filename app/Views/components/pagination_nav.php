<?php
$alignmentClass = match ($alignment) {
    'start' => 'justify-content-start',
    'center' => 'justify-content-center',
    'end' => 'justify-content-end',
    default => 'justify-content-md-end justify-content-center'
};

$sizeClass = $size !== 'md' ? "pagination-{$size}" : '';
?>

<nav aria-label="Pagination Navigation" class="d-flex <?= $alignmentClass ?>">
    <ul class="pagination <?= $sizeClass ?> mb-0">
        <?= view('Components/pagination_controls', [
            'pagination' => $pagination,
            'type' => 'first'
        ]) ?>

        <?= view('Components/pagination_controls', [
            'pagination' => $pagination,
            'type' => 'previous'
        ]) ?>

        <?= view('Components/pagination_numbers', [
            'pagination' => $pagination
        ]) ?>

        <?= view('Components/pagination_controls', [
            'pagination' => $pagination,
            'type' => 'next'
        ]) ?>

        <?= view('Components/pagination_controls', [
            'pagination' => $pagination,
            'type' => 'last'
        ]) ?>
    </ul>
</nav>
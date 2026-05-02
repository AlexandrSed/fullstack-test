<?php

use CodeIgniter\Pager\PagerRenderer;

/**
 * @var PagerRenderer $pager
 */
$pager->setSurroundCount(2);
?>

<?php if ($pager->getPageCount() > 1) : ?>
<nav aria-label="Pagination">
    <ul class="pagination justify-content-center flex-wrap my-3">
        <?php if ($pager->getCurrentPageNumber() > 1) : ?>
            <li class="page-item">
                <a class="page-link" href="<?= $pager->getFirst() ?>" aria-label="First">First</a>
            </li>
            <li class="page-item">
                <a class="page-link" href="<?= $pager->getPreviousPage() ?>" aria-label="Previous page">Previous</a>
            </li>
        <?php else : ?>
            <li class="page-item disabled"><span class="page-link">First</span></li>
            <li class="page-item disabled"><span class="page-link">Previous</span></li>
        <?php endif ?>

        <?php foreach ($pager->links() as $link) : ?>
            <li class="page-item<?= $link['active'] ? ' active' : '' ?>">
                <a class="page-link" href="<?= $link['uri'] ?>"><?= $link['title'] ?></a>
            </li>
        <?php endforeach ?>

        <?php if ($pager->getCurrentPageNumber() < $pager->getPageCount()) : ?>
            <li class="page-item">
                <a class="page-link" href="<?= $pager->getNextPage() ?>" aria-label="Next page">Next</a>
            </li>
            <li class="page-item">
                <a class="page-link" href="<?= $pager->getLast() ?>" aria-label="Last">Last</a>
            </li>
        <?php else : ?>
            <li class="page-item disabled"><span class="page-link">Next</span></li>
            <li class="page-item disabled"><span class="page-link">Last</span></li>
        <?php endif ?>
    </ul>
</nav>
<?php endif ?>

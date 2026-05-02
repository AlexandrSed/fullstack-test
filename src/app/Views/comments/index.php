<h2><?= esc($title) ?></h2>

<?php
$sort = $sort ?? 'id';
$order = $order ?? 'desc';
$sortUrl = static function (string $s, string $o): string {
    return site_url('comments?' . http_build_query(['sort' => $s, 'order' => $o]));
};
$isActive = static function (string $s, string $o) use ($sort, $order): bool {
    return $sort === $s && $order === $o;
};
?>

<div class="btn-group flex-wrap mb-3" role="group" aria-label="Sort comments">
    <a href="<?= $sortUrl('id', 'asc') ?>"
       class="btn btn-sm <?= $isActive('id', 'asc') ? 'btn-primary' : 'btn-outline-secondary' ?>">ID ↑</a>
    <a href="<?= $sortUrl('id', 'desc') ?>"
       class="btn btn-sm <?= $isActive('id', 'desc') ? 'btn-primary' : 'btn-outline-secondary' ?>">ID ↓</a>
    <a href="<?= $sortUrl('date', 'asc') ?>"
       class="btn btn-sm <?= $isActive('date', 'asc') ? 'btn-primary' : 'btn-outline-secondary' ?>">Date ↑</a>
    <a href="<?= $sortUrl('date', 'desc') ?>"
       class="btn btn-sm <?= $isActive('date', 'desc') ? 'btn-primary' : 'btn-outline-secondary' ?>">Date ↓</a>
</div>

<div id="comments-block">
<?php if (isset($pager)) : ?>
    <p class="text-muted small mb-2" id="comments-page-info">
        Page <?= (int) $pager->getCurrentPage() ?> of <?= (int) max(1, $pager->getPageCount()) ?>
    </p>
<?php endif ?>

<div id="comments-list">
<?php if ($comments_list !== []): ?>

    <?php foreach ($comments_list as $comment_item): ?>

        <article class="card comment-item mb-3" id="comment-<?= esc($comment_item['id'], 'attr') ?>">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                <h5><?= esc($comment_item['name']) ?></h5>
                <span class="text-muted small"><?= esc($comment_item['date']) ?></span>
            </div>
            <div class="d-flex justify-content-between align-items-center flex-wrap card-body">
                <?= esc($comment_item['text']) ?>
                <?= form_open('comments/delete/' . $comment_item['id'], ['class' => 'comment-delete-form']) ?>
                <button type="submit" class="btn mt-2 btn-sm btn-outline-danger">Delete</button>
                <?= form_close() ?>
            </div>
        </article>

    <?php endforeach ?>

<?php else: ?>

    <div id="no-comments">
        <h3>No Comments</h3>
        <p>Unable to find any comments for you.</p>
    </div>

<?php endif; ?>
</div>

<?php if (isset($pager)) : ?>
    <div id="comments-pagination" class="container-fluid px-0">
        <?= $pager->links('default', 'bootstrap_full') ?>
    </div>
<?php endif ?>
</div>

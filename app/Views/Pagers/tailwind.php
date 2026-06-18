<?php
$pager->setSurroundCount(2);
?>
<nav class="flex justify-center mt-4">
    <div class="join">
        <?php if ($pager->hasPrevious()) : ?>
            <a href="<?= $pager->getFirst() ?>" class="join-item btn btn-sm" aria-label="<?= lang('Pager.first') ?>">
                <span aria-hidden="true"><?= lang('Pager.first') ?></span>
            </a>
            <a href="<?= $pager->getPrevious() ?>" class="join-item btn btn-sm" aria-label="<?= lang('Pager.previous') ?>">
                <span aria-hidden="true">&laquo;</span>
            </a>
        <?php endif ?>

        <?php foreach ($pager->links() as $link) : ?>
            <a href="<?= $link['uri'] ?>" class="join-item btn btn-sm <?= $link['active'] ? 'btn-active btn-primary' : '' ?>">
                <?= $link['title'] ?>
            </a>
        <?php endforeach ?>

        <?php if ($pager->hasNext()) : ?>
            <a href="<?= $pager->getNext() ?>" class="join-item btn btn-sm" aria-label="<?= lang('Pager.next') ?>">
                <span aria-hidden="true">&raquo;</span>
            </a>
            <a href="<?= $pager->getLast() ?>" class="join-item btn btn-sm" aria-label="<?= lang('Pager.last') ?>">
                <span aria-hidden="true"><?= lang('Pager.last') ?></span>
            </a>
        <?php endif ?>
    </div>
</nav>

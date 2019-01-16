<?= $this->render('partials/header') ?>

    <div id="add-entry-container">
    <?php if ($user) : ?>

        <a href="#" id="show-add-new-entry">Write an entry</a>

    <?php else: ?>

        <div id="info">
            <a href="/login">Log in</a> to write entries
        </div>

    <?php endif ?>
    </div>

    <div id="entries">

        <?php foreach ($entries as $entry) : ?>

            <?= $this->render('partials/entry', ['entry' => $entry]) ?>

        <?php endforeach ?>

        <?php if (!$entries) : ?>

            There's no entries here yet. Why not write one?

        <?php endif ?>

    </div>



<?= $this->render('partials/footer') ?>

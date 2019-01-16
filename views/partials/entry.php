
            <div class="entry level-<?= $entry['level'] ?? 0 ?>" id="entry-<?= $entry['id'] ?>">

            <?php if (is_null($entry['deleted'])) : ?>

                <div class="meta">
                    <span class="name"><?= $this->e($entry['name']) ?></span> @ <span class="date"><?= date('F j, Y H:i', strtotime($entry['created'])) ?></span>
                </div>

                <div class="body">
                    <?php if ($entry['reply_to'] ?? false) : ?>

                        <div class="reply">Replied to <?= $this->e($entry['name']) ?></div>

                    <?php endif ?>

                    <?= nl2br($this->e($entry['entry'])) ?>
                </div>


                <div class="actions">

                    <?php if ($user) : ?>

                        <a href="#" class="show-reply-entry" data-id="<?= $entry['id'] ?>"><span class="icon icon-reply"></span>Reply</a>

                    <?php endif ?>

                    <?php if ($entry['user_id'] == $user['id']) : ?>

                        <a href="#" class="show-edit-entry" data-id="<?= $entry['id'] ?>"><span class="icon icon-edit"></span>Edit</a>

                        <a href="#" class="delete-entry-btn" data-id="<?= $entry['id'] ?>" data-token="<?= $this->csrf('delete-entry-token' . $entry['id']) ?>"><span class="icon icon-delete"></span>Delete</a>

                    <?php endif ?>

                </div>

            <?php else: ?>

                <span class="deleted">The entry has been deleted</span>

            <?php endif ?>

            </div>

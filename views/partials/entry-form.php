
        <form method="post" action="<?= $this->e($action) ?>" id="entry-form">

            <input type="hidden" value="<?= $this->csrf('entry-form')?>" name="token" />
            <input type="hidden" value="<?= $this->e($id ?? 0) ?>" name="id" />
            <input type="hidden" value="<?= $this->e($parentId ?? 0) ?>" name="parent_id" />

            <div id="entry-error" class="error"></div>

            <div class="form-item">
                <label><?= $title ?></label>
                <textarea id="input-body" name="body"><?= $this->e($body ?? '')?></textarea>
            </div>

            <div class="form-item buttons">
                <button id="entry-form-btn">Save</button>
            </div>

        </form>

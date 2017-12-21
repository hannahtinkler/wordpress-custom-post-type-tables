<?php

if (empty($postTypes)) {
    return;
}

?>

<div class="wrap">
    <h1>Custom Post Type Tables</h1>

    <?php if (!empty($_GET['success'])) : ?>
        <div id="message" class="updated notice notice-success is-dismissible">
            <p>
                Custom tables have been updated.
            </p>
            <button type="button" class="notice-dismiss">
            <span class="screen-reader-text">Dismiss this notice.</span></button>
        </div>
    <?php endif ?>

    <form method="POST">
        <input type="hidden" name="cpt_tables:submitted" value="1" />
        <fieldset>
            <legend>Which custom post types would you like to enable custom tables for?</legend>

            <?php foreach ($postTypes as $postType) : ?>
                <p>
                    <label>
                        <input type="checkbox"
                            name="custom_post_type_tables_enable[]"
                            value="<?= $postType['slug'] ?>"
                            <?= in_array($postType['slug'], $enabled) ? 'checked' : '' ?>
                        />

                        <?= $postType['name'] ?>
                    </label>
                </p>
            <?php endforeach ?>

        </fieldset>
        <button class="button button-primary" type="submit">Save</button>
    </form>
</div>

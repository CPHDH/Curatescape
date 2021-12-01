<?php echo head(); ?>

<div id="content" role="main">
    <article class="page show contribution">
        <div id="primary">
            <h2 class="page_title"><?php echo __("Thank you for contributing!"); ?></h2>
            <p><?php echo __("Your contribution will show up in the archive once an administrator approves it. Meanwhile, feel free to %s or %s .", contribution_link_to_contribute(__('make another contribution')), "<a href='" . url('items/browse') . "'>" . __('browse the archive') . "</a>"); ?>
            </p>
            <?php if (get_option('contribution_simple') && !current_user()): ?>
            <p><?php echo __("If you would like to interact with the site further, you can use an account that is ready for you. Visit %s, and request a new password for the email you used", "<a href='" . url('users/forgot-password') . "'>" . __('this page') . "</a>"); ?>
                <?php endif; ?>
        </div>
    </article>
</div>
<?php echo foot(); ?>
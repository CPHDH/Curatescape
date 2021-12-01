<?php
$head = array('title' => __('Contribution Terms of Service'));
echo head($head);
?>
<div id="content" role="main">
    <article class="page show contribution">
        <div id="primary">
            <h2><?php echo $head['title']; ?></h2>
            <?php echo get_option('contribution_consent_text'); ?>
        </div>
    </article>
</div>
<?php echo foot(); ?>
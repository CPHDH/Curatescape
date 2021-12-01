<?php
$head = array('title' => __('Confirmation Error'));
echo head($head);
?>
<div id="content" role="main">
    <article class="page show guest-user">
        <h2 class="page_title"><?php echo $head['title']; ?></h2>
        <div id='primary'>
            <?php echo flash(); ?>
        </div>
    </article>
</div>
<?php echo foot(); ?>
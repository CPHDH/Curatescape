<?php
if (!$collection) {
    // We don't use the Collections on the front-end
    include_once($_SERVER["DOCUMENT_ROOT"] . "/themes/curatescape/error/404.php");
} else {
    $title=metadata($collection, array('Dublin Core','Title'));
    $bodyclass = 'collections show';
    $bodyid='collections';
    echo head(array('maptype'=>'none','title' => __('Collection').' | '.$title, 'bodyclass' => $bodyclass, 'bodyid' => $bodyid)); ?>

<div id="content" role="main">
    <article class="browse collection">
        <h2 class="title"><?php echo $title; ?></h2>
        <?php echo '<div class="byline">'.__('%1s %2s', metadata($collection, 'total_items'), rl_item_label('plural')).'</div>'; ?><br>

        <div id="primary" class="browse">

            <section id="text">
                <div id="collection-description">
                    <p><?php echo metadata($collection, array('Dublin Core','Description')); ?></p>

                    <?php if (metadata('collection', 'total_items') > 0): ?>

                    <?php echo link_to('items', 'browse', __("Browse %1s %2s in this collection.", metadata('collection', 'total_items'), rl_item_label('plural')), array('class'=>'button collection-items-browse'), array('collection'=>$collection->id)); ?>

                    <?php else: ?>

                    <br><br>
                    <p><?php echo __("This collection currently has no %s.", rl_item_label('plural')); ?></p>

                    <?php endif; ?>

                </div>

            </section>
        </div>
    </article>
</div> <!-- end content -->



<?php echo foot(); ?>

<?php
} ?>
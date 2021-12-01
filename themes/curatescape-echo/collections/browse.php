<?php
if (!$collections) {
    // We don't use the Collections on the front-end
    include_once($_SERVER["DOCUMENT_ROOT"] . "/themes/curatescape/error/404.php");
} else {
    $total=count($collections);
    $title=__('Browse Collections');
    $bodyclass = 'collections browse';
    $bodyid='collections';
    echo head(array('maptype'=>'none','title' => $title, 'bodyclass' => $bodyclass, 'bodyid' => $bodyid)); ?>
<div id="content" role="main">
    <article class="browse collection">
        <div class="browse-header">
            <h2 class="query-header"><?php echo $title.': '.$total; ?></h2>
            <nav class="collections-nav navigation secondary-nav">
                <?php echo rl_collection_browse_subnav(); ?>
            </nav>
            <div id="helper-links">
                <span class="helper-label"><?php echo rl_icon('funnel').'&nbsp;'.__("Sort by: "); ?>
                </span>
                <?php echo browse_sort_links(array('Title'=>'Dublin Core,Title','Date Added'=>'added')); ?>
            </div>
        </div>
        <div id="primary" class="browse">
            <section id="results">
                <?php foreach ($collections as $collection): ?>
                <article class="collection item-result">
                    <?php echo link_to($collection, 'show', '<h3 class="title">'.metadata($collection, array('Dublin Core','Title')).'</h3>', array('class'=>'permalink')) ?>
                    <?php echo '<div class="byline">'.rl_icon('folder').__('%1s %2s', metadata($collection, 'total_items'), rl_item_label('plural')).'</div>'; ?>
                </article>
                <?php endforeach; ?>
            </section>
        </div>
        <div class="pagination bottom"><?php echo pagination_links(); ?></div>
    </article>
</div> <!-- end content -->



<?php echo foot(); ?>

<?php
} ?>
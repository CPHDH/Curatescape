<?php
    if (!$_GET["sort_field"]) {
        $tags=get_records('Tag', array('sort_field' => 'count', 'sort_dir' => 'd'), 0);
    }
    echo head(array('maptype'=>'none', 'title'=>'Browse by Tag','bodyid'=>'items','bodyclass'=>'browse tags'));
    ?>


<div id="content" role="main">
    <article class="browse tags">
        <div class="browse-header">
            <h2 class="query-header"><?php echo __('Tags: %s', total_records('Tags'));?></h2>
            <nav class="secondary-nav" id="tag-browse">
                <?php rl_item_browse_subnav(); ?>
            </nav>

            <div id="helper-links">
                <span class="helper-label"><?php echo rl_icon('funnel').'&nbsp;'.__("Sort by: "); ?>
                </span>
                <?php echo browse_sort_links(array('Name'=>'name','Count'=>'count')); ?>
            </div>
        </div>
        <div id="primary" class="">
            <section id="tags" aria-label="<?php echo __('Tags');?>">
                <?php echo tag_cloud($tags, 'items/browse', 9, true, 'after'); ?>
            </section>
        </div><!-- end primary -->

    </article>
</div> <!-- end content -->


<?php echo foot(); ?>

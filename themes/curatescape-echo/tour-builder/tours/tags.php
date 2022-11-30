<?php
if (!isset($_GET["sort_field"])) {
    $tags=get_records('Tag', array('sort_field' => 'count', 'sort_dir' => 'd', 'type'=>'tour'), 0);
}
echo head(array('maptype'=>'none', 'title'=>__('%s Tags',rl_tour_label()),'bodyid'=>'tours','bodyclass'=>'browse tags'));
?>
<div id="content" role="main">
    <article class="browse tags">
        <div class="browse-header">
            <h2 class="query-header"><?php echo __('%1s Tags: %2s', rl_tour_label(), count($tags));?></h2>
            <nav class="secondary-nav" id="tag-browse">
                <?php rl_tour_browse_subnav(); ?>
            </nav>

            <div id="helper-links">
                <span class="helper-label"><?php echo rl_icon('funnel').'&nbsp;'.__("Sort by: "); ?>
                </span>
                <?php echo browse_sort_links(array(__('Name')=>'name',__('Count')=>'count')); ?>
            </div>
        </div>
        <div id="primary" class="">
            <section id="tags" aria-label="<?php echo __('Tags');?>">
                <?php echo tag_cloud($tags, 'tours/browse', 9, true, 'after'); ?>
            </section>
        </div><!-- end primary -->

    </article>
</div> <!-- end content -->
<?php echo foot(); ?>

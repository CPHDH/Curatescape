<?php
$title = __('Browse Exhibits by Tag');
echo head(array('maptype'=>'none','title' => $title, 'bodyid' => 'exhibit', 'bodyclass' => 'exhibits tags browse'));
?>


<div id="content" role="main">

    <article class="browse tags">
        <div class="browse-header">
            <h2 class="query-header"><?php
        $title .= ((isset($total_results)) ? ': <span class="item-number">'.$total_results.'</span>' : '');
        echo $title;
        ?></h2>
            <nav class="secondary-nav" id="item-browse">
                <?php echo nav(
            array(
                        array(
                            'label' => __('All'),
                            'uri' => url('exhibits/browse')
                        ),
                        array(
                            'label' => __('Featured'),
                            'uri' => url('exhibits/browse?featured=1')
                        ),
                        array(
                            'label' => __('Tags'),
                            'uri' => url('exhibits/tags')
                        )
                    )
        ); ?>
            </nav>

            <div id="helper-links">
                <span class="helper-label"><?php echo rl_icon('funnel').'&nbsp;'.__("Sort by: "); ?>
                </span>
                <?php echo browse_sort_links(array(__('Name')=>'name',__('Count')=>'count')); ?>
            </div>
        </div>

        <div id="primary" class="browse">
            <?php echo tag_cloud($tags, 'exhibits/browse'); ?>
        </div><!-- end primary -->



    </article>
</div> <!-- end content -->



<?php echo foot(); ?>
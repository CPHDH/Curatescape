<?php
$query = (isset($_GET['query']) ? htmlspecialchars($_GET['query']) : null);
$title = $query ? __('Search Results for "%s"', htmlspecialchars($query)) : __('Sitewide Search');
$bodyclass ='browse queryresults';
$maptype='none';
$scroll_to = ($query && $total_results) ? 'sort-links' : null;
echo head(array('maptype'=>$maptype,'title'=>$title,'bodyid'=>'search','bodyclass'=>$bodyclass));
?>

<div id="content" role="main" data-scrollto="<?php echo $scroll_to;?>">
    <article class="search browse">
        <div class="browse-header">
            <h2 class="query-header"><?php
            $title .= ($total_results ? ': <span class="item-number">'.$total_results.'</span>' : '');
            echo $title;
            ?></h2>
            <nav class="secondary-nav" id="item-browse">
                <?php echo rl_search_subnav();?>
            </nav>

            <div id="helper-links">
                <span><?php echo rl_icon('information-circle').'&nbsp;'.__('Use the form below to search for the selected record types. Or use advanced options to <a href="/items/search">Search %s</a>.', rl_item_label('plural'));?></span>
            </div>
        </div>

        <div id="primary" class="browse">
            <section id="results" aria-label="<?php echo __('Search Results');?>">

                <?php if ($total_results): ?>
                <?php echo search_form(array('show_advanced'=>true));?>
                <div id="sort-links" class="sub">
                    <span class="sort-label"><?php echo rl_icon('funnel').'&nbsp;'.__("Sort by: "); ?>
                    </span>
                    <?php echo browse_sort_links(array('Title'=>'title','Record Type'=>'record_type')); ?>
                </div>
                <?php echo rl_search_results(loop('search_texts'));?>

                <?php else: ?>
                <div id="no-results">
                    <p><?php echo ($query) ? '<em>'.__('Your query returned <strong>no results</strong>.').'</em>' : null;?></p>
                    <?php echo search_form(array('show_advanced'=>true));?>
                </div>
                <?php endif; ?>


                <div class="pagination bottom"><?php echo pagination_links(); ?></div>

            </section>
        </div><!-- end primary -->


    </article>
</div> <!-- end content -->



<?php echo foot(); ?>
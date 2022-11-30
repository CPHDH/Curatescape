<?php
$label=rl_tour_label('plural');
if (isset($_GET['tags'])) {
   $title = __('%1$s tagged "%2$s": %3$s', rl_tour_label('plural'), htmlspecialchars($_GET['tags']),'<span class="item-number">'.total_tours().'</span>');
   $title_facet = ' | '.htmlspecialchars($_GET['tags']);
   $scroll_to = 'content';
}elseif (isset($_GET['featured']) && $_GET['featured'] == 1) {
    $title = __('Featured %1$s: %2$s', $label, '<span class="item-number">'.total_tours().'</span>');
    $title_facet = ' | '.__('Featured');
    $scroll_to = null;
} else {
    $title = __('All %1$s: %2$s', $label, '<span class="item-number">'.total_tours().'</span>');
    $title_facet = null;
    $scroll_to = null;
}
$sort_field = (isset($_GET['sort_field']) ? htmlspecialchars($_GET['sort_field']) : null);
$sort_dir = (isset($_GET['sort_dir']) ? htmlspecialchars($_GET['sort_dir']) : null);
$sort=[$sort_field, $sort_dir];
if($tours){
    switch ($sort) {
        // added
        case $sort[0]=='id' && $sort[1]=='a':
        case $sort[0]=='id' && $sort[1]==null:
        case $sort[0]==null && $sort[1]==null:
           rl_sort_objects_array($tours, 'id', true); 
           break;
        // added reverse
        case $sort[0]=='id' && $sort[1]=='d':
           rl_sort_objects_array($tours, 'id', false); 
           break;
        // title
        case $sort[0]=='title' && $sort[1]=='a':
        case $sort[0]=='title' && $sort[1]==null:
           rl_sort_objects_array($tours, 'title', true); 
           break;
        // title reverse
        case $sort[0]=='title' && $sort[1]=='d':
           rl_sort_objects_array($tours, 'title', false); 
           break;
    }
}
echo head(
    array(
    'maptype'=>'none',
    'title' => $label.$title_facet,
    'bodyid'=>'tours',
    'bodyclass' => 'browse' )
);

?>
<div id="content" role="main" data-scrollto="<?php echo $scroll_to;?>">
    <article class="browse tour">
        <div class="browse-header">
            <h2 class="query-header"><?php echo $title;?></h2>
            <nav class="tours-nav navigation secondary-nav">
                <?php echo rl_tour_browse_subnav(); ?>
            </nav>
            <div id="helper-links">
                <span class="helper-label"><?php echo rl_icon('funnel').'&nbsp;'.__("Sort by: "); ?>
                </span>
                <?php echo rl_tours_browse_sort_links($sort); ?>
                &nbsp;&nbsp;
                <span class="helper-label"><?php echo rl_icon('information-circle').'&nbsp;'.(get_theme_option('tour_info') ? strip_tags(get_theme_option('tour_info'),'<a>') :  __("%s are self-guided.", rl_tour_label('plural'))); ?></span>
                
            </div>
        </div>
        <div id="primary" class="browse">
            <section id="results" aria-label="<?php echo rl_tour_label('plural');?>">
                <div id="browse-tours-container">
                    <?php
                    $html = null;
                    if (has_tours()) {
                        if (has_tours_for_loop()) {
                            foreach ($tours as $tour) {
                                set_current_record('tour', $tour);
                                $bg=array();
                                if ($touritems = $tour->getItems()) {
                                    foreach ($touritems as $ti) {
                                        if (count($bg) == 4) {
                                            break;
                                        }
                                        if ($src=rl_get_first_image_src($ti, 'square_thumbnails')) {
                                            $bg[]='url('.$src.')';
                                        }
                                    }
                                }
                                $html .= '<article class="item-result tour">';
                                $html .= '<a aria-label="'.tour('title').'" class="tour-image '.(count($bg) < 4 ? 'single' : 'multi').'" style="background-image:'.implode(',', $bg).'" href="'.WEB_ROOT.'/tours/show/'.tour('id').'"></a><div class="separator thin flush-bottom flush-top"></div>';
                                $html .= '<div class="tour-inner">';
                                $html .= '<a class="permalink" href="' . WEB_ROOT . '/tours/show/'. tour('id').'"><h3 class="title">' . tour('title').'</h3></a>'.
                                    '<span class="byline">'.rl_icon('compass').__('%s Locations', rl_tour_total_items($tour)).'</span>';
                                $html .= '<p class="tour-snip">'.snippet(strip_tags(htmlspecialchars_decode(tour('description'))), 0, 200).'<br><a class="readmore" href="'.WEB_ROOT . '/tours/show/'. tour('id').'">'.__('View %s', rl_tour_label('singular')).'</a></p>';
                                $html .= '</div>';
                                $html .= '</article>';
                        }
                    } else {
                        $html .= '<p>'.__('No tours are available. Publish some now.').'</p>';
                    }
                }
                echo $html;
                ?>
                </div>
            </section>
        </div>

        <div class="pagination bottom">
            <?php echo pagination_links(); ?>
        </div>

    </article>
</div> <!-- end content -->
<?php echo foot();?>

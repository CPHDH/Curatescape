<?php
$label=rl_tour_label('plural');
if (isset($_GET['featured']) && $_GET['featured'] == 1) {
    $title = __('Featured %1$s: %2$s', $label, total_tours());
} else {
    $title = __('All %1$s: %2$s', $label, total_tours());
}
echo head(
    array(
    'maptype'=>'none',
    'title' => $label,
    'bodyid'=>'tours',
    'bodyclass' => 'browse' )
);
?>
<div id="content" role="main">
    <article class="browse tour">
        <div class="browse-header">
            <h2 class="query-header"><?php echo $title;?></h2>
            <nav class="tours-nav navigation secondary-nav">
                <?php echo rl_tour_browse_subnav(rl_tour_label('plural'), null); ?>
            </nav>
            <div id="helper-links">
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
                            $html .= '<a class="tour-image '.(count($bg) < 4 ? 'single' : 'multi').'" style="background-image:'.implode(',', $bg).'" href="'.WEB_ROOT.'/tours/show/'.tour('id').'"></a><div class="separator thin flush-bottom flush-top"></div>';
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

<?php
$tag = (isset($_GET['tag']) ? htmlspecialchars($_GET['tag']) : null); // items --> browse
$tags = (isset($_GET['tags']) ? htmlspecialchars($_GET['tags']) : null); // tags/items --> show
$subj = ((isset($_GET['advanced'][0]['element_id']) && $_GET['advanced'][0]['element_id'] == 49) ? htmlspecialchars($_GET['advanced'][0]['terms']) : null);
$auth= ((isset($_GET['advanced'][0]['element_id']) && $_GET['advanced'][0]['element_id'] == 39) ? htmlspecialchars($_GET['advanced'][0]['terms']) : null);
$collection = (isset($_GET['collection']) ? htmlspecialchars($_GET['collection']) : null);
$query = (isset($_GET['search']) ? htmlspecialchars($_GET['search']) : null);
$other = (isset($_GET['advanced'][0]['element_id']) ? true : false);
$bodyclass='browse';
$maptype='focusarea';
$scroll_to = (($query || $tag || $tags || $subj || $auth || $collection || $other) && $total_results) ? 'content' : null;
if (($tag || $tags) && !($query)) {
    $the_tag=($tag ? $tag : $tags);
    $title = __('%1$s tagged "%2$s"', rl_item_label('plural'), $the_tag);
    $bodyclass .=' queryresults';
    $maptype='queryresults';
} elseif (!empty($auth)) {
    $title = __('%1$s by author %2$s', rl_item_label('plural'), $auth);
    $bodyclass .=' queryresults';
    $maptype='queryresults';
} elseif (!empty($subj)) {
    $title = __('Results for subject term "%s"', $subj);
    $bodyclass .=' queryresults';
    $maptype='queryresults';
} elseif (!empty($collection)) {
    $c=get_record_by_id('collection', $collection);
    $collection_title=metadata($c, array('Dublin Core','Title'));
    $title = __('%1s in "%2s"', rl_item_label('plural'), $collection_title);
    $bodyclass .=' queryresults';
    $maptype='queryresults';
} elseif (isset($_GET['featured']) && $_GET['featured'] == 1) {
    $title = __('Featured %s', rl_item_label('plural'));
    $bodyclass .=' queryresults';
    $maptype='queryresults';
} elseif ($query) {
    $title = __('Search Results for "%s"', $query);
    $bodyclass .=' queryresults';
    $maptype='queryresults';
} elseif ($other) {
    $title = __('Search Results');
    $bodyclass .=' queryresults';
    $maptype='queryresults';
} else {
    $title = __('All %s', rl_item_label('plural'));
    $bodyclass .=' items stories';
}
echo head(array('maptype'=>$maptype,'title'=>$title,'bodyid'=>'items','bodyclass'=>$bodyclass));
?>

<div id="content" role="main" data-scrollto="<?php echo $scroll_to;?>">

    <article class="browse stories items">
        <div class="browse-header">
            <?php echo rl_admin_message('items-browse', array('super','admin'));?>
            <h2 class="query-header"><?php
        $title .= ($total_results ? ': <span class="item-number">'.$total_results.'</span>' : '');
        echo $title;
        ?></h2>
            <nav class="secondary-nav" id="item-browse">
                <?php echo rl_item_browse_subnav();?>
            </nav>
            <div id="helper-links">
                <span class="helper-label"><?php echo rl_icon('funnel').'&nbsp;'.__("Sort by: "); ?>
                </span>
                <?php echo browse_sort_links(array('Title'=>'Dublin Core,Title','Date Added'=>'added')); ?>
            </div>
        </div>

        <div id="primary" class="browse">
            <section id="results" aria-label="<?php echo rl_item_label('plural');?>">

                <div class="browse-items <?php echo $total_results ? '' : 'empty';?>">
                    <?php
            foreach (loop('Items') as $item):
                $tags=tag_string(get_current_record('item'), url('items/browse'));
                $hasImage=metadata($item, 'has thumbnail');
                $location = get_db()->getTable('Location')->findLocationByItem($item, true);
                $has_location = (isset($location) && $location[ 'latitude' ] && $location[ 'longitude' ]) ? true : false;
                if ($item_image = rl_get_first_image_src($item)) {
                    $size=getimagesize($item_image);
                    $orientation = $size && ($size[0] > $size[1]) ? 'landscape' : 'portrait';
                } elseif ($hasImage && (!stripos($img, 'ionicons') && !stripos($img, 'fallback'))) {
                    $img = item_image('fullsize');
                    preg_match('/<img(.*)src(.*)=(.*)"(.*)"/U', $img, $result);
                    $item_image = array_pop($result);
                    $size=getimagesize($item_image);
                    $orientation = $size && ($size[0] > $size[1]) ? 'landscape' : 'portrait';
                }else{
                    $item_image=null;
                    $orientation=null;
                }
                ?>
                    <article class="item-result <?php echo $hasImage ? 'has-image' : 'no-image';?>">
                        <?php echo link_to_item('<span class="item-image '.$orientation.'" style="background-image:url('.$item_image.');" role="img" aria-label="Image: '.metadata($item, array('Dublin Core', 'Title')).'"></span>', array('title'=>metadata($item, array('Dublin Core','Title')),'class'=>'image-container')); ?>

                        <div class="result-details">
                            <?php echo rl_filed_under($item);?>
                            <?php echo rl_the_title_expanded($item); ?>
                            <?php echo rl_the_byline($item, false);?>
                            <?php echo link_to_item(__('View %s', rl_item_label('singular')),array('class'=>'readmore')).($has_location && $item->public ? ' <span class="sep-bar">|</span> <a role="button" data-id="'.$item->id.'" class="readmore showonmap" href="javascript:void(0)">'.__('Show on Map').'</a>' : null);?>
                        </div>

                    </article>
                    <?php endforeach; ?>

                    <?php if ($query && !$total_results) {?>
                    <div id="no-results">
                        <p><?php echo ($query) ? '<em>'.__('Your query returned <strong>no results.</strong>').'</em><br><span class="caption">'.rl_icon('information-circle').__('Try using <a href="%1$s">Advanced %2$s Search</a> or <a href="%3$s">Sitewide Search</a>.',url('items/search'),rl_item_label(),url('search')).'</span>' : null;?></p>
                    </div>
                    <?php }else{ ?>
                    <article class="item-result" style="visibility:hidden"></article>
                    <article class="item-result" style="visibility:hidden"></article>
                    <?php } ?>

                </div>
            </section>

            <div class="pagination bottom"><?php echo pagination_links(); ?></div>

        </div><!-- end primary -->
            
        <?php echo $total_results ? multimap_markup(false,$title) : null;?>
            
    </article>
</div> <!-- end content -->


<?php echo foot(); ?>
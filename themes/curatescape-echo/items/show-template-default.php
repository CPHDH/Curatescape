<?php
// SETUP
$tour = isset($_GET['tour']) ? (int)htmlspecialchars($_GET['tour']) : null;
$tour_index = isset($_GET['index']) ? (int)htmlspecialchars($_GET['index']) : null;
$maptype = 'story';
$filesforitem = rl_item_files_by_type($item);
if (isset($filesforitem['images'][0]['src'])) {
    $hero_img = WEB_ROOT.'/files/fullsize/'.$filesforitem['images'][0]['src'];
    $hero_caption = $filesforitem['images'][0]['caption'];
    $hero_orientation = $filesforitem['images'][0]['orientation'];
    $hero_class = "has-image";
    $has_image_count = count($filesforitem['images']);
} else {
    $hero_img = '';
    $hero_orientation = '';
    $hero_caption = '';
    $hero_class = "no-image";
    $has_image_count = 0;
}
$has_audio_count = isset($filesforitem['audio'][0]) ? count($filesforitem['audio']) : 0;
$has_video_count = isset($filesforitem['video'][0]) ? count($filesforitem['video']) : 0;
$has_other_count = isset($filesforitem['other'][0]) ? count($filesforitem['other']) : 0;
$the_lede = rl_the_lede();
$location = get_db()->getTable('Location')->findLocationByItem($item, true);
$address = (element_exists('Item Type Metadata', 'Street Address'))
? metadata($item, array( 'Item Type Metadata','Street Address' ))
: null;
$has_location = (isset($location) && $location[ 'latitude' ] && $location[ 'longitude' ]) ? true : false;
echo head(array(
    'item'=>$item,
    'maptype'=>$maptype,
    'bodyid'=>'items',
    'bodyclass'=>'show item-story item-'.$item->id.($tour ? ' tour-'.$tour : null).(isset($tour_index) ? ' tour-index-'.$tour_index : null),
    'title' => metadata($item, array('Dublin Core', 'Title'))
    ));
?>

<article class="story item show wide <?php echo $hero_class;?>" role="main" id="content">
    <header id="story-header">
        <div class="background-image <?php echo $hero_orientation;?>" style="background-image:url(<?php echo $hero_img;?>)"></div>
        <div class="background-gradient"></div>
        <div class="title-card inner-padding max-content-width">
            <div class="title-card-main">
                <?php echo rl_filed_under($item);?>
                <div class="separator wide thin flush-top"></div>
                <?php echo rl_the_title();?>
                <?php echo rl_the_subtitle();?>
                <div class="separator"></div>
                <?php echo rl_the_byline($item, true);?>
                <?php echo rl_post_date_header();?>
            </div>
            <div class="title-card-image">
                <?php echo isset($filesforitem['images'][0]) ? rl_gallery_figure($filesforitem['images'][0], 'featured', '#images') : null;?>
            </div>
        </div>
    </header>
    <aside id="social-actions" class="max-content-width inner-padding-flush">
        <?php echo rl_story_actions('transparent-on-light', rl_seo_pagetitle(metadata($item, array('Dublin Core', 'Title')), $item), $item->id);?>
    </aside>
    <div class="separator wide thin"></div>
    <div class="story-columns inner-padding max-content-width">
        <div class="column">
            <div class="sticky">
                <?php echo rl_story_nav($has_image_count, $has_audio_count, $has_video_count, $has_other_count, $has_location, $tour, $tour_index);?>
            </div>
        </div>
        <div class="column">

            <section aria-label="<?php echo __('Main Text');?>" id="text-section" data-toc="#text-section">
                <?php echo $the_lede ? $the_lede.'<div class="separator flush-top"></div>' : null; ?>
                <?php echo rl_the_text(); ?>
                <?php echo rl_factoid();?>
            </section>
            <div class="separator"></div>

            <?php if (metadata($item, 'has files')):?>
            <section aria-label="<?php echo __('Media Files');?>" id="media-section">

                <?php if ($has_video_count):?>
                <div id="video" data-toc="#video">
                    <h2><?php echo __('Video');?></h2>
                    <?php rl_streaming_files($filesforitem['video'], 'video');?>
                </div>
                <?php endif;?>

                <?php if ($has_audio_count):?>
                <div id="audio" data-toc="#audio">
                    <h2><?php echo __('Audio');?></h2>
                    <?php rl_streaming_files($filesforitem['audio'], 'audio');?>
                </div>
                <?php endif;?>

                <?php if ($has_image_count):?>
                <div itemscope itemtype="http://schema.org/ImageGallery" id="images" data-toc="#images" data-pswp="<?php echo src('photoswipe.min.js', 'javascripts/pswp');?>" data-pswp-ui="<?php echo src('photoswipe-ui-default.min.js', 'javascripts/pswp');?>" data-pswp-css="<?php echo src('photoswipe.css', 'javascripts/pswp');?>" data-pswp-skin-css="<?php echo src('default-skin.css', 'javascripts/pswp/default-skin');?>">
                    <h2><?php echo __('Images');?></h2>

                    <?php foreach ($filesforitem['images'] as $image) {
                        echo rl_gallery_figure($image, 'border');
                    }?>
                </div>
                <?php endif;?>

                <?php if ($has_other_count):?>
                <div id="documents" data-toc="#documents">
                    <h2><?php echo __('Documents');?></h2>
                    <?php echo rl_document_files($filesforitem['other']);?>
                </div>
                <?php endif;?>
            </section>
            <div class="separator"></div>
            <?php endif;?>

            <?php if ($has_location && plugin_is_active('Geolocation')): ?>
            <section id="map-section" data-toc="#map-section">
                <h2><?php echo __('Location');?></h2>
                <?php echo rl_story_map_single(rl_the_title(), $location, $address, $hero_img, $hero_orientation);?>
            </section>
            <div class="separator"></div>
            <?php endif;?>

            <section aria-label="<?php echo __('Metadata');?>" id="metadata-section" data-toc="#metadata-section">
                <h2><?php echo __('Metadata');?></h2>
                <?php if ($res = rl_meta_style(__('Related Resources'), array(rl_related_links()))) {
                    echo $res;
                }?>
                <?php if ($website = rl_meta_style(__('Official Website'), array(rl_official_website()))) {
                    echo $website;
                }?>
                <?php if ($cite = rl_meta_style(__('Citation Info'), array(rl_item_citation(),rl_post_date()))) {
                    echo $cite;
                };?>
                <?php if ($tours = rl_tours_for_item($item->id)) {
                    echo rl_meta_style(__('Related Tours'), array($tours));
                }?>
                <?php if ($cats = rl_meta_style(__('Filed Under'), array(rl_collection($item),rl_subjects(),rl_tags($item)))) {
                    echo $cats;
                }?>
            </section>

        </div>
    </div>

    <?php if (get_theme_option('comments_id')):?>
    <section aria-label="<?php echo __('Comments');?>" id="comments-section">
        <?php echo rl_display_comments();?>
    </section>
    <?php endif;?>
</article>

<?php echo foot(); ?>
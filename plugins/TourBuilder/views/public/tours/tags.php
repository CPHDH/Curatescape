<?php
if (!isset($_GET["sort_field"])) {
    $tags=get_records('Tag', 
        array('sort_field' => 'count', 'sort_dir' => 'd', 'type'=>'tour'), 0);
}
echo head(array('maptype'=>'none', 
    'title'=>__('Browse by Tag'),
    'bodyid'=>'tours',
    'bodyclass'=>'browse tags'));
?>
<h1><?php echo __('Browse Tags (%s total)', count($tags));?></h1>

<nav class="secondary-nav" id="tag-browse"> 
    <?php echo public_nav_tours(); ?>
</nav>

<section class="results" id="tags" aria-label="<?php echo __('Tags');?>">
    <?php echo tag_cloud($tags, 'tours/browse', 9); ?>
</section>

<?php echo foot(); ?>

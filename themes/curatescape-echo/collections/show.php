<?php
$title=metadata($collection, array('Dublin Core','Title'));
$bodyclass = 'collections show';
$bodyid='collections';
if($collection && ($file=$collection->getFile())){
    $hero_img = $file->getProperty('fullsize_uri');
    $image = array();
    $src=str_ireplace(array('.JPG','.jpeg','.JPEG','.png','.PNG','.gif','.GIF', '.bmp','.BMP'), '.jpg', $file->filename);
       $size=getimagesize(WEB_ROOT.'/files/fullsize/'.$src);
       $orientation = $size[0] > $size[1] ? 'landscape' : 'portrait';
       array_push(
          $image,
          array(
          'title'=>metadata($file, array('Dublin Core','Title')),
          'id'=>$file->id,
          'src'=>$src,
          'size'=>array($size[0],$size[1]),
          'orientation'=>$orientation,
          'caption'=>'<span class="file-title" itemprop="name">'.link_to('items', 'browse', __("Browse %1s %2s in this collection.", metadata('collection', 'total_items'), rl_item_label('plural')), array('itemprop'=>'contentUrl','class'=>''), array('collection'=>$collection->id)).'</span>',
          )
       );
}else{
    $hero_img = null;
}
echo head(array('maptype'=>'none','title' => __('Collection').' | '.$title, 'bodyclass' => $bodyclass, 'bodyid' => $bodyid)); 
?>

<div class="background-image portrait" style="background-image:url(<?php echo $hero_img;?>)"></div>
<div class="background-gradient"></div>

<article class="browse collection" id="content" role="main">

    <h2 class="title"><?php echo $title; ?></h2>
    <?php echo '<div class="byline">'.rl_icon('folder').'&nbsp;'.__('%1s %2s', metadata($collection, 'total_items'), rl_item_label('plural')).'</div>'; ?><br>

    <div id="primary" class="browse">
        <section id="text">
            <div id="collection-description">
                <p><?php echo ($description = metadata($collection, array('Dublin Core','Description'))) ? $description : __('This collection does not have a description.'); ?></p>

                <?php if (metadata('collection', 'total_items') > 0): ?>

                <?php echo link_to('items', 'browse', __("Browse %1s %2s in this collection.", metadata('collection', 'total_items'), rl_item_label('plural')), array('class'=>'button collection-items-browse'), array('collection'=>$collection->id)); ?>

                <?php else: ?>

                <br><br>
                <p><?php echo __("This collection currently has no %s.", rl_item_label('plural')); ?></p>

                <?php endif; ?>
            </div>
        </section>
        <?php echo isset($image[0]) ? rl_gallery_figure($image[0], 'featured', true) : null;?>
    </div>
</article>

<?php echo foot(); ?>